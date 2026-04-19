<?php
include '../database/product_base.php';
include '../database/product.php';

// ----------------------------------------------------------------------------

if (is_post()) {
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

    if ($color_id == '')                    $_err['color_id'] = 'Required';
    else if (!is_exists($color_id, 'tb_color', 'id')) $_err['color_id'] = 'Invalid';

    if ($category_id == '')                 $_err['category_id'] = 'Required';
    else if (!is_exists($category_id, 'tb_category', 'id')) $_err['category_id'] = 'Invalid';

    if ($name == '')                        $_err['name'] = 'Required';
    else if (strlen($name) > 100)           $_err['name'] = 'Maximum 100 characters';

    if (strlen($description) > 5000)        $_err['description'] = 'Maximum 5000 characters';

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
        $photo = save_photo($f, '../images');

        insert_product(db(), $color_id, $category_id, $name, $description,
                       $weight_g, $height_cm, $base_diameter_cm, $material,
                       $price, $stock, $photo);

        temp('info', 'Record inserted');
        redirect('../pages/admin/admin.php?page=stock');
    }
}

// ----------------------------------------------------------------------------

$colors      = get_colors(db());
$cat_options = get_categories(db());

$_title = 'Product | Insert';
include '../components/header.php';
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.page-wrap {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    min-height: 100vh;
    padding: 40px 5%;
    background: #F7F7F7;
}

.form-card {
    background: #FFFFFF;
    border: 1px solid #E5E7EB;
    border-radius: 16px;
    padding: 36px 36px 28px;
    width: 100%;
    max-width: 720px;
}

.form-table {
    width: 100%;
    border-collapse: collapse;
}
.form-table tr {
    border-bottom: 1px solid #E5E7EB;
}
.form-table tr:last-child {
    border-bottom: none;
}
.form-table th {
    width: 190px;
    text-align: left;
    padding: 0.85rem 1rem 0.85rem 0;
    font-family: 'Inter', sans-serif;
    font-size: 0.68rem;
    font-weight: 700;
    color: #9CA3AF;
    text-transform: uppercase;
    letter-spacing: 1px;
    vertical-align: top;
    padding-top: 1.1rem;
    white-space: nowrap;
}
.form-table td {
    padding: 0.65rem 0;
}

.form-table td input[type="text"],
.form-table td input[type="number"],
.form-table td select,
.form-table td textarea {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid #D1D5DB;
    border-radius: 9px;
    background: #FFFFFF;
    color: #111111;
    font-size: 0.88rem;
    font-family: 'Inter', sans-serif;
    line-height: 1.5;
    outline: none;
    appearance: none;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.form-table td input:focus,
.form-table td select:focus,
.form-table td textarea:focus {
    border-color: #fecaca;
    box-shadow: 0 0 0 3px rgba(243,158,158,0.15);
}
.form-table td input::placeholder,
.form-table td textarea::placeholder {
    color: #9CA3AF;
}
.form-table td textarea {
    resize: vertical;
    line-height: 1.6;
}
.form-table td select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath d='M2 4l4 4 4-4' stroke='%239CA3AF' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    padding-right: 2.25rem;
    cursor: pointer;
}

.hint {
    display: block;
    font-size: 0.72rem;
    color: #9CA3AF;
    margin-top: 5px;
    font-family: 'Inter', sans-serif;
}
.err {
    display: block;
    font-size: 0.72rem;
    color: #991B1B;
    background: #FEE2E2;
    padding: 3px 8px;
    border-radius: 6px;
    margin-top: 5px;
    font-weight: 600;
    font-family: 'Inter', sans-serif;
}

.upload-wrap {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 4px;
}
#preview {
    width: 76px;
    height: 76px;
    object-fit: cover;
    border-radius: 12px;
    border: 1px solid #E5E7EB;
    background: #F7F7F7;
}
.upload-label {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 18px;
    border: 1px solid #E5E7EB;
    border-radius: 9999px;
    font-family: 'Inter', sans-serif;
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    cursor: pointer;
    background: #FFFFFF;
    color: #6B7280;
    transition: border-color 0.2s, color 0.2s;
}
.upload-label:hover {
    border-color: #e08585;
    color: #e08585;
}

.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 18px;
    border: 1px solid #E5E7EB;
    border-radius: 9999px;
    background: #FFFFFF;
    color: #9CA3AF;
    font-family: 'Inter', sans-serif;
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    text-decoration: none;
    margin-bottom: 20px;
    transition: border-color 0.2s, color 0.2s;
}
.btn-back:hover {
    border-color: #e08585;
    color: #e08585;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 28px;
    padding-top: 22px;
    border-top: 1px solid #E5E7EB;
}
.btn-submit {
    padding: 10px 28px;
    border: none;
    border-radius: 9999px;
    background: #e08585;
    color: #FFFFFF;
    font-family: 'Inter', sans-serif;
    font-size: 0.8rem;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    cursor: pointer;
    transition: background 0.2s, transform 0.1s;
}
.btn-submit:hover  { background: #c97070; }
.btn-submit:active { transform: scale(0.98); }

.btn-reset {
    padding: 10px 22px;
    border: 1px solid #E5E7EB;
    border-radius: 9999px;
    background: transparent;
    color: #9CA3AF;
    font-family: 'Inter', sans-serif;
    font-size: 0.8rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    cursor: pointer;
    transition: border-color 0.2s, color 0.2s;
}
.btn-reset:hover { border-color: #e08585; color: #e08585; }

.alert {
    padding: 12px 16px;
    border-radius: 12px;
    font-family: 'Inter', sans-serif;
    font-size: 0.85rem;
    font-weight: 500;
    margin-bottom: 20px;
}
.alert-success { background: #DCFCE7; color: #166534; }
.alert-error   { background: #FEE2E2; color: #991B1B; }

@media (max-width: 600px) {
    .form-card { padding: 24px 20px 20px; }
    .form-table th {
        display: block;
        width: auto;
        padding-bottom: 2px;
        padding-top: 0.75rem;
    }
    .form-table tr  { display: block; padding: 0.5rem 0; }
    .form-table td  { display: block; }
}
</style>

<div class="page-wrap">
    <div class="form-card">

        <a href="../pages/admin/admin.php?page=stock" class="btn-back">&#8592; Back to Stock</a>

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
                        <?= html_select('category_id', $cat_options) ?>
                        <?= err('category_id') ?>
                    </td>
                </tr>
                <tr>
                    <th><label for="description">Description</label></th>
                    <td>
                        <?php
                            $val = htmlspecialchars($GLOBALS['description'] ?? '');
                            echo "<textarea id='description' name='description' maxlength='5000' rows='4'>$val</textarea>";
                        ?>
                        <span class="hint">Optional. Maximum 5000 characters.</span>
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
                            <img src="/images/upload.png" alt="Preview" id="preview">
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
                <button class="btn-reset" type="reset">Reset</button>
            </div>
        </form>
    </div>
</div>

<script>
function initPhotoPreview() {
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
            preview.src = '/images/photo.jpg';
        });
    }
}

initPhotoPreview();
</script>

<?php include '../components/footer.php'; ?>