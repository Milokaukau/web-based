<?php
// pages/admin/change_password.php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require_once $project_root . "config.php";
require_once $project_root . "logic/auth_helper.php";

if (!isAdmin()) {
    header("Location: /pages/admin/login.php");
    exit;
}

// Intercept POST request if any
require_once $project_root . "logic/admin/change_password.php";

$pw_errors = $_SESSION['form_errors'] ?? [];
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['form_data']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password — Admin</title>
    <link rel="stylesheet" href="/css/admin.css">
</head>
<body>

<!-- ── TOPBAR ─────────────────────────────────────────────────────────────── -->
<div class="topbar">
    <div class="topbar-brand">NOAIR</div>
    <div class="topbar-right">
        <span class="topbar-clock" id="clock"></span>
        <div class="topbar-user">
            <div class="avatar-sm">AD</div>
            <span class="topbar-name"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></span>
        </div>
        <a href="/pages/logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<div class="layout">
    <?php include $project_root . "components/admin_sidebar.php"; ?>

    <div class="content">
        <section class="section-container">
            <div class="section-header">
                <a href="/pages/admin/admin.php?page=profile" class="btn-outline" style="margin-bottom:10px">← Back to Profile</a>
                <h1 class="admin-section-title">Change Password</h1>
                <div class="line"></div>
            </div>

            <!-- Added feature-card-wrap to center the card -->
            <div class="feature-card-wrap">
                <div class="feature-card" style="text-align: left; width: 100%; max-width: 450px;">
                    <form method="POST" action="/pages/admin/change_password.php" novalidate>
                        
                        <div style="margin-bottom: 16px;">
                            <!-- Restored the Forgot Password link inline with the label -->
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                <label style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted); margin: 0;">Current Password</label>
                                <a href="/pages/admin/forgot_password.php" style="font-size: 0.75rem; color: #6b7280; text-decoration: underline;">
                                    Forgot Password?
                                </a>
                            </div>
                            <div class="password-group" style="position: relative; display: flex; align-items: center;">
                                <input type="password" name="current_password" required 
                                    value="<?= htmlspecialchars($form_data['current_password'] ?? '') ?>"
                                    style="width: 100%; padding: 10px 40px 10px 14px; border: 1px solid <?= isset($pw_errors['current_password']) ? '#ef4444' : 'var(--border-input)' ?>; border-radius: 8px; outline: none;">
                                <button type="button" class="toggle-password" style="position: absolute; right: 10px; background: none; border: none; cursor: pointer; color: #888; font-size: 1.1rem; padding: 4px;">👁</button>
                            </div>
                            <?php if (!empty($pw_errors['current_password'])): ?>
                                <span class="error-msg" style="display: block; color: #ef4444; font-size: 0.8rem; margin-top: 4px;"><?= htmlspecialchars($pw_errors['current_password']) ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div style="margin-bottom: 16px;">
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px; color: var(--text-muted);">New Password (min 8 chars)</label>
                            <div class="password-group" style="position: relative; display: flex; align-items: center;">
                                <input type="password" name="new_password" required minlength="8" 
                                    value="<?= htmlspecialchars($form_data['new_password'] ?? '') ?>"
                                    style="width: 100%; padding: 10px 40px 10px 14px; border: 1px solid <?= isset($pw_errors['new_password']) ? '#ef4444' : 'var(--border-input)' ?>; border-radius: 8px; outline: none;">
                                <button type="button" class="toggle-password" style="position: absolute; right: 10px; background: none; border: none; cursor: pointer; color: #888; font-size: 1.1rem; padding: 4px;">👁</button>
                            </div>
                            <?php if (!empty($pw_errors['new_password'])): ?>
                                <span class="error-msg" style="display: block; color: #ef4444; font-size: 0.8rem; margin-top: 4px;"><?= htmlspecialchars($pw_errors['new_password']) ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div style="margin-bottom: 24px;">
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px; color: var(--text-muted);">Confirm Password</label>
                            <div class="password-group" style="position: relative; display: flex; align-items: center;">
                                <input type="password" name="confirm_password" required minlength="8" 
                                    value="<?= htmlspecialchars($form_data['confirm_password'] ?? '') ?>"
                                    style="width: 100%; padding: 10px 40px 10px 14px; border: 1px solid <?= isset($pw_errors['confirm_password']) ? '#ef4444' : 'var(--border-input)' ?>; border-radius: 8px; outline: none;">
                                <button type="button" class="toggle-password" style="position: absolute; right: 10px; background: none; border: none; cursor: pointer; color: #888; font-size: 1.1rem; padding: 4px;">👁</button>
                            </div>
                            <?php if (!empty($pw_errors['confirm_password'])): ?>
                                <span class="error-msg" style="display: block; color: #ef4444; font-size: 0.8rem; margin-top: 4px;"><?= htmlspecialchars($pw_errors['confirm_password']) ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; padding: 12px;">Update Password</button>
                    </form>
                </div>
            </div>
            
        </section>
    </div>
</div>
<?php require $project_root . "components/admin_footer.php"; ?>
<script src="/js/admin.js"></script>
</body>
</html>