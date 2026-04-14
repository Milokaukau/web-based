<?php
// database/admin.php
require_once "db.php"; 


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
 * Get a specific admin by ID.
 */
function getAdminById($id) {
    $stmt = db()->prepare("SELECT id, name, email, photo, is_superadmin, is_active FROM tb_admin WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
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