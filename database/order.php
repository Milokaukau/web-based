<?php
require_once "db.php";

function getOrderListQuery(){
    return "
    SELECT 
        o.id AS order_id,
        o.member_id,
        o.status AS order_status,
        o.amount,
        o.created_at,
        m.name AS member_name,
        op.quantity,
        op.price AS purchase_price,
        p.name AS product_name,
        pm.method AS payment_method,
        pm.status AS payment_status
    FROM tb_order o
    JOIN tb_member m ON m.id = o.member_id
    JOIN tb_order_product op ON op.order_id = o.id
    JOIN tb_product p ON p.id = op.product_id
    LEFT JOIN tb_payment pm ON pm.order_id = o.id
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
 */
function insertOrder($member_id, $amount, $pay_method, $pay_status, $cart, $address, $city, $postcode) {
    $db = db();
    $db->beginTransaction();

    try {
        // 1. Insert order record FIRST (Defaults to 'pending_payment' based on your DB rules)
        $stmt = $db->prepare("
            INSERT INTO tb_order (member_id, amount, status, created_at)
            VALUES (?, ?, 'pending_payment', NOW())
        ");
        $stmt->execute([$member_id, $amount]);
        $order_id = $db->lastInsertId();

        // 2. Insert payment record SECOND (linked to the order_id)
        $stmt = $db->prepare("
            INSERT INTO tb_payment (order_id, method, status, created_at, completed_at)
            VALUES (?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([$order_id, $pay_method, $pay_status]);
        
        // If payment is success, update the order status
        if ($pay_status === 'success') {
            $stmtUpdate = $db->prepare("UPDATE tb_order SET status = 'confirmed' WHERE id = ?");
            $stmtUpdate->execute([$order_id]);
        }

        // 3. Insert each cart item as an order_product row (WITH price snapshot)
        $stmt = $db->prepare("
            INSERT INTO tb_order_product (order_id, product_id, quantity, price)
            VALUES (?, ?, ?, ?)
        ");
        foreach ($cart as $key => $item) {
            // Note: Make sure your session $cart array includes 'price'. If it doesn't, 
            // you will need to SELECT the price from tb_product inside this loop.
            $price = $item['price'] ?? 0; 
            $stmt->execute([$order_id, $item['id'], $item['qty'], $price]);
        }

        $db->commit();
        return (int) $order_id;

    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
}

function updateOrderStatus($order_id, $status) {
    $stmt = db()->prepare("
        UPDATE tb_order 
        SET status = ? 
        WHERE id = ?
    ");
    $stmt->execute([$status, $order_id]); 
}

function getOrdersByMember($member_id) {
    $stmt = db()->prepare("
        SELECT 
            o.id AS order_id, 
            o.amount, 
            o.status AS order_status, 
            o.created_at, 
            p.status AS payment_status, 
            p.method AS payment_method
        FROM tb_order o
        LEFT JOIN tb_payment p ON o.id = p.order_id AND p.status = 'success'
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
            op.price AS purchase_price,
            pr.name AS product_name, 
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

function getOrderById($order_id, $member_id) {
    $stmt = db()->prepare("
        SELECT 
            o.id AS order_id, 
            o.amount, 
            o.status AS order_status, 
            o.created_at, 
            p.status AS payment_status, 
            p.method AS payment_method
        FROM tb_order o
        LEFT JOIN tb_payment p ON o.id = p.order_id
        WHERE o.id = ? AND o.member_id = ?
    ");
    $stmt->execute([$order_id, $member_id]);
    return $stmt->fetch(); 
}

function cancelAndRefundOrder($order_id) {
    $db = db();
    
    try {
        $db->beginTransaction();

        // 1. Update Order Status to cancelled
        $stmtOrder = $db->prepare("UPDATE tb_order SET status = 'cancelled' WHERE id = ?");
        $stmtOrder->execute([$order_id]);
        
        // 2. Update Payment Status to refunded
        $stmtPay = $db->prepare("UPDATE tb_payment SET status = 'refunded' WHERE order_id = ? AND status = 'success'");
        $stmtPay->execute([$order_id]);
        
        // 3. Restore Stock
        $stmtStock = $db->prepare("
            UPDATE tb_product p 
            JOIN tb_order_product op ON p.id = op.product_id 
            SET p.stock = p.stock + op.quantity 
            WHERE op.order_id = ?
        ");
        $stmtStock->execute([$order_id]);
        
        $db->commit();
        
    } catch (Exception $e) {
        $db->rollBack();
        throw $e; // Throw back to logic file to handle the error if needed
    }
}