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
        (
            SELECT SUM(quantity) 
            FROM tb_order_product 
            WHERE order_id = o.id
        ) AS quantity,
        (
            SELECT GROUP_CONCAT(p.name SEPARATOR ', ')
            FROM tb_order_product op
            JOIN tb_product p ON p.id = op.product_id
            WHERE op.order_id = o.id
        ) AS product_name,
        (
            SELECT method 
            FROM tb_payment 
            WHERE order_id = o.id 
            ORDER BY id DESC 
            LIMIT 1
        ) AS payment_method,
        (
            SELECT status 
            FROM tb_payment 
            WHERE order_id = o.id 
            ORDER BY id DESC 
            LIMIT 1
        ) AS payment_status
    FROM tb_order o
    JOIN tb_member m ON m.id = o.member_id
    ";
}

function getOrderList(){
    // Append order by descending so newest orders show first
    $stmt = db()->query(getOrderListQuery() . " ORDER BY o.id DESC");
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

function getOrderListByMemberId($member_id){
    $stmt = db()->prepare(
        getOrderListQuery() . " WHERE o.member_id = ? ORDER BY o.id DESC"
    );
    $stmt->execute([$member_id]);
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

function getOrderListByMember($member_id){
    $stmt = db()->prepare("
        SELECT 
            o.id AS order_id,
            o.created_at,
            p.id AS product_id,
            p.name AS product_name,
            p.price AS unit_price,
            op.quantity,
            o.amount,
            m.name AS member_name,
            pm.status
        FROM tb_order o
        JOIN tb_member m ON m.id = o.member_id
        JOIN tb_order_product op ON op.order_id = o.id
        JOIN tb_product p ON p.id = op.product_id
        JOIN tb_payment pm ON pm.order_id = o.id
        WHERE o.member_id = ? 
        ORDER BY o.created_at DESC
    ");
    
    $stmt->execute([$member_id]);
    return $stmt->fetchAll(PDO::FETCH_OBJ);
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

function getOrders(): array {
    $pdo = db();
    return $pdo->query("
        SELECT o.id,
               o.amount,
               o.status,
               o.created_at,
               m.name AS username
        FROM tb_order o
        LEFT JOIN tb_member m ON o.member_id = m.id
        ORDER BY o.id DESC
    ")->fetchAll(PDO::FETCH_OBJ);
}
 
// ── Order status breakdown for charts ─────────────────────────────────────
function getOrderStatusBreakdown(): array {
    $pdo = db();
    try {
        return $pdo->query("
            SELECT status, COUNT(*) AS total
            FROM tb_order
            GROUP BY status
        ")->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        return [(object)['status' => 'pending', 'total' => 0]];
    }
}

// ── Monthly revenue (last 12 months, excluding cancelled) ─────────────────
function getMonthlyRevenue(): array {
    $pdo = db();
    try {
        return $pdo->query("
            SELECT DATE_FORMAT(created_at, '%b %Y') AS month_label,
                   DATE_FORMAT(created_at, '%Y-%m') AS month_key,
                   SUM(amount)                      AS revenue
            FROM tb_order
            WHERE status != 'cancelled'
              AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY month_key, month_label
            ORDER BY month_key ASC
        ")->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) { /* fall through */ }
 
    // Fallback: 12 months of zeroes
    $result = [];
    for ($i = 11; $i >= 0; $i--) {
        $result[] = (object)[
            'month_label' => date('M Y', strtotime("-$i months")),
            'month_key'   => date('Y-m',  strtotime("-$i months")),
            'revenue'     => 0,
        ];
    }
    return $result;
}

function updateOrderStatus($order_id, $status) {
    $db = db();

    $prev = $db->prepare("SELECT status FROM tb_order WHERE id = ?");
    $prev->execute([$order_id]);
    $old_status = $prev->fetchColumn();

    $stmt = $db->prepare("UPDATE tb_order SET status = ? WHERE id = ?");
    $stmt->execute([$status, $order_id]);

    if ($old_status === 'cancelled') {
        $stmtStock = $db->prepare("
            UPDATE tb_product p
            JOIN tb_order_product op ON p.id = op.product_id
            SET p.stock = p.stock - op.quantity
            WHERE op.order_id = ?
        ");
        $stmtStock->execute([$order_id]);
    }
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

function getOrderById($order_id, $member_id = null) {
    if ($member_id === null) {
        $stmt = db()->prepare("
            SELECT 
                o.id AS order_id, o.amount, o.member_id,
                m.name AS member_name, m.email AS member_email,
                o.status AS order_status, o.created_at, 
                p.status AS payment_status, p.method AS payment_method
            FROM tb_order o
            LEFT JOIN tb_payment p ON o.id = p.order_id
            LEFT JOIN tb_member m ON o.member_id = m.id
            WHERE o.id = ? ORDER BY p.id DESC LIMIT 1
        ");
        $stmt->execute([$order_id]);
    } else {
        $stmt = db()->prepare("
            SELECT 
                o.id AS order_id, o.amount, o.status AS order_status, o.created_at, 
                p.status AS payment_status, p.method AS payment_method
            FROM tb_order o
            LEFT JOIN tb_payment p ON o.id = p.order_id
            WHERE o.id = ? AND o.member_id = ? ORDER BY p.id DESC LIMIT 1
        ");
        $stmt->execute([$order_id, $member_id]);
    }
    return $stmt->fetch(PDO::FETCH_OBJ); 
}

function cancelAndRefundOrder($order_id) {
    $db = db();
    
    try {
        $db->beginTransaction();

        // 1. Update Order Status to cancelled
        $stmtOrder = $db->prepare("UPDATE tb_order SET status = 'cancelled' WHERE id = ?");
        $stmtOrder->execute([$order_id]);
        
        // 2. Update Payment Status to pending_refund (was 'refunded')
        $stmtPay = $db->prepare("UPDATE tb_payment SET status = 'pending_refund' WHERE order_id = ? AND status = 'success'");
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

function getFilteredOrderList($filters = []) {
    $baseQuery = getOrderListQuery();
    $whereClauses = [];
    $params = [];
    
    if (!empty($filters['member_id'])) {
        $whereClauses[] = "o.member_id = ?";
        $params[] = $filters['member_id'];
    }
    if (!empty($filters['order_status'])) {
        $whereClauses[] = "o.status = ?";
        $params[] = $filters['order_status'];
    }
    if (!empty($filters['payment_method'])) {
        $whereClauses[] = "(SELECT method FROM tb_payment WHERE order_id = o.id ORDER BY id DESC LIMIT 1) = ?";
        $params[] = $filters['payment_method'];
    }
        if (!empty($filters['payment_status'])) {
        $whereClauses[] = "(SELECT status FROM tb_payment WHERE order_id = o.id ORDER BY id DESC LIMIT 1) = ?";
        $params[] = $filters['payment_status'];
    }
    // Updated date range filtering
    if (!empty($filters['date_from'])) {
        $whereClauses[] = "DATE(o.created_at) >= ?";
        $params[] = $filters['date_from'];
    }
    if (!empty($filters['date_to'])) {
        $whereClauses[] = "DATE(o.created_at) <= ?";
        $params[] = $filters['date_to'];
    }
    
    if (!empty($whereClauses)) {
        $baseQuery .= " WHERE " . implode(" AND ", $whereClauses);
    }
    
    $baseQuery .= " ORDER BY o.id DESC";
    $stmt = db()->prepare($baseQuery);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

function updateOrderPaymentStatus($order_id, $status) {
    // Uses ORDER BY ... LIMIT 1 to only update the latest payment attempt for that order
    $stmt = db()->prepare("
        UPDATE tb_payment 
        SET status = ? 
        WHERE order_id = ?
        ORDER BY id DESC LIMIT 1
    ");
    $stmt->execute([$status, $order_id]); 
}