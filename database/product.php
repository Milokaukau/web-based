<?php
require_once "db.php";

function getProductById($id){
    $stmt = db()->query("SELECT * FROM tb_product WHERE id = $id");
    return $stmt->fetch();
}
