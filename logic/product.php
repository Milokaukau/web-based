<?php
$project_root = $_SERVER['DOCUMENT_ROOT']."/";
require_once $project_root."config.php";
require_once $project_root."database/product.php";

$arr = null;
$variants = [];

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $arr = getProductById($_GET['id']);

    if (!$arr) {
        $cat_id = getProductCategoryId($_GET['id']);
        $fallback = getFirstProductByCategory($cat_id);
        if ($fallback) {
            header("Location: product.php?id=" . $fallback->id);
            exit;
        }
    }
}

if (!$arr) {
    die("Product not found.");
}

$variants = getVariantsByCategory($arr->category_id);