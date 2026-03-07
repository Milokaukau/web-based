<?php
require_once "db.php";

function getOrderList(){
    $stmt = db()->query("
    SELECT 
    o.id AS order_id,
    p.name AS product_name,
    op.quantity,
    o.amount,
    m.name AS member_name,
    pm.status
    FROM tb_order o
    JOIN tb_member m ON m.id = o.member_id
    JOIN tb_order_product op ON op.order_id = o.id
    JOIN tb_product p ON p.id = op.product_id
    JOIN tb_payment pm ON pm.id = o.payment_id
    ");
    return $stmt->fetchAll();
}

function getOrderListByMember($member_id){
    $stmt = db()->prepare("
        SELECT 
            o.id AS order_id,
            p.name AS product_name,
            op.quantity,
            o.amount,
            m.name AS member_name,
            pm.status
        FROM tb_order o
        JOIN tb_member m ON m.id = o.member_id
        JOIN tb_order_product op ON op.order_id = o.id
        JOIN tb_product p ON p.id = op.product_id
        JOIN tb_payment pm ON pm.id = o.payment_id
        WHERE o.member_id = ? 
    ");
    
    $stmt->execute([$member_id]);
    return $stmt->fetchAll();
}