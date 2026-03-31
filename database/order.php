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

/**
 * Insert a new order with payment info and cart items.
 *
 * @param int    $member_id   Logged-in member's ID (0 for guest)
 * @param float  $amount      Total amount charged
 * @param string $pay_method  'card' | 'tng'
 * @param string $pay_status  e.g. 'paid'
 * @param array  $cart        $_SESSION['cart'] — keys are product IDs
 * @param string $address
 * @param string $city
 * @param string $postcode
 * @return int   Newly created order ID
 */
function insertOrder($member_id, $amount, $pay_method, $pay_status, $cart, $address, $city, $postcode) {
    $db = db();
    $db->beginTransaction();

    try {
        // 1. Insert payment record
        $stmt = $db->prepare("
            INSERT INTO tb_payment (method, status, created_at, completed_at)
            VALUES (?, ?, NOW(), NOW())
        ");
        $stmt->execute([$pay_method, $pay_status]);
        $payment_id = $db->lastInsertId();

        // 2. Insert order record
        $stmt = $db->prepare("
            INSERT INTO tb_order (member_id, payment_id, amount, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$member_id, $payment_id, $amount]);
        $order_id = $db->lastInsertId();

        // 3. Insert each cart item as an order_product row
        $stmt = $db->prepare("
            INSERT INTO tb_order_product (order_id, product_id, quantity)
            VALUES (?, ?, ?)
        ");
        foreach ($cart as $key => $item) {
            $stmt->execute([$order_id, $item['id'], $item['qty']]);
        }

        $db->commit();
        return (int) $order_id;

    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
}

function updateOrderStatus($payment_id, $status) {
    $stmt = db()->prepare("
        UPDATE tb_payment 
        SET status = ? 
        WHERE id = ?
    ");
    $stmt->execute([$status, $payment_id]); 
}

function getOrdersByMember($member_id) {
    $stmt = db()->prepare("
        SELECT 
            o.id AS order_id, 
            o.amount, 
            o.created_at, 
            p.status AS payment_status, 
            p.method AS payment_method
        FROM tb_order o
        JOIN tb_payment p ON o.payment_id = p.id
        WHERE o.member_id = ?
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$member_id]);
    return $stmt->fetchAll();
}

function getOrderItems($order_id) {
    $stmt = db()->prepare("
        SELECT 
            op.quantity, 
            pr.name AS product_name, 
            pr.price,
            pr.photo, 
            c.name AS color_name
        FROM tb_order_product op
        JOIN tb_product pr ON op.product_id = pr.id
        LEFT JOIN tb_color c ON pr.color_id = c.id
        WHERE op.order_id = ?
    ");
    $stmt->execute([$order_id]);
    return $stmt->fetchAll();
}