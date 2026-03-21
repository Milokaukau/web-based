<?php
/**
 * admin_actions.php
 * Handles all admin POST/GET actions before HTML output.
 * Include at top of admin.php after auth check.
 *
 * tb_member columns: id, name, email, password, photo, address_id, gender, phone, login_attempts, locked_until
 */

require_once $_SERVER['DOCUMENT_ROOT'] . "/database/db.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/database/auth.php";

$pdo    = db();
$action = $_GET['action'] ?? '';

// ── LOCK member (set locked_until to far future) ──────────────────────────
if ($action === 'lock' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $locked_until = date('Y-m-d H:i:s', strtotime('+100 years'));
    $stmt = $pdo->prepare("UPDATE tb_member SET locked_until = ? WHERE id = ?");
    $stmt->execute([$locked_until, $id]);
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Member account has been locked.'];
    $redirect = $_GET['rpage'] ?? 'members';
    header("Location: /pages/admin/admin.php?page=$redirect");
    exit;
}

// ── UNLOCK member ─────────────────────────────────────────────────────────
if ($action === 'unlock' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("UPDATE tb_member SET locked_until = NULL, login_attempts = 0 WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Member account has been unlocked.'];
    $redirect = $_GET['rpage'] ?? 'members';
    header("Location: /pages/admin/admin.php?page=$redirect");
    exit;
}

// ── DELETE member ─────────────────────────────────────────────────────────
if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM tb_member WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Member deleted successfully.'];
    header("Location: /pages/admin/admin.php?page=members");
    exit;
}

// ── ADD member ────────────────────────────────────────────────────────────
if ($action === 'add_member' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';
    $phone    = trim($_POST['phone']    ?? '');
    $gender   = $_POST['gender']        ?? '';

    if ($name && $email && $password) {
        // Check duplicate email
        $check = $pdo->prepare("SELECT id FROM tb_member WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Email already exists.'];
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO tb_member (name, email, password, phone, gender, login_attempts)
                                    VALUES (?, ?, ?, ?, ?, 0)");
            $stmt->execute([$name, $email, $hashed, $phone ?: null, $gender ?: null]);
            $_SESSION['flash'] = ['type' => 'success', 'message' => "Member '$name' added successfully."];
        }
    } else {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Name, email and password are required.'];
    }
    header("Location: /pages/admin/admin.php?page=members");
    exit;
}

// ── CHANGE ADMIN PASSWORD ─────────────────────────────────────────────────
if ($action === 'change_pw' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $new  = $_POST['new_password']     ?? '';
    $conf = $_POST['confirm_password'] ?? '';

    if ($new && $new === $conf && strlen($new) >= 6) {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE tb_admin SET password = ? WHERE id = ?");
        $stmt->execute([$hashed, $_SESSION['admin_id'] ?? 1]);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Password updated successfully.'];
    } else {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Passwords do not match or are too short (min 6 chars).'];
    }
    header("Location: /pages/admin/admin.php?page=profile");
    exit;
}