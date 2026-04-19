<?php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require_once $project_root . "config.php";
require_once $project_root . "logic/auth_helper.php";
require_once $project_root . "database/admin.php"; 
require_once $project_root . "logic/admin/admin_list.php";

// Auth guards
requireAdmin();       
requireSuperAdmin();    

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOAIR — Admin Maintenance</title>
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

<!-- ── LAYOUT ─────────────────────────────────────────────────────────────── -->
<div class="layout">

    <!-- SIDEBAR -->
    <?php include $project_root . "components/admin_sidebar.php"; ?>

    <!-- CONTENT -->
    <div class="content">

        <!-- Flash message -->
        <?php if (!empty($_SESSION['flash'])): ?>
            <?php 
                // Map the dynamic 'success' or 'error' to the alert classes in admin.css
                $alertType = ($_SESSION['flash']['type'] === 'error') ? 'error' : 'success';
            ?>
            <div class="alert alert-<?= htmlspecialchars($alertType) ?>">
                <?= htmlspecialchars($_SESSION['flash']['message']) ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

            <section class="section-container">
            
            <!-- Section Header (replaces the h1 flex container) -->
                <div class="section-header" style="display: flex; justify-content: space-between; align-items: flex-end;">
                    <div>
                        <h1 class="admin-section-title">Admin Maintenance</h1>
                        <p class="admin-section-sub">Manage all registered NOAIR administrative accounts</p>
                        <div class="line"></div>
                    </div>
                    <a href="/pages/admin/admin_add.php" class="btn-primary" style="margin-bottom: 20px;">+ Add New Admin</a>
                </div>

            <!-- Table Container -->
                <div class="table-wrap" style="overflow: visible;">
                    <?php if (empty($admins)): ?>
                        <div class="empty-state"><p>No admins found.</p></div>
                    <?php else: ?>
                        <table>
                        <thead>
                            <tr>
                                <th style="width: 5%;">ID</th>
                                <th style="width: 10%;">Photo</th>
                                <th style="width: 25%;">Name</th>
                                <th style="width: 25%;">Email</th>
                                <th style="width: 15%;">Account Status</th>
                                <th style="width: 20%; text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($admins as $admin): ?>
                                <tr>
                                    <td><?= htmlspecialchars($admin['id']) ?></td>
                                    <td>
                                        <?php $photo_src = !empty($admin['photo']) ? '/uploads/admins/' . htmlspecialchars($admin['photo']) : '/assets/images/default-avatar.png'; ?>
                                        <img src="<?= $photo_src ?>" alt="Admin Photo" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 1px solid var(--border-card);">
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: var(--text-dark);">
                                            <?= htmlspecialchars($admin['name']) ?>
                                        </div>
                                        <?php if ($admin['is_superadmin'] == 1): ?>
                                            <span class="badge badge-locked" style="margin-top: 4px; padding: 2px 6px; font-size: 0.65rem;">SUPERADMIN</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="color: var(--text-muted);">
                                        <?= htmlspecialchars($admin['email']) ?>
                                    </td>
                                    <td>
                                        <?php if ($admin['is_active'] == 1): ?>
                                            <span class="badge badge-valid">Active</span>
                                        <?php else: ?>
                                            <span class="badge badge-invalid">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="display: flex; justify-content: flex-end; gap: 8px; align-items: center;">
                                            <a href="/pages/admin/admin_edit.php?id=<?= $admin['id'] ?>" class="btn-outline" style="padding: 4px 12px; font-size: 0.75rem;">Edit</a>
                                            
                                            <?php if ($admin['id'] != $_SESSION['admin_id']): ?>
                                                <div style="position: relative; display: inline-block;">
                                                    <button onclick="toggleDropdown(<?= $admin['id'] ?>)" class="btn-outline dropdown-trigger" style="padding: 4px 12px; font-size: 0.75rem; border-color: var(--border-card); color: var(--text-muted); cursor: pointer;">...</button>
                                                    
                                                    <!-- Dropdown styling modernized -->
                                                    <div id="dropdown-<?= $admin['id'] ?>" class="admin-dropdown" style="display: none; position: absolute; right: 0; top: 100%; margin-top: 6px; background: var(--bg-card); border: 1px solid var(--border-card); border-radius: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.06); min-width: 180px; z-index: 100; overflow: hidden; text-align: left;">
                                                        
                                                        <?php 
                                                        $is_inactive = ($admin['is_active'] == 0);
                                                        $disabled_attr = $is_inactive ? 'disabled' : '';
                                                        $disabled_style = $is_inactive ? 'opacity: 0.5; cursor: not-allowed;' : 'cursor: pointer;';
                                                        $disabled_title = $is_inactive ? 'title="Activate account first to change roles"' : '';
                                                        ?>

                                                        <?php if ($admin['is_superadmin'] == 0): ?>
                                                            <form action="/logic/admin/admin_action.php" method="POST" style="margin: 0;">
                                                                <input type="hidden" name="action" value="make_superadmin">
                                                                <input type="hidden" name="admin_id" value="<?= $admin['id'] ?>">
                                                                <button type="submit" <?= $disabled_attr ?> <?= $disabled_title ?> onclick="return confirm('Promote this user to Superadmin?');" style="width: 100%; text-align: left; background: transparent; border: none; border-bottom: 1px solid var(--border-card); padding: 10px 16px; font-size: 0.8rem; font-family: 'Inter', sans-serif; color: var(--text-body); transition: background 0.15s; <?= $disabled_style ?>" onmouseover="this.style.background='var(--bg-page)'" onmouseout="this.style.background='transparent'">Make Superadmin</button>
                                                            </form>
                                                        <?php else: ?>
                                                            <form action="/logic/admin/admin_action.php" method="POST" style="margin: 0;">
                                                                <input type="hidden" name="action" value="remove_superadmin">
                                                                <input type="hidden" name="admin_id" value="<?= $admin['id'] ?>">
                                                                <button type="submit" <?= $disabled_attr ?> <?= $disabled_title ?> onclick="return confirm('Demote this user to regular Admin?');" style="width: 100%; text-align: left; background: transparent; border: none; border-bottom: 1px solid var(--border-card); padding: 10px 16px; font-size: 0.8rem; font-family: 'Inter', sans-serif; color: var(--warning-text); transition: background 0.15s; <?= $disabled_style ?>" onmouseover="this.style.background='var(--bg-page)'" onmouseout="this.style.background='transparent'">Remove Superadmin</button>
                                                            </form>
                                                        <?php endif; ?>
                                                        
                                                        <form action="/logic/admin/admin_action.php" method="POST" style="margin: 0;">
                                                            <input type="hidden" name="admin_id" value="<?= $admin['id'] ?>">
                                                            <?php if ($admin['is_active'] == 1): ?>
                                                                <input type="hidden" name="action" value="suspend">
                                                                <button type="submit" onclick="return confirm('DEACTIVATE this account?');" style="width: 100%; text-align: left; background: transparent; border: none; padding: 10px 16px; font-size: 0.8rem; font-family: 'Inter', sans-serif; cursor: pointer; color: var(--danger-text); transition: background 0.15s;" onmouseover="this.style.background='var(--bg-page)'" onmouseout="this.style.background='transparent'">Deactivate Account</button>
                                                            <?php else: ?>
                                                                <input type="hidden" name="action" value="activate">
                                                                <button type="submit" onclick="return confirm('ACTIVATE this account?');" style="width: 100%; text-align: left; background: transparent; border: none; padding: 10px 16px; font-size: 0.8rem; font-family: 'Inter', sans-serif; cursor: pointer; color: var(--success-text); transition: background 0.15s;" onmouseover="this.style.background='var(--bg-page)'" onmouseout="this.style.background='transparent'">Activate Account</button>
                                                            <?php endif; ?>
                                                        </form>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        </table>
                    <?php endif; ?>
                </div>

            </section>
    </div><!-- /content -->
</div><!-- /layout -->

<?php include $project_root . "components/footer.php"; ?>
<script src="/js/admin_list.js"></script>
<script src="/js/admin.js"></script>
</body>
</html>