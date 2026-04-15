<?php
include '../database/product_base.php';
include '../database/product_query.php';

// ----------------------------------------------------------------------------

if (is_get()) {
    $id = req('id');
    $p = get_product_by_id(db(), $id);

    if (!$p) {
        redirect('index.php');
    }

    extract((array)$p);
    $_SESSION['photo'] = $p->photo;

   
    foreach ((array)$p as $k => $v) {
        $GLOBALS[$k] = $v;
    }
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

        update_product(db(), $color_id, $category_id, $name, $description,
                       $weight_g, $height_cm, $base_diameter_cm, $material,
                       $price, $stock, $photo, $id);

        temp('info', 'Record updated');
        redirect('../pages/product_maintenance.php');
    }
}

// ----------------------------------------------------------------------------

$colors     = get_colors(db());
$categories = array_column(get_categories(db()), 'name', 'id');

$_title = 'Product | Update';
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
                        <?php if (!$p->is_active || $p->stock == 0): ?>
                            <a class="btn-restore" href="../logic/product_restore.php?id=<?= $id ?>"
                            onclick="return confirm('Restore this product?')">Restore</a>
                        <?php endif; ?>
                        <button class="btn-reset"  type="reset">Reset</button>
                    </div>
                </form>
            </div>

        </main>

        <?php include '../components/admin_footer.php'; ?>

    </div><!-- /.admin-body -->

</div><!-- /.admin-shell -->

<script src="../js/admin_product.js"></script>

<?php include '../components/footer.php'; ?>