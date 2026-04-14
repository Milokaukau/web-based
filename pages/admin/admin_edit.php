<?php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require_once $project_root . "config.php";
require_once $project_root . "logic/auth_helper.php";
require_once $project_root . "database/admin.php"; 
require_once $project_root . "logic/admin/admin_edit.php";

$_title = 'Edit Admin';
include $project_root . "components/header.php";
?>

<div class="container" style="max-width: 600px; margin: 40px auto; padding: 20px;">
    
    <div style="margin-bottom: 20px;">
        <a href="/pages/admin/admin_list.php" style="color: #666; text-decoration: none; font-size: 0.9rem;">&larr; Back to Admin List</a>
    </div>

    <?php if (!empty($_SESSION['flash'])): ?>
        <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type']) ?>" style="margin-bottom: 20px; padding: 15px; border-radius: 4px; background-color: #d4edda; color: #155724;">
            <?= htmlspecialchars($_SESSION['flash']['message']) ?>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <div style="background: #fff; border: 1px solid var(--border-ultra-light, #eee); border-radius: 8px; padding: 30px; margin-bottom: 20px;">
        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 25px;">
            <?php $photo_src = !empty($admin_data['photo']) ? '/uploads/admins/' . htmlspecialchars($admin_data['photo']) : '/assets/images/default-avatar.png'; ?>
            <img src="<?= $photo_src ?>" alt="Admin Photo" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 1px solid #ccc;">
            <div>
                <h1 style="text-transform: uppercase; letter-spacing: 1px; font-size: 1.5rem; margin: 0;">Edit Admin</h1>
                <span style="color: #888; font-size: 0.85rem;">ID: <?= htmlspecialchars($admin_data['id']) ?></span>
                
                <?php if ($admin_data['is_superadmin'] == 1): ?>
                    <span style="background: #cce5ff; color: #004085; padding: 2px 6px; border-radius: 4px; font-size: 0.7rem; font-weight: 700; margin-left: 8px;">SUPERADMIN</span>
                <?php endif; ?>
                <?php if ($admin_data['is_active'] == 0): ?>
                    <span style="background: #f8d7da; color: #721c24; padding: 2px 6px; border-radius: 4px; font-size: 0.7rem; font-weight: 700; margin-left: 8px;">INACTIVE</span>
                <?php endif; ?>
            </div>
        </div>

        <form action="" method="POST">
            <input type="hidden" name="admin_id" value="<?= htmlspecialchars($admin_data['id']) ?>">

            <div style="margin-bottom: 20px;">
                <label for="name" style="display: block; font-weight: 700; margin-bottom: 8px; font-size: 0.85rem; text-transform: uppercase;">Full Name</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($_POST['name'] ?? $admin_data['name']) ?>" style="width: 100%; padding: 10px 15px; border: 1px solid <?= !empty($errors['name']) ? '#dc3545' : '#ccc' ?>; border-radius: 4px; font-family: inherit; font-size: 1rem;">
                <?php if (!empty($errors['name'])) echo "<div style='color: #dc3545; font-size: 0.85rem; margin-top: 5px;'>" . htmlspecialchars($errors['name']) . "</div>"; ?>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="email" style="display: block; font-weight: 700; margin-bottom: 8px; font-size: 0.85rem; text-transform: uppercase;">Email Address</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? $admin_data['email']) ?>" style="width: 100%; padding: 10px 15px; border: 1px solid <?= !empty($errors['email']) ? '#dc3545' : '#ccc' ?>; border-radius: 4px; font-family: inherit; font-size: 1rem;">
                <?php if (!empty($errors['email'])) echo "<div style='color: #dc3545; font-size: 0.85rem; margin-top: 5px;'>" . htmlspecialchars($errors['email']) . "</div>"; ?>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">Save Profile Changes</button>
            </div>
        </form>
    </div>

    <?php if ($admin_data['id'] != $_SESSION['admin_id']): // Don't show these actions for logged-in user ?>
        <div style="background: #fff; border: 1px solid var(--border-ultra-light, #eee); border-radius: 8px; padding: 20px;">
            <h3 style="margin-top: 0; font-size: 1rem; color: #333; text-transform: uppercase; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px;">Account Settings</h3>
            
            <div style="display: flex; gap: 10px;">
                
                <?php 
                // Determine if the Superadmin buttons should be disabled
                $is_inactive = ($admin_data['is_active'] == 0);
                $disabled_attr = $is_inactive ? 'disabled' : '';
                $disabled_style = $is_inactive ? 'opacity: 0.5; cursor: not-allowed;' : 'cursor: pointer;';
                $disabled_title = $is_inactive ? 'title="Activate account first to change roles"' : '';
                ?>

                <?php if ($admin_data['is_superadmin'] == 0): ?>
                    <form action="/logic/admin/admin_action.php" method="POST" style="margin: 0;">
                        <input type="hidden" name="action" value="make_superadmin">
                        <input type="hidden" name="admin_id" value="<?= $admin_data['id'] ?>">
                        <button type="submit" class="btn" <?= $disabled_attr ?> <?= $disabled_title ?> onclick="return confirm('Are you sure you want to promote this user to Superadmin?\n\nSuperadmins have full access to view, add, edit, and deactivate other admin accounts.');" style="background: #e2e3e5; border: 1px solid #d6d8db; color: #383d41; padding: 8px 15px; <?= $disabled_style ?>">Make Superadmin</button>
                    </form>
                <?php else: ?>
                    <form action="/logic/admin/admin_action.php" method="POST" style="margin: 0;">
                        <input type="hidden" name="action" value="remove_superadmin">
                        <input type="hidden" name="admin_id" value="<?= $admin_data['id'] ?>">
                        <button type="submit" class="btn" <?= $disabled_attr ?> <?= $disabled_title ?> onclick="return confirm('Are you sure you want to demote this user?\n\nThey will become a regular Admin and will lose access to manage other admin accounts.');" style="background: #fff3cd; border: 1px solid #ffeeba; color: #856404; padding: 8px 15px; <?= $disabled_style ?>">Remove Superadmin</button>
                    </form>
                <?php endif; ?>
                
                <form action="/logic/admin/admin_action.php" method="POST" style="margin: 0;">
                    <input type="hidden" name="admin_id" value="<?= $admin_data['id'] ?>">
                    <?php if ($admin_data['is_active'] == 1): ?>
                        <input type="hidden" name="action" value="suspend">
                        <button type="submit" class="btn" onclick="return confirm('Are you sure you want to DEACTIVATE this account?\n\nThe user will be instantly logged out and blocked from logging back into the system.');" style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 8px 15px; cursor: pointer;">Deactivate Account</button>
                    <?php else: ?>
                        <input type="hidden" name="action" value="activate">
                        <button type="submit" class="btn" onclick="return confirm('Are you sure you want to ACTIVATE this account?\n\nThe user will regain full access to log into the admin portal.');" style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 8px 15px; cursor: pointer;">Activate Account</button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    <?php endif; ?>

</div>

<?php include $project_root . "components/footer.php"; ?>