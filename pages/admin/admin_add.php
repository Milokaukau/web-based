<?php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require_once $project_root . "config.php";
require_once $project_root . "logic/auth_helper.php";
require_once $project_root . "database/admin.php"; 
require_once $project_root . "logic/admin/admin_add.php";

$_title = 'Add New Admin';
include $project_root . "components/header.php";
?>

<div class="container" style="max-width: 600px; margin: 40px auto; padding: 20px;">
    
    <div style="margin-bottom: 20px;">
        <a href="/pages/admin/admin_list.php" style="color: #666; text-decoration: none; font-size: 0.9rem;">&larr; Back to Admin List</a>
    </div>

    <div style="background: #fff; border: 1px solid var(--border-ultra-light, #eee); border-radius: 8px; padding: 30px;">
        <h1 style="text-transform: uppercase; letter-spacing: 1px; margin-bottom: 25px;">Add New Admin</h1>

        <form action="" method="POST">
            <div style="margin-bottom: 20px;">
                <label for="name" style="display: block; font-weight: 700; margin-bottom: 8px; font-size: 0.85rem; text-transform: uppercase;">Full Name</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" style="width: 100%; padding: 10px 15px; border: 1px solid <?= !empty($errors['name']) ? '#dc3545' : '#ccc' ?>; border-radius: 4px; font-family: inherit; font-size: 1rem;">
                <?php if (!empty($errors['name'])) echo "<div style='color: #dc3545; font-size: 0.85rem; margin-top: 5px;'>" . htmlspecialchars($errors['name']) . "</div>"; ?>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="email" style="display: block; font-weight: 700; margin-bottom: 8px; font-size: 0.85rem; text-transform: uppercase;">Email Address</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" style="width: 100%; padding: 10px 15px; border: 1px solid <?= !empty($errors['email']) ? '#dc3545' : '#ccc' ?>; border-radius: 4px; font-family: inherit; font-size: 1rem;">
                <?php if (!empty($errors['email'])) echo "<div style='color: #dc3545; font-size: 0.85rem; margin-top: 5px;'>" . htmlspecialchars($errors['email']) . "</div>"; ?>
            </div>

            <div style="margin-bottom: 20px; background: #f8f9fa; padding: 15px; border-radius: 4px; border: 1px solid #e9ecef;">
                <label style="display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; cursor: pointer; margin: 0;">
                    <input type="checkbox" name="is_superadmin" value="1" <?= isset($_POST['is_superadmin']) ? 'checked' : '' ?> style="width: 18px; height: 18px; cursor: pointer;">
                    Make this user a Superadmin
                </label>
                <div style="font-size: 0.8rem; color: #666; margin-top: 5px; margin-left: 28px;">Superadmins have full access to view, add, edit, and deactivate other admin accounts.</div>
            </div>

            <div style="margin-bottom: 30px;">
                <label for="password" style="display: block; font-weight: 700; margin-bottom: 8px; font-size: 0.85rem; text-transform: uppercase;">Temporary Password</label>
                <input type="password" id="password" name="password" style="width: 100%; padding: 10px 15px; border: 1px solid <?= !empty($errors['password']) ? '#dc3545' : '#ccc' ?>; border-radius: 4px; font-family: inherit; font-size: 1rem;">
                <div style="font-size: 0.8rem; color: #888; margin-top: 5px;">Must be at least 6 characters.</div>
                <?php if (!empty($errors['password'])) echo "<div style='color: #dc3545; font-size: 0.85rem; margin-top: 5px;'>" . htmlspecialchars($errors['password']) . "</div>"; ?>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <a href="/pages/admin/admin_list.php" class="btn" style="background: transparent; color: #555; border: 1px solid #ccc; padding: 10px 20px; text-decoration: none;">Cancel</a>
                <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">Create Admin</button>
            </div>
        </form>
    </div>

</div>

<?php include $project_root . "components/footer.php"; ?>