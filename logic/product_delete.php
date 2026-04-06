<?php
include '../database/product_base.php';

// ----------------------------------------------------------------------------

if (is_get()) {
    $id = req('id');

    // Fetch and delete photo file
    $stm = $_db->prepare('SELECT photo FROM tb_product WHERE id = ?');
    $stm->execute([$id]);
    $photo = $stm->fetchColumn();

    if ($photo && file_exists("../photos/$photo")) {
        unlink("../photos/$photo");
    }

    // Delete product record
    $stm = $_db->prepare('DELETE FROM tb_product WHERE id = ?');
    $stm->execute([$id]);

    temp('info', 'Record deleted');
    redirect('../pages/product_maintenance.php');
}