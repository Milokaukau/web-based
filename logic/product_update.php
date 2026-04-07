<?php
include '../database/product_base.php';

// ----------------------------------------------------------------------------

if (is_get()) {
    $id = req('id');

    $stm = $_db->prepare('SELECT * FROM tb_product WHERE id = ?');
    $stm->execute([$id]);
    $p = $stm->fetch();

    if (!$p) {
        redirect('index.php');
    }

    extract((array)$p);
    $_SESSION['photo'] = $p->photo;
}

if (is_post()) {
    $id               = req('id');
    $color_id         = req('color_id');
    $category_id      = req('category_id');
    $name             = req('name');
    $description      = req('description');
    $weight_g         = req('weight_g');
    $height_cm        = req('height_cm');
    $base_diameter_cm = req('base_diameter_cm');
    $material         = req('material');
    $price            = req('price');
    $stock            = req('stock');
    $f                = get_file('photo');
    $photo            = $_SESSION['photo'];

    if ($color_id == '')            $_err['color_id']    = 'Required';
    if ($category_id == '')         $_err['category_id'] = 'Required';

    if ($name == '')                $_err['name'] = 'Required';
    else if (strlen($name) > 100)   $_err['name'] = 'Maximum 100 characters';

    if (strlen($description) > 500) $_err['description'] = 'Maximum 500 characters';

    if ($weight_g === '')           $_err['weight_g'] = 'Required';
    else if (!is_numeric($weight_g) || $weight_g <= 0) $_err['weight_g'] = 'Must be a positive number';

    if ($height_cm === '')          $_err['height_cm'] = 'Required';
    else if (!is_numeric($height_cm) || $height_cm <= 0) $_err['height_cm'] = 'Must be a positive number';

    if ($base_diameter_cm === '')   $_err['base_diameter_cm'] = 'Required';
    else if (!is_numeric($base_diameter_cm) || $base_diameter_cm <= 0) $_err['base_diameter_cm'] = 'Must be a positive number';

    if ($material == '')            $_err['material'] = 'Required';
    else if (strlen($material) > 100) $_err['material'] = 'Maximum 100 characters';

    if ($price == '')               $_err['price'] = 'Required';
    else if (!is_money($price))     $_err['price'] = 'Must be money';
    else if ($price < 0.01 || $price > 99.99) $_err['price'] = 'Must between 0.01 - 99.99';

    if ($stock === '')              $_err['stock'] = 'Required';
    else if (!ctype_digit($stock) || (int)$stock < 0) $_err['stock'] = 'Must be a non-negative integer';

    if ($f) {
        if (!str_starts_with($f->type, 'image/'))  $_err['photo'] = 'Must be image';
        else if ($f->size > 1 * 1024 * 1024)       $_err['photo'] = 'Maximum 1MB';
    }

    if (!$_err) {
        if ($f) {
            if ($photo && file_exists("../photos/$photo")) unlink("../photos/$photo");
            $photo = save_photo($f, '../photos');
        }

        $stm = $_db->prepare('
            UPDATE tb_product
            SET color_id = ?, category_id = ?, name = ?, description = ?,
                weight_g = ?, height_cm = ?, base_diameter_cm = ?, material = ?,
                price = ?, stock = ?, photo = ?
            WHERE id = ?
        ');
        $stm->execute([$color_id, $category_id, $name, $description,
                       $weight_g, $height_cm, $base_diameter_cm, $material,
                       $price, $stock, $photo, $id]);

        temp('info', 'Record updated');
        redirect('../pages/product_maintenance.php');
    }
}

// ----------------------------------------------------------------------------

$colors     = $_db->query('SELECT id, name FROM tb_color ORDER BY name')
                  ->fetchAll(PDO::FETCH_KEY_PAIR);

$categories = $_db->query('SELECT id, name FROM tb_category ORDER BY name')
                  ->fetchAll(PDO::FETCH_KEY_PAIR);

$_title = 'Product | Update';
include '../components/header.php';
?>


<div class="admin-shell">

    <!-- Top Bar -->
    <div class="top-bar">
        <a class="logo" href="/index.php">Admin Panel</a>
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
            <a class="nav-item" href="../pages/order_listing.php">
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
                    <h1>Update Product</h1>
                    <p class="sub">Edit the details below and submit</p>
                </div>
                <a class="btn-back" href="../pages/product_maintenance.php">&#8592; Back to Index</a>
            </div>

            <div class="form-card">
                <form method="post" enctype="multipart/form-data" novalidate>
                    <table class="form-table">
                        <tr>
                            <th>ID</th>
                            <td>
                                <span class="id-val"><?= $id ?></span>
                                <input type="hidden" name="id" value="<?= $id ?>">
                            </td>
                        </tr>
                        <tr>
                            <th><label for="name">Name</label></th>
                            <td>
                                <?= html_text('name', 'maxlength="100"') ?>
                                <?= err('name') ?>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="color_id">Color</label></th>
                            <td>
                                <?= html_select('color_id', $colors) ?>
                                <?= err('color_id') ?>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="category_id">Category</label></th>
                            <td>
                                <?= html_select('category_id', $categories) ?>
                                <?= err('category_id') ?>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="description">Description</label></th>
                            <td>
                                <?php
                                    $val = htmlspecialchars($GLOBALS['description'] ?? '');
                                    echo "<textarea id='description' name='description' maxlength='500' rows='4'>$val</textarea>";
                                ?>
                                <span class="hint">Optional. Maximum 500 characters.</span>
                                <?= err('description') ?>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="weight_g">Weight (g)</label></th>
                            <td>
                                <?= html_number('weight_g', 0.01, 99999, 0.01) ?>
                                <span class="hint">In grams, e.g. 295</span>
                                <?= err('weight_g') ?>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="height_cm">Height (cm)</label></th>
                            <td>
                                <?= html_number('height_cm', 0.01, 9999, 0.01) ?>
                                <span class="hint">In centimetres, e.g. 22.50</span>
                                <?= err('height_cm') ?>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="base_diameter_cm">Base Diameter (cm)</label></th>
                            <td>
                                <?= html_number('base_diameter_cm', 0.01, 9999, 0.01) ?>
                                <span class="hint">In centimetres, e.g. 7.50</span>
                                <?= err('base_diameter_cm') ?>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="material">Material</label></th>
                            <td>
                                <?= html_text('material', 'maxlength="100"') ?>
                                <span class="hint">e.g. 18/8 Pro-Grade Stainless Steel</span>
                                <?= err('material') ?>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="price">Price (RM)</label></th>
                            <td>
                                <?= html_number('price', 0.01, 99.99, 0.01) ?>
                                <span class="hint">Between 0.01 and 99.99</span>
                                <?= err('price') ?>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="stock">Stock</label></th>
                            <td>
                                <?= html_number('stock', 0, 9999, 1) ?>
                                <?= err('stock') ?>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="photo">Photo</label></th>
                            <td>
                                <div class="upload-wrap">
                                    <img src="/web-based/photos/<?= htmlspecialchars($photo) ?>"
                                         alt="Product photo" id="preview">
                                    <label class="upload-label">
                                        &#128247; Choose image
                                        <?= html_file('photo', 'image/*', 'hidden') ?>
                                    </label>
                                </div>
                                <span class="hint">Optional. Leave empty to keep current photo.</span>
                                <?= err('photo') ?>
                            </td>
                        </tr>
                    </table>

                    <div class="form-actions">
                        <button class="btn-submit" type="submit">Submit</button>
                        <button class="btn-reset"  type="reset">Reset</button>
                    </div>
                </form>
            </div>

        </main>
    </div><!-- /.admin-body -->

    <!-- Footer -->
    <div class="admin-footer">
        &copy; <?= date('Y') ?> Admin Panel
    </div>

</div><!-- /.admin-shell -->

<script src="../js/admin_product.js"></script>

<?php include '../components/footer.php'; ?>