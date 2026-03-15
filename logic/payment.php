<?php
// 1. Redirect if cart is empty
if (empty($_SESSION['cart'])) {
    $project_root = $_SERVER['DOCUMENT_ROOT']."/";
    exit;
}

// 2. Calculate Totals
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal += ($item['price'] * $item['qty']);
}

$shipping = 0.00;
$total = $subtotal + $shipping;