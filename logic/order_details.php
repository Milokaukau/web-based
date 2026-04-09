<?php
require_once $project_root . "logic/auth_helper.php";

requireMember(); 

require_once $project_root . "database/order.php";

$member_id = $_SESSION['member_id']; 
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

$status_labels = [
    'pending_payment' => 'Pending Payment',
    'confirmed'       => 'Confirmed',
    'in_delivery'     => 'In Delivery',
    'delivered'       => 'Delivered',
    'completed'       => 'Completed',
    'cancelled'       => 'Cancelled',
    'pending_refund'  => 'Refund Pending',
    'refunded'        => 'Refunded'
];
?>