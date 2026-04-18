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

function updateOrderStatus(int $id, string $status): void {
    $stmt = db()->prepare("UPDATE tb_order SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
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