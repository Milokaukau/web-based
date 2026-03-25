<?php
$project_root = $_SERVER['DOCUMENT_ROOT']."/";
require_once $project_root . "database/order.php";

$arr = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_id'], $_POST['status'])) {
    $payment_id = $_POST['payment_id'];
    $status = $_POST['status'];
    
    updateOrderStatus($payment_id, $status);
    
    header("Location: order_listing.php");
    exit;
}

if (isset($_GET['member_id']) && is_numeric($_GET['member_id'])) {
    $arr = getOrderListByMemberId($_GET['member_id']);
    return;
}
    
$arr = getOrderList();

?>