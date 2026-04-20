<?php
// logic/admin/change_password.php
require_once $project_root . "database/admin.php";
require_once $project_root . "database/profile.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $curr     = $_POST['current_password'] ?? '';
    $new      = $_POST['new_password']     ?? '';
    $conf     = $_POST['confirm_password'] ?? '';
    $admin_id = $_SESSION['admin_id']    ?? 1;

    $errors = [];

    // Validations
    if (empty($curr)) {
        $errors['current_password'] = 'Current password is required.';
    }
    if (empty($new)) {
        $errors['new_password'] = 'New password is required.';
    } elseif (strlen($new) < 8) {
        $errors['new_password'] = 'Password must be at least 8 characters.';
    }
    if (empty($conf)) {
        $errors['confirm_password'] = 'Please confirm your new password.';
    } elseif ($new !== $conf) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        $adminHash = getAdminPasswordHash($admin_id);

        if (!$adminHash || !password_verify($curr, $adminHash)) {
            $errors['current_password'] = 'Current password is incorrect.';
        } else {
            // Success
            $hashed = password_hash($new, PASSWORD_DEFAULT);
            updateAdminPassword($admin_id, $hashed);
            
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Password updated successfully.'];
            header("Location: /pages/admin/admin.php?page=profile");
            exit;
        }
    }
    
    // Store errors AND the input data so it won't disappear
    $_SESSION['form_errors'] = $errors;
    $_SESSION['form_data'] = $_POST;
    
    header("Location: /pages/admin/change_password.php");
    exit;
}