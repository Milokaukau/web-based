<?php
include '../logic/product_base.php';

if (is_get()) {
    $id = (int) req('id');
    
    // Explicitly check for the GET parameter to prevent "0" from evaluating to false
    if (isset($_GET['cat_id']) && $_GET['cat_id'] !== '') {
        $return_url = "/pages/admin/category_items.php?id=" . urlencode($_GET['cat_id']);
    } else {
        $return_url = "/pages/admin/admin.php?page=stock";
    }

    // Validate
    if ($id <= 0) {
        temp('error', 'Invalid product ID.');
        redirect($return_url);
    }

    // Check product exists and is currently active
    $check = db()->prepare('SELECT id, is_active FROM tb_product WHERE id = ?');
    $check->execute([$id]);
    $product = $check->fetch();

    if (!$product) {
        temp('error', 'Product not found.');
        redirect($return_url);
    }

    if (!$product->is_active) {
        temp('info', 'Product is already inactive.');
        redirect($return_url);
    }

    // Soft-delete: set is_active = 0, leave stock untouched
    $stm = db()->prepare('UPDATE tb_product SET is_active = 0 WHERE id = ?');
    $stm->execute([$id]);

    temp('info', 'Product deleted (moved to inactive).');
    redirect($return_url);
}

// Block non-GET access
temp('error', 'Invalid request.');
redirect('/pages/admin/admin.php?page=stock');