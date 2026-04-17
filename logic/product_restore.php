<?php
include '../database/product_base.php';

if (is_get()) {
    $id = req('id');

    $stm = db()->prepare('UPDATE tb_product SET is_active = 1 WHERE id = ?');
    $stm->execute([$id]);

    temp('info', 'Record restored. Please update the stock.');
    redirect('../logic/product_update.php?id=' . $id);
}