<?php
$project_root = $_SERVER['DOCUMENT_ROOT']."/";
require_once $project_root . "database/order.php";

$arr = [];

if (isset($_GET['member_id']) && is_numeric($_GET['member_id'])) {
    $arr = getOrderListByMember($_GET['member_id']);
    return;
}
    
$arr = getOrderList();

?> 