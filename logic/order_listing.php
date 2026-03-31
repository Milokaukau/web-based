<?php
$project_root = $_SERVER['DOCUMENT_ROOT']."/";
require_once $project_root . "database/order.php";
require_once $project_root . "database/member.php";

$arr = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_id'], $_POST['status'])) {
    $payment_id = $_POST['payment_id'];
    $status = $_POST['status'];
    
    updateOrderStatus($payment_id, $status);
    
    header("Location: order_listing.php");
    exit;
}

$members = getAllMembers();

if (isset($_GET['member_id']) && is_numeric($_GET['member_id'])) {
    $arr = getOrderListByMemberId($_GET['member_id']);
} else {
    $arr = getOrderList();
}
?>