<?php
require_once "db.php";

function getLatestProduct(){
    $stmt = db()->query("SELECT * FROM tb_product GROUP BY name ORDER BY id DESC LIMIT 12");
    return $stmt->fetchAll();
}