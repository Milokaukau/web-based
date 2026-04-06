
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

// Auto-init 
initTableSort();
initPhotoPreview();
;