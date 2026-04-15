<?php
include '../database/product_base.php';
include '../database/product_query.php';

$arr        = get_all_products(db());
$active_arr  = array_filter($arr, fn($p) => (int)$p->is_active === 1 && (int)$p->stock > 0);
$deleted_arr = array_filter($arr, fn($p) => (int)$p->is_active === 0 || (int)$p->stock == 0);
$active_arr  = array_values($active_arr);
$deleted_arr = array_values($deleted_arr); 

$_title = 'Product | Index';
include '../components/header.php';
?>

<div class="admin-shell">

    <?php include '../components/admin_topbar.php'; ?>

    <div class="admin-body">

        <?php $active = 'product'; include '../components/admin_sidebar.php'; ?>

        <!-- Main -->
        <main class="main-content">
            <div class="page-header">
                <div>
                    <h1>Product</h1>
                    <p class="sub"><?= count($arr) ?> record(s) found</p>
                </div>
                <a class="btn-add" href="../logic/product_insert.php">&#43; Add Product</a>
            </div>

            <div class="search-wrap">
                <input type="text" id="product-search" placeholder="Search by name, material, description…" autocomplete="off">
            </div>

            <div class="filter-wrap">
                <!-- Category filter -->
                <select id="filter-category">
                    <option value="">All Categories</option>
                    <?php
                    $cats = array_unique(array_column($arr, 'category_id'));
                    sort($cats);
                    foreach ($cats as $cid): ?>
                        <option value="<?= htmlspecialchars($cid) ?>"><?= htmlspecialchars($cid) ?></option>
                    <?php endforeach; ?>
                </select>

                <!-- Price range filter -->
                <div class="price-range">
                    <input type="number" id="filter-price-min" placeholder="Min price (RM)" min="0" step="0.01">
                    <span>–</span>
                    <input type="number" id="filter-price-max" placeholder="Max price (RM)" min="0" step="0.01">
                </div>

                <button id="filter-reset" type="button">Reset</button>
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
                    <tbody id="product-tbody">
                        <?php foreach ($active_arr as $p): ?>
                        <tr data-group="active" class="">
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
                        <?php endforeach; ?>

                        <?php foreach ($deleted_arr as $p): ?>
                        <tr data-group="deleted" class="row-disabled">
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
                                    <!-- 没有 Delete 按钮 -->
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="pagination-wrap">
                    <span id="pagination-info"></span>
                    <div id="pagination"></div>
            </div>
        </main>

    </div><!-- /.admin-body -->

    <?php include '../components/admin_footer.php'; ?>

</div><!-- /.admin-shell -->

<script src="../js/admin_product.js"></script>

<?php include '../components/footer.php'; ?>