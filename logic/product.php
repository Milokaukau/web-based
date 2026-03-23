<?php
// logic/product.php
$project_root = $_SERVER['DOCUMENT_ROOT']."/";
require_once $project_root . "database/product.php";

$arr = [];
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $arr = getProductById($_GET['id']);
}

if (!$arr) {
    die("Product not found.");
}
?>