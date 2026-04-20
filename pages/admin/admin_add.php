<?php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require_once $project_root . "config.php";
require_once $project_root . "logic/auth_helper.php";
require_once $project_root . "database/admin.php"; 
require_once $project_root . "logic/admin/admin_add.php";

if (!isAdmin()) {
    header("Location: /pages/admin/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOAIR — Add New Admin</title>
    <link rel="stylesheet" href="/css/admin.css">
</head>
<body>

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
    <!-- SIDEBAR -->
    <?php include $project_root . "components/admin_sidebar.php"; ?>

    <!-- CONTENT -->
    <div class="content">
        <section class="section-container" style="max-width: 600px; margin: 0 auto;">
            
            <div class="section-header">
                <a href="/pages/admin/admin_list.php" class="btn-outline" style="margin-bottom: 20px; padding: 6px 14px; font-size: 0.8rem;">← Back</a>
                <h1 class="admin-section-title">Add New Admin</h1>
                <p class="admin-section-sub">Create a new administrative account</p>
                <div class="line"></div>
            </div>

            <div style="background: var(--bg-card); border: 1px solid var(--border-card); border-radius: 16px; padding: 24px;">
                <form action="" method="POST">
                    
                    <div class="form-row">
                        <label for="name" style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Full Name</label>
                        <input type="text" id="name" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" 
                               style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-input); border-radius: 9px; background: var(--bg-input);">
                        <?php if (!empty($errors['name'])) echo "<div style='color: var(--danger-text); font-size: 0.8rem; margin-top: 5px;'>" . htmlspecialchars($errors['name']) . "</div>"; ?>
                    </div>

                    <div class="form-row">
                        <label for="email" style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Email Address</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" 
                               style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-input); border-radius: 9px; background: var(--bg-input);">
                        <?php if (!empty($errors['email'])) echo "<div style='color: var(--danger-text); font-size: 0.8rem; margin-top: 5px;'>" . htmlspecialchars($errors['email']) . "</div>"; ?>
                    </div>

                    <div class="form-row" style="background: var(--bg-page); padding: 16px; border-radius: 9px; border: 1px solid var(--border-card);">
                        <label style="display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 0.8rem; color: var(--text-dark); cursor: pointer; margin: 0;">
                            <input type="checkbox" name="is_superadmin" value="1" <?= isset($_POST['is_superadmin']) ? 'checked' : '' ?> style="width: 16px; height: 16px; cursor: pointer;">
                            Make this user a Superadmin
                        </label>
                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 6px; margin-left: 26px;">Superadmins have full access to view, add, edit, and deactivate other admin accounts.</div>
                    </div>

                    <div class="form-row">
                        <label for="password" style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Temporary Password</label>
                        <input type="password" id="password" name="password" minlength="8"
                               style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-input); border-radius: 9px; background: var(--bg-input);">
                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 6px;">Must be at least 8 characters.</div>
                        <?php if (!empty($errors['password'])) echo "<div style='color: var(--danger-text); font-size: 0.8rem; margin-top: 5px;'>" . htmlspecialchars($errors['password']) . "</div>"; ?>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                        <button type="submit" class="btn-primary">Create Admin</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>

<?php include $project_root . "components/admin_footer.php"; ?>
<script src="/js/admin.js"></script>
</body>
</html>