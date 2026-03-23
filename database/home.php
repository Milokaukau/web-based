<?php
require_once "db.php";

function getLatestProduct(){
    $stmt = db()->query("SELECT * FROM tb_product ORDER BY id DESC LIMIT 12");
    return $stmt->fetchAll();
}