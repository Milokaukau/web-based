<?php
include '../database/product_base.php';

if (is_get()) {
    $id    = req('id');
    $stock = req('stock');

    if ((int)$stock <= 0) {
        temp('error', 'Cannot restore. Stock must be greater than 0.');
        redirect('../pages/admin/admin.php?page=stock');
    } else {
        $stm = db()->prepare('UPDATE tb_product SET is_active = 1, stock = ? WHERE id = ?');
        $stm->execute([$stock, $id]);

        temp('info', 'Record restored.');
        redirect('../pages/admin/admin.php?page=stock');
    }
}