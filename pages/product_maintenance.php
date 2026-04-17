<?php
include '../database/product_base.php';
include '../database/product_query.php';

function photo_src(string $photo): string {
    if (!$photo) return '';
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/photos/' . $photo)) {
        return '/photos/' . $photo;
    }
    return '/images/' . $photo;
}

$arr        = get_all_products(db());
$active_arr = array_filter($arr, fn($p) => (int)$p->is_active === 1 && (int)$p->stock > 0);
$oos_arr    = array_filter($arr, fn($p) => (int)$p->is_active === 0 || (int)$p->stock === 0);
$active_arr = array_values($active_arr);
$oos_arr    = array_values($oos_arr);

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
                <div class="page-header-actions">
                    <a class="btn-add" href="../logic/product_insert.php">&#43; Add Product</a>
                    <button class="btn-oos" id="toggle-oos" type="button">
                        Out of Stock (<?= count($oos_arr) ?>)
                    </button>
                    <button class="btn-view-toggle" id="toggle-view" type="button">&#9783; Photo View</button>
                </div>
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
                    <tbody id="active-tbody">
                        <?php foreach ($active_arr as $p): ?>
                        <tr data-group="active">
                            <td>
                                <?php if ($p->photo): ?>
                                    <img class="product-thumb"
                                        src="<?= photo_src($p->photo) ?>"
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
                    </tbody>

                    <tbody id="deleted-tbody" hidden>
                        <?php foreach ($oos_arr as $p): ?> 
                        <tr data-group="deleted" class="row-disabled">
                            <td>
                                <?php if ($p->photo): ?>
                                    <img class="product-thumb"
                                        src="<?= photo_src($p->photo) ?>"
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
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="pagination-wrap" id="table-pagination-wrap">
                <span id="pagination-info"></span>
                <div id="pagination"></div>
            </div>

            <div id="photo-grid" class="photo-grid" hidden>
                <?php foreach ($active_arr as $p): ?>
                <div class="photo-card" 
                    data-group="active"
                    data-id="<?= $p->id ?>"
                    data-colorid="<?= htmlspecialchars($p->color_id) ?>"
                    data-catid="<?= htmlspecialchars($p->category_id) ?>"
                    data-name="<?= htmlspecialchars($p->name) ?>"
                    data-weight="<?= htmlspecialchars($p->weight_g) ?>"
                    data-height="<?= htmlspecialchars($p->height_cm) ?>"
                    data-diameter="<?= htmlspecialchars($p->base_diameter_cm) ?>"
                    data-material="<?= htmlspecialchars($p->material) ?>"
                    data-price="<?= number_format($p->price, 2) ?>"
                    data-stock="<?= htmlspecialchars($p->stock) ?>">

                    <div class="photo-card-img">
                        <?php if ($p->photo): ?>
                            <img src="<?= photo_src($p->photo) ?>" alt="<?= htmlspecialchars($p->name) ?>">
                        <?php else: ?>
                            <span class="no-photo">No Photo</span>
                        <?php endif; ?>
                    </div>

                    <div class="photo-card-info">
                        <span class="pc-id">#<?= $p->id ?></span>
                        <span class="pc-cat">Cat: <?= htmlspecialchars($p->category_id) ?></span>
                        <span class="pc-name"><?= htmlspecialchars($p->name) ?></span>
                        <span class="pc-stock">Stock: <?= htmlspecialchars($p->stock) ?></span>
                    </div>

                    <div class="photo-card-hover">
                        <p><span>ID</span><?= $p->id ?></p>
                        <p><span>Color ID</span><?= htmlspecialchars($p->color_id) ?></p>
                        <p><span>Category</span><?= htmlspecialchars($p->category_id) ?></p>
                        <p><span>Name</span><?= htmlspecialchars($p->name) ?></p>
                        <p><span>Weight</span><?= htmlspecialchars($p->weight_g) ?> g</p>
                        <p><span>Height</span><?= htmlspecialchars($p->height_cm) ?> cm</p>
                        <p><span>Diameter</span><?= htmlspecialchars($p->base_diameter_cm) ?> cm</p>
                        <p><span>Material</span><?= htmlspecialchars($p->material) ?></p>
                        <p><span>Price</span>RM <?= number_format($p->price, 2) ?></p>
                        <p><span>Stock</span><?= htmlspecialchars($p->stock) ?></p>
                        <div class="photo-card-actions">
                            <a class="btn-sm" href="../logic/product_update.php?id=<?= $p->id ?>">Edit</a>
                            <a class="btn-sm btn-del" href="../logic/product_delete.php?id=<?= $p->id ?>"
                            onclick="return confirm('Sure to delete?')">Delete</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <?php foreach ($oos_arr as $p): ?>
                <div class="photo-card card-disabled"
                    data-group="oos"
                    data-id="<?= $p->id ?>"
                    data-colorid="<?= htmlspecialchars($p->color_id) ?>"
                    data-catid="<?= htmlspecialchars($p->category_id) ?>"
                    data-name="<?= htmlspecialchars($p->name) ?>"
                    data-weight="<?= htmlspecialchars($p->weight_g) ?>"
                    data-height="<?= htmlspecialchars($p->height_cm) ?>"
                    data-diameter="<?= htmlspecialchars($p->base_diameter_cm) ?>"
                    data-material="<?= htmlspecialchars($p->material) ?>"
                    data-price="<?= number_format($p->price, 2) ?>"
                    data-stock="<?= htmlspecialchars($p->stock) ?>">

                    <div class="photo-card-img">
                        <?php if ($p->photo): ?>
                            <img src="<?= photo_src($p->photo) ?>" alt="<?= htmlspecialchars($p->name) ?>">
                        <?php else: ?>
                            <span class="no-photo">No Photo</span>
                        <?php endif; ?>
                    </div>

                    <div class="photo-card-info">
                        <span class="pc-id">#<?= $p->id ?></span>
                        <span class="pc-cat">Cat: <?= htmlspecialchars($p->category_id) ?></span>
                        <span class="pc-name"><?= htmlspecialchars($p->name) ?></span>
                        <span class="pc-stock">Stock: <?= htmlspecialchars($p->stock) ?></span>
                    </div>

                    <div class="photo-card-hover">
                        <p><span>ID</span><?= $p->id ?></p>
                        <p><span>Color ID</span><?= htmlspecialchars($p->color_id) ?></p>
                        <p><span>Category</span><?= htmlspecialchars($p->category_id) ?></p>
                        <p><span>Name</span><?= htmlspecialchars($p->name) ?></p>
                        <p><span>Weight</span><?= htmlspecialchars($p->weight_g) ?> g</p>
                        <p><span>Height</span><?= htmlspecialchars($p->height_cm) ?> cm</p>
                        <p><span>Diameter</span><?= htmlspecialchars($p->base_diameter_cm) ?> cm</p>
                        <p><span>Material</span><?= htmlspecialchars($p->material) ?></p>
                        <p><span>Price</span>RM <?= number_format($p->price, 2) ?></p>
                        <p><span>Stock</span><?= htmlspecialchars($p->stock) ?></p>
                        <div class="photo-card-actions">
                            <a class="btn-sm" href="../logic/product_update.php?id=<?= $p->id ?>">Edit</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div id="photo-pagination-wrap" class="pagination-wrap" hidden>
                <span id="photo-pagination-info"></span>
                <div id="photo-pagination"></div>
            </div>

        </main>

    </div><!-- /.admin-body -->

    <?php include '../components/admin_footer.php'; ?>

</div><!-- /.admin-shell -->

<script src="../js/admin_product.js"></script>

<?php include '../components/footer.php'; ?>