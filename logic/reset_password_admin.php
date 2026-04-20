<?php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require_once $project_root . "database/auth.php";
require_once $project_root . "database/profile.php";
require_once $project_root . "logic/auth_helper.php";

$errors    = [];
$token     = trim($_POST['token'] ?? $_GET['token'] ?? '');
$token_row = null;

// Validate token exists and is not expired
if ($token === '') {
    redirectWith('/pages/admin/forgot_password.php', 'error', 'Invalid or missing reset link. Please try again.');
}

$token_row = getAdminResetToken($token);

if (!$token_row) {
    redirectWith('/pages/admin/forgot_password.php', 'error', 'This reset link has expired or is invalid. Please request a new one.');
}

// Handle the new password form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_pass = $_POST['new_password']     ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if ($new_pass === '')
        $errors['new_password'] = 'New password is required.';
    elseif (strlen($new_pass) < 8)
        $errors['new_password'] = 'Password must be at least 8 characters.';

    if ($confirm === '')
        $errors['confirm_password'] = 'Please confirm your new password.';
    elseif ($new_pass !== $confirm)
        $errors['confirm_password'] = 'Passwords do not match.';

    if (empty($errors)) {
        $admin = getAdminById($token_row->admin_id);
        if ($admin && password_verify($new_pass, $admin->password)) {
            $errors['new_password'] = 'New password must be different from your current password.';
        }
    }

    if (empty($errors)) {
        resetAdminPassword($token_row->admin_id, password_hash($new_pass, PASSWORD_DEFAULT));
        deleteAdminResetToken($token);
        redirectWith('/pages/admin/login.php', 'success', 'Password reset successfully. You can now log in.');
    }
}