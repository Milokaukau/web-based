<?php
require_once "db.php";


function getOrderListQuery(){
    return "
    SELECT 
        o.id AS order_id,
        o.member_id,
        o.payment_id,
        o.amount,
        o.created_at,
        m.name AS member_name,
        op.quantity,
        p.name AS product_name,
        pm.method,
        pm.status
    FROM tb_order o
    JOIN tb_member m ON m.id = o.member_id
    JOIN tb_order_product op ON op.order_id = o.id
    JOIN tb_product p ON p.id = op.product_id
    JOIN tb_payment pm ON pm.id = o.payment_id
    ";
}

function getOrderList(){
    $stmt = db()->query(getOrderListQuery());
    return $stmt->fetchAll();
}

function getOrderListByMemberId($member_id){
    $stmt = db()->prepare(
        getOrderListQuery()
        ."WHERE o.member_id = ? 
    ");
    
    $stmt->execute([$member_id]);
    return $stmt->fetchAll();
}

function updateOrderStatus($payment_id, $status) {
    $stmt = db()->prepare("
        UPDATE tb_payment 
        SET status = ? 
        WHERE id = ?
    ");
    $stmt->execute([$status, $payment_id]); 
}