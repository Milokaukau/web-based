<?php
include '../logic/product_base.php';

if (is_get()) {
    $id = (int) req('id');

    if ($id <= 0) {
        temp('error', 'Invalid product ID.');
        redirect('../pages/admin/admin.php?page=stock');
    }

    $stm = db()->prepare('UPDATE tb_product SET is_active = 1 WHERE id = ?');
    $stm->execute([$id]);

    temp('info', 'Product restored successfully.');
    redirect('../pages/admin/admin.php?page=stock');
}