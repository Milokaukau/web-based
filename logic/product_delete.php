<?php
include '../database/product_base.php';

// ----------------------------------------------------------------------------

if (is_get()) {
    $id = req('id');


    $stm = db()->prepare('UPDATE tb_product SET is_active = 0 WHERE id = ?');
    $stm->execute([$id]);

    temp('info', 'Record deleted');
    redirect('../pages/admin/admin.php?page=stock');
}