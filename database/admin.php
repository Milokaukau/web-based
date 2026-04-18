<?php
/**
 * database/admin.php
 * Database functions for admin profile and dashboard KPI summary.
 */

// ── Single admin record by ID ──────────────────────────────────────────────
function getAdmin(int $id): object|false {
    $pdo  = db();
    $stmt = $pdo->prepare("SELECT * FROM tb_admin WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_OBJ);
}

// ── Dashboard / charts KPI summary ────────────────────────────────────────
function getChartSummaryStats(): object {
    $pdo = db();

    $totalMembers  = (int) $pdo->query("SELECT COUNT(*) FROM tb_member")->fetchColumn();
    $totalOrders   = (int) $pdo->query("SELECT COUNT(*) FROM tb_order")->fetchColumn();
    $totalRevenue  = (float) $pdo->query("
        SELECT COALESCE(SUM(amount), 0) FROM tb_order WHERE status != 'cancelled'
    ")->fetchColumn();
    $totalProducts = (int) $pdo->query("SELECT COUNT(*) FROM tb_product")->fetchColumn();

    return (object)[
        'total_members'  => $totalMembers,
        'total_orders'   => $totalOrders,
        'total_revenue'  => $totalRevenue,
        'total_products' => $totalProducts,
    ];
}