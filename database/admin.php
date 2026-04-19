<?php
require_once "db.php"; 


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

/**
 * Fetch all admins for the maintenance list.
 */
function getAllAdmins() {
    $stmt = db()->query("
        SELECT id, name, email, photo, is_superadmin, is_active 
        FROM tb_admin 
        ORDER BY id ASC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Update basic admin details (Name and Email).
 */
function updateAdmin($id, $name, $email) {
    $stmt = db()->prepare("UPDATE tb_admin SET name = ?, email = ? WHERE id = ?");
    return $stmt->execute([$name, $email, $id]);
}

/**
 * Check if an email is already used by an admin.
 */
function isAdminEmailTaken($email) {
    $stmt = db()->prepare("SELECT id FROM tb_admin WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    return $stmt->fetch() !== false; // returns true if email exists
}

/**
 * Insert a new admin into the database.
 */
function insertAdmin($name, $email, $password, $is_superadmin = 0) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = db()->prepare("INSERT INTO tb_admin (name, email, password, is_superadmin, is_active) VALUES (?, ?, ?, ?, 1)");
    return $stmt->execute([$name, $email, $hashed_password, $is_superadmin]);
}

/**
 * Update the Superadmin status of an admin account.
 */
function setAdminSuperadminStatus($admin_id, $is_superadmin) {
    $stmt = db()->prepare("UPDATE tb_admin SET is_superadmin = ? WHERE id = ?");
    return $stmt->execute([$is_superadmin, $admin_id]);
}

/**
 * Update the Active/Deactivated status of an admin account.
 */
function setAdminActiveStatus($admin_id, $is_active) {
    $stmt = db()->prepare("UPDATE tb_admin SET is_active = ? WHERE id = ?");
    return $stmt->execute([$is_active, $admin_id]);
}