<?php
include '../logic/product_base.php';

if (is_get()) {
    $id = (int) req('id');

    // Validate
    if ($id <= 0) {
        temp('error', 'Invalid product ID.');
        redirect('../pages/admin/admin.php?page=stock');
    }

    // Check product exists and is currently active
    $check = db()->prepare('SELECT id, is_active FROM tb_product WHERE id = ?');
    $check->execute([$id]);
    $product = $check->fetch();

    if (!$product) {
        temp('error', 'Product not found.');
        redirect('../pages/admin/admin.php?page=stock');
    }

    if (!$product->is_active) {
        temp('info', 'Product is already inactive.');
        redirect('../pages/admin/admin.php?page=stock');
    }

    // Soft-delete: set is_active = 0, leave stock untouched
    $stm = db()->prepare('UPDATE tb_product SET is_active = 0 WHERE id = ?');
    $stm->execute([$id]);

    temp('info', 'Product deleted (moved to inactive).');
    redirect('../pages/admin/admin.php?page=stock');
}

// Block non-GET access
temp('error', 'Invalid request.');
redirect('../pages/admin/admin.php?page=stock');