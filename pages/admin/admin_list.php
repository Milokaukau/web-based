<?php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require_once $project_root . "config.php";
require_once $project_root . "logic/auth_helper.php";
require_once $project_root . "database/admin.php"; 
require_once $project_root . "logic/admin/admin_list.php";

$_title = 'Admin Maintenance';
include $project_root . "components/header.php";
?>

<div class="container" style="max-width: 1000px; margin: 40px auto; padding: 20px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="text-transform: uppercase; letter-spacing: 1px;">Admin Maintenance</h1>
        <a href="/pages/admin/admin_add.php" class="btn btn-primary" style="padding: 10px 20px; text-decoration: none; display: inline-block;">+ Add New Admin</a>
    </div>

    <?php if (!empty($_SESSION['flash'])): ?>
        <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type']) ?>" style="margin-bottom: 20px; padding: 15px; border-radius: 4px; background-color: #d4edda; color: #155724;">
            <?= htmlspecialchars($_SESSION['flash']['message']) ?>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <div style="background: #fff; border: 1px solid var(--border-ultra-light, #eee); border-radius: 8px; overflow: hidden; padding-bottom: 100px;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead style="background-color: #fafafa; border-bottom: 2px solid #eee;">
                <tr>
                    <th style="padding: 12px 15px; font-weight: 700; color: #555; width: 5%;">ID</th>
                    <th style="padding: 12px 15px; font-weight: 700; color: #555; width: 10%;">Photo</th>
                    <th style="padding: 12px 15px; font-weight: 700; color: #555; width: 25%;">Name</th>
                    <th style="padding: 12px 15px; font-weight: 700; color: #555; width: 25%;">Email</th>
                    <th style="padding: 12px 15px; font-weight: 700; color: #555; width: 15%;">Account Status</th>
                    <th style="padding: 12px 15px; font-weight: 700; color: #555; width: 20%; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($admins)): ?>
                    <tr>
                        <td colspan="6" style="padding: 30px; text-align: center; color: #888;">No admins found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($admins as $admin): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 12px 15px; color: #555;">
                                <?= htmlspecialchars($admin['id']) ?>
                            </td>
                            <td style="padding: 12px 15px;">
                                <?php $photo_src = !empty($admin['photo']) ? '/uploads/admins/' . htmlspecialchars($admin['photo']) : '/assets/images/default-avatar.png'; ?>
                                <img src="<?= $photo_src ?>" alt="Admin Photo" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 1px solid #ccc;">
                            </td>
                            <td style="padding: 12px 15px; font-weight: 500;">
                                <?= htmlspecialchars($admin['name']) ?>
                                <?php if ($admin['is_superadmin'] == 1): ?>
                                    <br><span style="background: #cce5ff; color: #004085; padding: 2px 6px; border-radius: 4px; font-size: 0.7rem; font-weight: 700; display: inline-block; margin-top: 4px;">SUPERADMIN</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 12px 15px; color: #555; font-size: 0.9rem;">
                                <?= htmlspecialchars($admin['email']) ?>
                            </td>
                            <td style="padding: 12px 15px;">
                                <?php if ($admin['is_active'] == 1): ?>
                                    <span style="color: #28a745; font-weight: 600; font-size: 0.8rem;">● Active</span>
                                <?php else: ?>
                                    <span style="color: #dc3545; font-weight: 600; font-size: 0.8rem;">● Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 12px 15px;">
                                <div style="display: flex; justify-content: flex-end; gap: 6px; align-items: center;">
                                    
                                    <a href="/pages/admin/admin_edit.php?id=<?= $admin['id'] ?>" class="btn" style="background: transparent; border: 1px solid #ccc; color: #333; padding: 4px 10px; font-size: 0.8rem; text-decoration: none; border-radius: 4px;">Edit</a>
                                    
                                    <?php if ($admin['id'] != $_SESSION['admin_id']): ?>
                                        <div style="position: relative; display: inline-block;">
                                            <button onclick="toggleDropdown(<?= $admin['id'] ?>)" class="btn dropdown-trigger" style="background: transparent; border: 1px solid #ccc; color: #333; padding: 4px 10px; font-size: 0.8rem; border-radius: 4px; cursor: pointer;">...</button>
                                            
                                            <div id="dropdown-<?= $admin['id'] ?>" class="admin-dropdown" style="display: none; position: absolute; right: 0; top: 100%; margin-top: 4px; background: #fff; border: 1px solid #ccc; border-radius: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); min-width: 180px; z-index: 100; overflow: hidden;">
                                                
                                                <?php 
                                                // Determine if the Superadmin buttons should be disabled for this specific row
                                                $is_inactive = ($admin['is_active'] == 0);
                                                $disabled_attr = $is_inactive ? 'disabled' : '';
                                                $disabled_style = $is_inactive ? 'opacity: 0.5; cursor: not-allowed;' : 'cursor: pointer;';
                                                $disabled_title = $is_inactive ? 'title="Activate account first to change roles"' : '';
                                                ?>

                                                <?php if ($admin['is_superadmin'] == 0): ?>
                                                    <form action="/logic/admin/admin_action.php" method="POST" style="margin: 0;">
                                                        <input type="hidden" name="action" value="make_superadmin">
                                                        <input type="hidden" name="admin_id" value="<?= $admin['id'] ?>">
                                                        <button type="submit" <?= $disabled_attr ?> <?= $disabled_title ?> onclick="return confirm('Are you sure you want to promote this user to Superadmin?\n\nSuperadmins have full access to view, add, edit, and deactivate other admin accounts.');" style="width: 100%; text-align: left; background: none; border: none; border-bottom: 1px solid #eee; padding: 10px 15px; font-size: 0.85rem; <?= $disabled_style ?>">Make Superadmin</button>
                                                    </form>
                                                <?php else: ?>
                                                    <form action="/logic/admin/admin_action.php" method="POST" style="margin: 0;">
                                                        <input type="hidden" name="action" value="remove_superadmin">
                                                        <input type="hidden" name="admin_id" value="<?= $admin['id'] ?>">
                                                        <button type="submit" <?= $disabled_attr ?> <?= $disabled_title ?> onclick="return confirm('Are you sure you want to demote this user?\n\nThey will become a regular Admin and will lose access to manage other admin accounts.');" style="width: 100%; text-align: left; background: none; border: none; border-bottom: 1px solid #eee; padding: 10px 15px; font-size: 0.85rem; color: #856404; <?= $disabled_style ?>">Remove Superadmin</button>
                                                    </form>
                                                <?php endif; ?>
                                                
                                                <form action="/logic/admin/admin_action.php" method="POST" style="margin: 0;">
                                                    <input type="hidden" name="admin_id" value="<?= $admin['id'] ?>">
                                                    <?php if ($admin['is_active'] == 1): ?>
                                                        <input type="hidden" name="action" value="suspend">
                                                        <button type="submit" onclick="return confirm('Are you sure you want to DEACTIVATE this account?\n\nThe user will be instantly logged out and blocked from logging back into the system.');" style="width: 100%; text-align: left; background: none; border: none; padding: 10px 15px; font-size: 0.85rem; cursor: pointer; color: #dc3545;">Deactivate Account</button>
                                                    <?php else: ?>
                                                        <input type="hidden" name="action" value="activate">
                                                        <button type="submit" onclick="return confirm('Are you sure you want to ACTIVATE this account?\n\nThe user will regain full access to log into the admin portal.');" style="width: 100%; text-align: left; background: none; border: none; padding: 10px 15px; font-size: 0.85rem; cursor: pointer; color: #28a745;">Activate Account</button>
                                                    <?php endif; ?>
                                                </form>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <script src="/js/admin_list.js"></script>

</div>

<?php include $project_root . "components/footer.php"; ?>