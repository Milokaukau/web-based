<?php
$project_root = $_SERVER['DOCUMENT_ROOT']."/";
require_once $project_root . "database/order.php";
require_once $project_root . "database/member.php";

$arr = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    if (isset($_POST['update_type']) && $_POST['update_type'] === 'payment_status') {
        updateOrderPaymentStatus($_POST['order_id'], $_POST['status']);
    } else {
        updateOrderStatus($_POST['order_id'], $_POST['status']);
    }
    echo "success";
    exit;
}

$members = getAllMembers();
$filters = [];

if (!empty($_GET['member_id']) && is_numeric($_GET['member_id'])) $filters['member_id'] = $_GET['member_id'];
if (!empty($_GET['order_status']))     $filters['order_status']   = $_GET['order_status'];
if (!empty($_GET['payment_status']))   $filters['payment_status'] = $_GET['payment_status'];
if (!empty($_GET['payment_method']))   $filters['payment_method'] = $_GET['payment_method'];
if (!empty($_GET['date_from']))        $filters['date_from']      = $_GET['date_from'];
if (!empty($_GET['date_to']))          $filters['date_to']        = $_GET['date_to'];

$arr = getFilteredOrderList($filters);
?>