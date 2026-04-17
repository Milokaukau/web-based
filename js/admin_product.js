// ── Table Sort ──────────────────────────────────────────────
function initTableSort() {
    const table = document.querySelector('table');
    if (!table) return;

    const headers = table.querySelectorAll('th.sortable');
    let sortCol = -1, sortDir = 1;

    headers.forEach(th => {
        th.addEventListener('click', () => {
            const col   = parseInt(th.dataset.col);
            const isNum = th.dataset.type === 'num';

            sortDir = (sortCol === col) ? sortDir * -1 : 1;
            sortCol = col;

            headers.forEach(h => h.classList.remove('asc', 'desc'));
            th.classList.add(sortDir === 1 ? 'asc' : 'desc');

            const tbody = table.querySelector('tbody');

            const activeRows  = Array.from(tbody.querySelectorAll('tr[data-group="active"]'));
            const deletedRows = Array.from(tbody.querySelectorAll('tr[data-group="deleted"]'));

            const sortFn = (a, b) => {
                const aVal = a.cells[col].textContent.trim();
                const bVal = b.cells[col].textContent.trim();
                return isNum
                    ? (parseFloat(aVal) - parseFloat(bVal)) * sortDir
                    : aVal.localeCompare(bVal) * sortDir;
            };

            activeRows.sort(sortFn).forEach(r => tbody.appendChild(r));
            deletedRows.sort(sortFn).forEach(r => tbody.appendChild(r));

            applyPagination();
        });
    });
}

// ── Photo Preview ───────────────────────────────────────────
function initPhotoPreview(defaultSrc = '/images/photo.jpg') {
    const input   = document.querySelector('input[name="photo"]');
    const preview = document.getElementById('preview');
    if (!input || !preview) return;

    input.addEventListener('change', function () {
        if (this.files && this.files[0]) {
            preview.src = URL.createObjectURL(this.files[0]);
        }
    });

    const resetBtn = document.querySelector('button[type="reset"]');
    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            preview.src = defaultSrc;
        });
    }
}

// ── Pagination ──────────────────────────────────────────────
const PAGE_SIZE = 10;
let currentActivePage  = 1;
let currentDeletedPage = 1;

function renderPagination(visibleActive) {
    const totalActivePages = Math.ceil(visibleActive.length / PAGE_SIZE) || 1;

    if (currentActivePage > totalActivePages) currentActivePage = totalActivePages;

    const container = document.getElementById('pagination');
    if (!container) return;
    container.innerHTML = '';

    const prevA = document.createElement('button');
    prevA.textContent = '‹';
    prevA.disabled = currentActivePage === 1;
    prevA.onclick = () => { currentActivePage--; applyPagination(); };
    container.appendChild(prevA);

    for (let i = 1; i <= totalActivePages; i++) {
        const btn = document.createElement('button');
        btn.textContent = i;
        if (i === currentActivePage) btn.classList.add('active');
        btn.onclick = (function(p) { return () => { currentActivePage = p; applyPagination(); }; })(i);
        container.appendChild(btn);
    }

    const nextA = document.createElement('button');
    nextA.textContent = '›';
    nextA.disabled = currentActivePage === totalActivePages;
    nextA.onclick = () => { currentActivePage++; applyPagination(); };
    container.appendChild(nextA);

    const info = document.getElementById('pagination-info');
    if (info) {
        const aStart = visibleActive.length === 0 ? 0 : (currentActivePage - 1) * PAGE_SIZE + 1;
        const aEnd   = Math.min(currentActivePage * PAGE_SIZE, visibleActive.length);
        info.textContent = `${aStart}–${aEnd} of ${visibleActive.length} record(s)`;
    }
}

function applyPagination() {
    const oosVisible = !document.getElementById('deleted-tbody').hidden;

    const activeRows   = Array.from(document.querySelectorAll('#active-tbody tr'));
    const deletedRows  = Array.from(document.querySelectorAll('#deleted-tbody tr'));

    const visibleActive  = activeRows.filter(tr  => tr.dataset.filtered !== 'true');
    const visibleDeleted = deletedRows.filter(tr => tr.dataset.filtered !== 'true');

    if (oosVisible) {
        activeRows.forEach(tr => tr.style.display = 'none');

        deletedRows.forEach(tr => tr.style.display = 'none');
        visibleDeleted.forEach((tr, idx) => {
            const start = (currentDeletedPage - 1) * PAGE_SIZE;
            const end   =  currentDeletedPage      * PAGE_SIZE;
            tr.style.display = (idx >= start && idx < end) ? '' : 'none';
        });

        renderPagination(visibleDeleted);   

        const sub = document.querySelector('.page-header .sub');
        if (sub) sub.textContent = visibleDeleted.length + ' record(s) found';

    } else {
        activeRows.forEach(tr => tr.style.display = 'none');
        visibleActive.forEach((tr, idx) => {
            const start = (currentActivePage - 1) * PAGE_SIZE;
            const end   =  currentActivePage      * PAGE_SIZE;
            tr.style.display = (idx >= start && idx < end) ? '' : 'none';
        });

        renderPagination(visibleActive);

        const sub = document.querySelector('.page-header .sub');
        if (sub) sub.textContent = visibleActive.length + ' record(s) found';
    }
}

// ── Filters ─────────────────────────────────────────────────
function applyFilters() {
    const q        = (document.getElementById('product-search')?.value || '').toLowerCase().trim();
    const catVal   =  document.getElementById('filter-category')?.value  || '';
    const minVal   =  document.getElementById('filter-price-min')?.value;
    const maxVal   =  document.getElementById('filter-price-max')?.value;
    const minPrice = minVal !== '' ? parseFloat(minVal) : null;
    const maxPrice = maxVal !== '' ? parseFloat(maxVal) : null;

    document.querySelectorAll('#active-tbody tr, #deleted-tbody tr').forEach(row => {
        const name     = row.cells[4]?.textContent.toLowerCase()  || '';
        const desc     = row.cells[5]?.textContent.toLowerCase()  || '';
        const material = row.cells[9]?.textContent.toLowerCase()  || '';
        const id       = row.cells[1]?.textContent.toLowerCase()  || '';
        const catId    = row.cells[3]?.textContent.trim()         || '';
        const price    = parseFloat(row.cells[10]?.textContent.replace(/,/g, '') || '0');

        const matchSearch = !q       || name.includes(q) || desc.includes(q) || material.includes(q) || id.includes(q);
        const matchCat    = !catVal  || catId === catVal;
        const matchMin    = minPrice === null || price >= minPrice;
        const matchMax    = maxPrice === null || price <= maxPrice;

        row.dataset.filtered = (matchSearch && matchCat && matchMin && matchMax) ? 'false' : 'true';
    });

    currentActivePage  = 1;
    currentDeletedPage = 1;
    applyPagination();

    // photo mode
    currentPhotoPage = 1;
    const photoGrid = document.getElementById('photo-grid');
    if (photoGrid && !photoGrid.hidden) {
        applyPhotoPagination();
    }
}

function initFilters() {
    document.getElementById('product-search')?.addEventListener('input',  applyFilters);
    document.getElementById('filter-category')?.addEventListener('change', applyFilters);
    document.getElementById('filter-price-min')?.addEventListener('input', applyFilters);
    document.getElementById('filter-price-max')?.addEventListener('input', applyFilters);

    document.getElementById('filter-reset')?.addEventListener('click', () => {
        document.getElementById('product-search').value   = '';
        document.getElementById('filter-category').value  = '';
        document.getElementById('filter-price-min').value = '';
        document.getElementById('filter-price-max').value = '';
        applyFilters();
    });
}

// ── OOS Toggle ───────────────────────────────────────────────
function initOosToggle() {
    const btn         = document.getElementById('toggle-oos');
    const deletedTbody = document.getElementById('deleted-tbody');
    const activeTbody  = document.getElementById('active-tbody');
    if (!btn || !deletedTbody) return;

    let showing = false;

    btn.addEventListener('click', () => {
        showing = !showing;
        deletedTbody.hidden = !showing;
        if (activeTbody) activeTbody.hidden = showing;
        btn.classList.toggle('active', showing);
        applyPagination();
    });
}

// ── Photo View State ─────────────────────────────────────────
let currentPhotoPage = 1;
const PHOTO_PAGE_SIZE = 10;
let photoOosShowing = false;

function getPhotoCards() {
    const q        = (document.getElementById('product-search')?.value || '').toLowerCase().trim();
    const catVal   =  document.getElementById('filter-category')?.value  || '';
    const minVal   =  document.getElementById('filter-price-min')?.value;
    const maxVal   =  document.getElementById('filter-price-max')?.value;
    const minPrice = minVal !== '' ? parseFloat(minVal) : null;
    const maxPrice = maxVal !== '' ? parseFloat(maxVal) : null;

    const allCards = Array.from(document.querySelectorAll('#photo-grid .photo-card'));

    return allCards.filter(card => {
        const group    = card.dataset.group;  // 'active' or 'oos'
        const name     = (card.dataset.name     || '').toLowerCase();
        const material = (card.dataset.material || '').toLowerCase();
        const id       = (card.dataset.id       || '').toLowerCase();
        const catId    =  card.dataset.catid    || '';
        const price    = parseFloat(card.dataset.price || '0');

        if (!photoOosShowing && group === 'oos')    return false;
        if ( photoOosShowing && group === 'active') return false;

        const matchSearch = !q      || name.includes(q) || material.includes(q) || id.includes(q);
        const matchCat    = !catVal || catId === catVal;
        const matchMin    = minPrice === null || price >= minPrice;
        const matchMax    = maxPrice === null || price <= maxPrice;

        return matchSearch && matchCat && matchMin && matchMax;
    });
}

function applyPhotoPagination() {
    const allCards = Array.from(document.querySelectorAll('#photo-grid .photo-card'));
    const visible  = getPhotoCards();

    const totalPages = Math.ceil(visible.length / PHOTO_PAGE_SIZE) || 1;
    if (currentPhotoPage > totalPages) currentPhotoPage = totalPages;

    const start = (currentPhotoPage - 1) * PHOTO_PAGE_SIZE;
    const end   =  currentPhotoPage      * PHOTO_PAGE_SIZE;

    allCards.forEach(card => card.style.display = 'none');
    visible.forEach((card, idx) => {
        card.style.display = (idx >= start && idx < end) ? '' : 'none';
    });

    renderPhotoPagination(visible.length, totalPages);

    const sub = document.querySelector('.page-header .sub');
    if (sub) sub.textContent = visible.length + ' record(s) found';
}

function renderPhotoPagination(total, totalPages) {
    const container = document.getElementById('photo-pagination');
    const info      = document.getElementById('photo-pagination-info');
    if (!container) return;

    container.innerHTML = '';

    const prev = document.createElement('button');
    prev.textContent = '‹';
    prev.disabled = currentPhotoPage === 1;
    prev.onclick = () => { currentPhotoPage--; applyPhotoPagination(); };
    container.appendChild(prev);

    for (let i = 1; i <= totalPages; i++) {
        const btn = document.createElement('button');
        btn.textContent = i;
        if (i === currentPhotoPage) btn.classList.add('active');
        btn.onclick = (function(p) { return () => { currentPhotoPage = p; applyPhotoPagination(); }; })(i);
        container.appendChild(btn);
    }

    const next = document.createElement('button');
    next.textContent = '›';
    next.disabled = currentPhotoPage === totalPages;
    next.onclick = () => { currentPhotoPage++; applyPhotoPagination(); };
    container.appendChild(next);

    if (info) {
        const s = total === 0 ? 0 : (currentPhotoPage - 1) * PHOTO_PAGE_SIZE + 1;
        const e = Math.min(currentPhotoPage * PHOTO_PAGE_SIZE, total);
        info.textContent = `${s}–${e} of ${total} record(s)`;
    }
}

// ── View Toggle (Table ↔ Photo) ──────────────────────────────
function initViewToggle() {
    const btn       = document.getElementById('toggle-view');
    const tblWrap   = document.querySelector('.tbl-wrap');
    const pagWrap   = document.getElementById('table-pagination-wrap'); // ← 直接用 id
    const photoGrid = document.getElementById('photo-grid');
    const photoPag  = document.getElementById('photo-pagination-wrap');
    if (!btn || !photoGrid) return;

    photoGrid.hidden = true;
    if (photoPag) photoPag.hidden = true;
    tblWrap.hidden  = false;
    pagWrap.hidden  = false;

    let photoMode = false;

    btn.addEventListener('click', () => {
        photoMode = !photoMode;
        btn.classList.toggle('active', photoMode);
        btn.textContent = photoMode ? '☰ Table View' : '⊞ Photo View';

        tblWrap.hidden   = photoMode;
        pagWrap.hidden   = photoMode;
        photoGrid.hidden = !photoMode;
        if (photoPag) photoPag.hidden = !photoMode;

        if (photoMode) {
            currentPhotoPage = 1;
            applyPhotoPagination();
        } else {
            applyPagination(); 
        }
    });
}

// ── Photo OOS Toggle ─────────────────────────────────────────
function initPhotoOosToggle() {
    const btn = document.getElementById('toggle-oos');
    if (!btn) return;

    const deletedTbody = document.getElementById('deleted-tbody');
    const activeTbody  = document.getElementById('active-tbody');
    const photoGrid    = document.getElementById('photo-grid');
    const pagWrap = document.getElementById('table-pagination-wrap');

    btn.addEventListener('click', () => {
        photoOosShowing = !photoOosShowing;

        // table mode
        if (deletedTbody) deletedTbody.hidden = !photoOosShowing;
        if (activeTbody)  activeTbody.hidden  =  photoOosShowing;
        btn.classList.toggle('active', photoOosShowing);

        // photo mode 
        if (photoGrid && !photoGrid.hidden) {
            currentPhotoPage = 1;
            applyPhotoPagination();
        } else {
            // table mode pagination
            currentActivePage  = 1;
            currentDeletedPage = 1;
            applyPagination();
        }
    });
}

// ── Init ─────────────────────────────────────────────────────
initTableSort();
initPhotoPreview();
initFilters();
initPhotoOosToggle();   
initViewToggle();       
applyFilters();