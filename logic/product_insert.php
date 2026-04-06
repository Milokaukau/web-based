<?php
include '../database/product_base.php';

// ----------------------------------------------------------------------------

if (is_post()) {
    $color_id        = req('color_id');
    $category_id     = req('category_id');
    $name            = req('name');
    $description     = req('description');
    $weight_g        = req('weight_g');
    $height_cm       = req('height_cm');
    $base_diameter_cm = req('base_diameter_cm');
    $material        = req('material');
    $price           = req('price');
    $stock           = req('stock');
    $f               = get_file('photo');

    if ($color_id == '')                    $_err['color_id'] = 'Required';
    else if (!is_exists($color_id, 'tb_color', 'id')) $_err['color_id'] = 'Invalid';

    if ($category_id == '')                 $_err['category_id'] = 'Required';
    else if (!is_exists($category_id, 'tb_category', 'id')) $_err['category_id'] = 'Invalid';

    if ($name == '')                        $_err['name'] = 'Required';
    else if (strlen($name) > 100)           $_err['name'] = 'Maximum 100 characters';

    if (strlen($description) > 500)         $_err['description'] = 'Maximum 500 characters';

    if ($weight_g === '')                   $_err['weight_g'] = 'Required';
    else if (!is_numeric($weight_g) || $weight_g <= 0) $_err['weight_g'] = 'Must be a positive number';

    if ($height_cm === '')                  $_err['height_cm'] = 'Required';
    else if (!is_numeric($height_cm) || $height_cm <= 0) $_err['height_cm'] = 'Must be a positive number';

    if ($base_diameter_cm === '')           $_err['base_diameter_cm'] = 'Required';
    else if (!is_numeric($base_diameter_cm) || $base_diameter_cm <= 0) $_err['base_diameter_cm'] = 'Must be a positive number';

    if ($material == '')                    $_err['material'] = 'Required';
    else if (strlen($material) > 100)       $_err['material'] = 'Maximum 100 characters';

    if ($price == '')                       $_err['price'] = 'Required';
    else if (!is_money($price))             $_err['price'] = 'Must be money';
    else if ($price < 0.01 || $price > 99.99) $_err['price'] = 'Must between 0.01 - 99.99';

    if ($stock === '')                      $_err['stock'] = 'Required';
    else if (!ctype_digit($stock) || (int)$stock < 0) $_err['stock'] = 'Must be a non-negative integer';

    if (!$f)                                $_err['photo'] = 'Required';
    else if (!str_starts_with($f->type, 'image/')) $_err['photo'] = 'Must be image';
    else if ($f->size > 1 * 1024 * 1024)   $_err['photo'] = 'Maximum 1MB';

    if (!$_err) {
        $photo = save_photo($f, '../photos');

        $stm = $_db->prepare('
            INSERT INTO tb_product (color_id, category_id, name, description, weight_g, height_cm, base_diameter_cm, material, price, stock, photo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');
        $stm->execute([$color_id, $category_id, $name, $description, $weight_g, $height_cm, $base_diameter_cm, $material, $price, $stock, $photo]);

        temp('info', 'Record inserted');
        redirect('../pages/product_maintenance.php');
    }
}

// ----------------------------------------------------------------------------

$colors     = $_db->query('SELECT id, name FROM tb_color ORDER BY name')
                  ->fetchAll(PDO::FETCH_KEY_PAIR);

$categories = $_db->query('SELECT id, name FROM tb_category ORDER BY name')
                  ->fetchAll(PDO::FETCH_KEY_PAIR);

$_title = 'Product | Insert';
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
                    <h1>Add Product</h1>
                    <p class="sub">Fill in the details below to create a new product</p>
                </div>
                <a class="btn-back" href="../pages/product_maintenance.php">&#8592; Back to Index</a>
            </div>

            <div class="form-card">
                <form method="post" enctype="multipart/form-data" novalidate>
                    <table class="form-table">
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
                                <span class="hint">e.g. Pro-Grade Stainless Steel</span>
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
                                    <img src="/images/photo.jpg" alt="Preview" id="preview">
                                    <label class="upload-label">
                                        &#128247; Choose image
                                        <?= html_file('photo', 'image/*', 'hidden') ?>
                                    </label>
                                </div>
                                <span class="hint">Required. Image file, max 1MB.</span>
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