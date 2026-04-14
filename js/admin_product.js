
// Table Sort (product_maintenance.php) 
function initTableSort() {
    const table = document.querySelector('table');
    if (!table) return;

    const headers = table.querySelectorAll('th.sortable');
    let sortCol = -1, sortDir = 1;

    headers.forEach(th => {
        th.addEventListener('click', () => {
            const col = parseInt(th.dataset.col);
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
        });
    });
}

// Photo Preview (insert + update)
function initPhotoPreview(defaultSrc = '/images/photo.jpg') {
    const input   = document.querySelector('input[name="photo"]');
    const preview = document.getElementById('preview');
    if (!input || !preview) return;

    input.addEventListener('change', function () {
        if (this.files && this.files[0]) {
            preview.src = URL.createObjectURL(this.files[0]);
        }
    });

    // Reset button (insert page only)
    const resetBtn = document.querySelector('button[type="reset"]');
    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            preview.src = defaultSrc;
        });
    }
}

// Search filter
const searchInput = document.getElementById('product-search');

searchInput.addEventListener('input', function () {
    const q = this.value.toLowerCase().trim();
    const rows = document.querySelectorAll('tbody tr');

    rows.forEach(row => {
        // Search across: name (col 4), description (col 5), material (col 9)
        const name     = row.cells[4]?.textContent.toLowerCase() || '';
        const desc     = row.cells[5]?.textContent.toLowerCase() || '';
        const material = row.cells[9]?.textContent.toLowerCase() || '';
        const id       = row.cells[1]?.textContent.toLowerCase() || '';

        const match = !q || name.includes(q) || desc.includes(q) || material.includes(q) || id.includes(q);
        row.style.display = match ? '' : 'none';
    });

    // Update record count
    const visible = [...rows].filter(r => r.style.display !== 'none').length;
    document.querySelector('.page-header .sub').textContent = visible + ' record(s) found';
});

// Auto-init 
initTableSort();
initPhotoPreview();
;