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
            Array.from(tbody.querySelectorAll('tr'))
                .sort((a, b) => {
                    const aVal = a.cells[col].textContent.trim();
                    const bVal = b.cells[col].textContent.trim();
                    return isNum
                        ? (parseFloat(aVal) - parseFloat(bVal)) * sortDir
                        : aVal.localeCompare(bVal) * sortDir;
                })
                .forEach(r => tbody.appendChild(r));

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
let currentPage = 1;  

function renderPagination(visibleActive, visibleDeleted) {
    const totalItems   = visibleActive.length + visibleDeleted.length;
    const totalPages   = Math.ceil(totalItems / PAGE_SIZE) || 1;

    if (currentPage > totalPages) currentPage = totalPages;

    const container = document.getElementById('pagination');
    if (!container) return;
    container.innerHTML = '';

    // ‹ Prev
    const prev = document.createElement('button');
    prev.textContent = '‹';
    prev.disabled = currentPage === 1;
    prev.onclick = () => { currentPage--; applyPagination(); };
    container.appendChild(prev);

   
// Active 页码（蓝色）
    const activePages = Math.ceil(visibleActive.length / PAGE_SIZE);
    for (let i = 1; i <= activePages; i++) {
        const btn = document.createElement('button');
        btn.textContent = i;
        if (i === currentPage) btn.classList.add('active');
        btn.onclick = (function(page) { return () => { currentPage = page; applyPagination(); }; })(i);
        container.appendChild(btn);
    }

    // 分隔线（只要两边都有数据就显示）
    if (visibleActive.length > 0 && visibleDeleted.length > 0) {
        const divider = document.createElement('span');
        divider.textContent = '|';
        divider.className = 'pagination-divider';
        container.appendChild(divider);
    }

    // Deleted 页码（红色），页码接续 active
    const deletedPages = Math.ceil(visibleDeleted.length / PAGE_SIZE);
    for (let i = 1; i <= deletedPages; i++) {
        const globalPage = activePages + i;
        const btn = document.createElement('button');
        btn.textContent = globalPage;
        btn.classList.add('btn-deleted');
        if (globalPage === currentPage) btn.classList.add('active');
        btn.onclick = (function(page) { return () => { currentPage = page; applyPagination(); }; })(globalPage);
        container.appendChild(btn);
    }

    // › Next
    const next = document.createElement('button');
    next.textContent = '›';
    next.disabled = currentPage === totalPages;
    next.onclick = () => { currentPage++; applyPagination(); };
    container.appendChild(next);

    // Info
    const info = document.getElementById('pagination-info');
    if (info) {
        const start = totalItems === 0 ? 0 : (currentPage - 1) * PAGE_SIZE + 1;
        const end   = Math.min(currentPage * PAGE_SIZE, totalItems);
        info.textContent = `Showing ${start}–${end} of ${totalItems} record(s) (${visibleActive.length} active, ${visibleDeleted.length} deleted/out of stock)`;
    }
}

function applyPagination() {
    const allRows = Array.from(document.querySelectorAll('#product-tbody tr'));

    const visibleActive  = allRows.filter(tr => tr.dataset.group === 'active'  && tr.dataset.filtered !== 'true');
    const visibleDeleted = allRows.filter(tr => tr.dataset.group === 'deleted' && tr.dataset.filtered !== 'true');

    const allVisible = [...visibleActive, ...visibleDeleted];

    allRows.forEach(tr => { tr.style.display = 'none'; });

    const start = (currentPage - 1) * PAGE_SIZE;
    const end   = currentPage * PAGE_SIZE;
    allVisible.forEach((tr, idx) => {
        tr.style.display = (idx >= start && idx < end) ? '' : 'none';
    });

    renderPagination(visibleActive, visibleDeleted);

    const sub = document.querySelector('.page-header .sub');
    if (sub) sub.textContent = allVisible.length + ' record(s) found';
}

// ── Filters ─────────────────────────────────────────────────
function applyFilters() {
    const q        = (document.getElementById('product-search')?.value || '').toLowerCase().trim();
    const catVal   =  document.getElementById('filter-category')?.value  || '';
    const minVal   =  document.getElementById('filter-price-min')?.value;
    const maxVal   =  document.getElementById('filter-price-max')?.value;
    const minPrice = (minVal !== '' && minVal != null) ? parseFloat(minVal) : null;
    const maxPrice = (maxVal !== '' && maxVal != null) ? parseFloat(maxVal) : null;

    document.querySelectorAll('#product-tbody tr').forEach(row => {
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

    currentPage = 1;
    applyPagination();
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

// ── Init ─────────────────────────────────────────────────────
initTableSort();
initPhotoPreview();
initFilters();
applyFilters();