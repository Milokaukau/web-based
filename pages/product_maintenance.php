<?php
include '../database/product_base.php';

$arr = $_db->query('
    SELECT p.*, c.name AS color_name, cat.name AS category_name
    FROM tb_product p
    JOIN tb_color c      ON p.color_id    = c.id
    JOIN tb_category cat ON p.category_id = cat.id
')->fetchAll();

$_title = 'Product | Index';
include '../components/header.php';
?>


<div class="admin-shell">

    <!-- Top Bar -->
    <div class="top-bar">
        <a class="logo" href="../pages/product_maintenance.php"> Admin Panel</a>
        <div class="user-info">
            <span>admin@example.com</span>
            <div class="avatar">A</div>
        </div>
    </div>

    <!-- Body -->
    <div class="admin-body">

        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="nav-section">Catalogue</div>
            <a class="nav-item active" href="../pages/product_maintenance.php">
                <span class="nav-icon">&#128230;</span> Product
            </a>

            <div class="nav-section">Sales</div>
            <a class="nav-item" href="../pages/order.php">
                <span class="nav-icon">&#128203;</span> Order
            </a>
            <a class="nav-item" href="../logic/member.php">
                <span class="nav-icon">&#128101;</span> Member
            </a>

        </nav>

        <!-- Main -->
        <main class="main-content">
            <div class="page-header">
                <div>
                    <h1>Product</h1>
                    <p class="sub"><?= count($arr) ?> record(s) found</p>
                </div>
                <a class="btn-add" href="../logic/product_insert.php">&#43; Add Product</a>
            </div>

            <div class="tbl-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th class="sortable" data-col="1" data-type="num">ID <span class="sort-icon"></span></th>
                            <th class="sortable" data-col="2" data-type="num">Color ID <span class="sort-icon"></span></th>
                            <th class="sortable" data-col="3" data-type="num">Category ID <span class="sort-icon"></span></th>
                            <th class="sortable" data-col="4">Name <span class="sort-icon"></span></th>
                            <th class="sortable" data-col="5">Description <span class="sort-icon"></span></th>
                            <th class="sortable" data-col="6" data-type="num">Weight (g) <span class="sort-icon"></span></th>
                            <th class="sortable" data-col="7" data-type="num">Height (cm) <span class="sort-icon"></span></th>
                            <th class="sortable" data-col="8" data-type="num">Base Diameter (cm) <span class="sort-icon"></span></th>
                            <th class="sortable" data-col="9">Material <span class="sort-icon"></span></th>
                            <th class="sortable" data-col="10" data-type="num">Price (RM) <span class="sort-icon"></span></th>
                            <th class="sortable" data-col="11" data-type="num">Stock <span class="sort-icon"></span></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($arr as $p): ?>
                        <tr>
                            <td>
                                <?php if ($p->photo): ?>
                                    <img class="product-thumb"
                                         src="/photos/<?= htmlspecialchars($p->photo) ?>"
                                         alt="<?= htmlspecialchars($p->name) ?>">
                                <?php else: ?>
                                    <span class="no-photo">No Photo</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($p->id) ?></td>
                            <td><?= htmlspecialchars($p->color_id) ?></td>
                            <td><?= htmlspecialchars($p->category_id) ?></td>
                            <td><?= htmlspecialchars($p->name) ?></td>
                            <td class="desc-cell" title="<?= htmlspecialchars($p->description) ?>">
                                <?= htmlspecialchars($p->description) ?>
                            </td>
                            <td><?= htmlspecialchars($p->weight_g) ?></td>
                            <td><?= htmlspecialchars($p->height_cm) ?></td>
                            <td><?= htmlspecialchars($p->base_diameter_cm) ?></td>
                            <td><?= htmlspecialchars($p->material) ?></td>
                            <td><?= number_format($p->price, 2) ?></td>
                            <td><?= htmlspecialchars($p->stock) ?></td>
                            <td>
                                <div class="actions">
                                    <a class="btn-sm" href="../logic/product_update.php?id=<?= $p->id ?>">Edit</a>
                                    <a class="btn-sm btn-del"
                                       href="../logic/product_delete.php?id=<?= $p->id ?>"
                                       onclick="return confirm('Sure to delete?')">Delete</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </main>

    </div><!-- /.admin-body -->

    <!-- Footer -->
    <div class="admin-footer">
        &copy; <?= date('Y') ?> Admin Panel
    </div>

</div><!-- /.admin-shell -->

<style>
.tbl-wrap {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.tbl-wrap table {
    min-width: 1200px;
}

/* Description cell — truncate long text, show full on hover */
.desc-cell {
    max-width: 180px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    cursor: default;
}

/* No-photo placeholder */
.no-photo {
    display: inline-block;
    width: 48px;
    height: 48px;
    line-height: 48px;
    text-align: center;
    background: #f0f0f0;
    color: #aaa;
    font-size: 10px;
    border-radius: 4px;
}
</style>

<?php include '../components/footer.php'; ?>

<script>
const table = document.querySelector('table');
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
</script>