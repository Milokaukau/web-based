<?php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require $project_root . "config.php";
require_once $project_root . "logic/auth_helper.php";

// Redirect away if already logged in as admin
if (isAdmin()) {
    header("Location: /pages/admin/dashboard.php");
    exit;
}

require $project_root . "logic/reset_password_admin.php";

$_title = 'Admin Reset Password';
include $project_root . 'components/header.php';
?>

<div class="auth-container">
    <div class="auth-box">
        <h2 class="auth-title">Reset Password</h2>

        <p class="forgot-desc">Enter and confirm your new admin password below.</p>

        <form id="reset-form" method="POST" novalidate>
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <div class="form-group">
                <label for="new_password">New Password</label>
                <div class="password-group">
                    <input type="password" id="new_password" name="new_password"
                        placeholder="At least 8 characters"
                        class="<?= isset($errors['new_password']) ? 'input-error' : '' ?>">
                    <button type="button" class="toggle-password" data-target="new_password" title="Show/Hide password">👁</button>
                </div>
                <?php if (!empty($errors['new_password'])): ?>
                    <span class="error-msg"><?= htmlspecialchars($errors['new_password']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <div class="password-group">
                    <input type="password" id="confirm_password" name="confirm_password"
                        placeholder="Repeat your new password"
                        class="<?= isset($errors['confirm_password']) ? 'input-error' : '' ?>">
                    <button type="button" class="toggle-password" data-target="confirm_password" title="Show/hide password">👁</button>
                </div>
                <?php if (!empty($errors['confirm_password'])): ?>
                    <span class="error-msg"><?= htmlspecialchars($errors['confirm_password']) ?></span>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary btn-full">Reset Password</button>
        </form>

        <p class="auth-footer"><a href="/pages/admin/login.php">Back to Admin Login</a></p>
    </div>
</div>

<?php include $project_root . 'components/footer.php'; ?>