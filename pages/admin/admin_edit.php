<?php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require_once $project_root . "config.php";
require_once $project_root . "logic/auth_helper.php";
require_once $project_root . "database/admin.php"; 
require_once $project_root . "logic/admin/admin_edit.php";

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
    <title>NOAIR — Edit Admin</title>
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
        <?php if (!empty($_SESSION['flash'])): ?>
            <?php $alertType = ($_SESSION['flash']['type'] === 'error') ? 'error' : 'success'; ?>
            <div class="alert alert-<?= htmlspecialchars($alertType) ?>">
                <?= htmlspecialchars($_SESSION['flash']['message']) ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <section class="section-container" style="max-width: 600px; margin: 0 auto;">
            
            <div class="section-header">
                <a href="/pages/admin/admin_list.php" class="btn-outline" style="margin-bottom: 20px; padding: 6px 14px; font-size: 0.8rem;">← Back</a>
                <h1 class="admin-section-title">Edit Admin</h1>
                <div class="line"></div>
            </div>

            <div style="background: var(--bg-card); border: 1px solid var(--border-card); border-radius: 16px; padding: 24px; margin-bottom: 24px;">
                <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid var(--border-card);">
                    <?php $photo_src = !empty($admin_data->photo) ? '/uploads/admins/' . htmlspecialchars($admin_data->photo) : '/assets/images/default-avatar.png'; ?>
                    <img src="<?= $photo_src ?>" alt="Admin Photo" style="width: 56px; height: 56px; border-radius: 50%; object-fit: cover; border: 1px solid var(--border-card);">
                    <div>
                        <h2 style="font-size: 1.2rem; margin: 0; color: var(--text-dark);"><?= htmlspecialchars($admin_data->name) ?></h2>
                        <div style="margin-top: 6px; display: flex; gap: 8px; align-items: center;">
                            <span style="font-size: 0.75rem; color: var(--text-muted);">ID: <?= htmlspecialchars($admin_data->id) ?></span>
                            <?php if ($admin_data->is_superadmin == 1): ?>
                                <span class="badge badge-locked" style="padding: 2px 6px; font-size: 0.65rem;">SUPERADMIN</span>
                            <?php endif; ?>
                            <?php if ($admin_data->is_active == 0): ?>
                                <span class="badge badge-invalid" style="padding: 2px 6px; font-size: 0.65rem;">INACTIVE</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <form action="" method="POST">
                    <input type="hidden" name="admin_id" value="<?= htmlspecialchars($admin_data->id) ?>">

                    <div class="form-row">
                        <label for="name" style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Full Name</label>
                        <input type="text" id="name" name="name" value="<?= htmlspecialchars($_POST['name'] ?? $admin_data->name) ?>" 
                               style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-input); border-radius: 9px; background: var(--bg-input);">
                        <?php if (!empty($errors['name'])) echo "<div style='color: var(--danger-text); font-size: 0.8rem; margin-top: 5px;'>" . htmlspecialchars($errors['name']) . "</div>"; ?>
                    </div>

                    <div class="form-row">
                        <label for="email" style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Email Address</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? $admin_data->email) ?>" 
                               style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-input); border-radius: 9px; background: var(--bg-input);">
                        <?php if (!empty($errors['email'])) echo "<div style='color: var(--danger-text); font-size: 0.8rem; margin-top: 5px;'>" . htmlspecialchars($errors['email']) . "</div>"; ?>
                    </div>

                    <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
                        <button type="submit" class="btn-primary">Save Profile Changes</button>
                    </div>
                </form>
            </div>

            <?php if ($admin_data->id != $_SESSION['admin_id']): ?>
                <div style="background: var(--bg-card); border: 1px solid var(--border-card); border-radius: 16px; padding: 24px;">
                    <h3 style="font-size: 0.8rem; font-weight: 800; color: var(--text-dark); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 16px;">Account Access</h3>
                    
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <?php 
                        $is_inactive = ($admin_data->is_active == 0);
                        $disabled_attr = $is_inactive ? 'disabled' : '';
                        $disabled_style = $is_inactive ? 'opacity: 0.5; cursor: not-allowed;' : 'cursor: pointer;';
                        $disabled_title = $is_inactive ? 'title="Activate account first to change roles"' : '';
                        ?>

                        <?php if ($admin_data->is_superadmin == 0): ?>
                            <form action="/logic/admin/admin_action.php" method="POST" style="margin: 0;">
                                <input type="hidden" name="action" value="make_superadmin">
                                <input type="hidden" name="admin_id" value="<?= $admin_data->id ?>">
                                <button type="submit" class="btn-outline" <?= $disabled_attr ?> <?= $disabled_title ?> onclick="return confirm('Promote this user to Superadmin?');" style="padding: 8px 16px; border-color: var(--border-input); <?= $disabled_style ?>">Make Superadmin</button>
                            </form>
                        <?php else: ?>
                            <form action="/logic/admin/admin_action.php" method="POST" style="margin: 0;">
                                <input type="hidden" name="action" value="remove_superadmin">
                                <input type="hidden" name="admin_id" value="<?= $admin_data->id ?>">
                                <button type="submit" class="btn-outline" <?= $disabled_attr ?> <?= $disabled_title ?> onclick="return confirm('Demote this user to regular Admin?');" style="padding: 8px 16px; border-color: #FCD34D; color: #92400E; <?= $disabled_style ?>">Remove Superadmin</button>
                            </form>
                        <?php endif; ?>
                        
                        <form action="/logic/admin/admin_action.php" method="POST" style="margin: 0;">
                            <input type="hidden" name="admin_id" value="<?= $admin_data->id ?>">
                            <?php if ($admin_data->is_active == 1): ?>
                                <input type="hidden" name="action" value="suspend">
                                <button type="submit" class="btn-outline" onclick="return confirm('DEACTIVATE this account?');" style="padding: 8px 16px; border-color: #FCA5A5; color: var(--danger-text); cursor: pointer;">Deactivate Account</button>
                            <?php else: ?>
                                <input type="hidden" name="action" value="activate">
                                <button type="submit" class="btn-outline" onclick="return confirm('ACTIVATE this account?');" style="padding: 8px 16px; border-color: #86EFAC; color: var(--success-text); cursor: pointer;">Activate Account</button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

        </section>
    </div>
</div>

<?php include $project_root . "components/footer.php"; ?>
<script src="/js/admin.js"></script>
</body>
</html>