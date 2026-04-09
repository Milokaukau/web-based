<?php
require_once $project_root . "logic/auth_helper.php";

requireMember(); 
require_once $project_root . "database/order.php";

$member_id = $_SESSION['member_id']; 

$orders = getOrdersByMember($member_id);

foreach ($orders as $order) {
    $order->items = getOrderItems($order->order_id);
}
?>