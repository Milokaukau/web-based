<?php
require_once $project_root . "database/order.php";

$member_id = $_SESSION['member_id'] ?? 3; 
$order_id = $_GET['id'] ?? null;

if (!$order_id) {
    header("Location: order_history.php");
    exit;
}

$order = getOrderById($order_id, $member_id);

if (!$order) {
    header("Location: order_history.php");
    exit;
}

$items = getOrderItems($order_id);

$payment_labels = [
    'e_wallet'       => 'E-Wallet',
    'online_banking' => 'Online Banking',
    'credit_card'    => 'Credit Card'
];
?>