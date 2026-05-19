<?php
$project_root = $_SERVER['DOCUMENT_ROOT']."/";
require_once $project_root . "database/home.php";
require_once $project_root . "database/product.php";

$arr = getLatestProduct();

// Fetch slider products dynamically
$slide_product_1 = getProductById(1); // Pro Max
$slide_product_2 = getProductById(18); // Coffee Cup
