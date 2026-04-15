<?php
// pages/admin/category_add.php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require_once $project_root . "config.php";
require_once $project_root . "logic/auth_helper.php";
require_once $project_root . "database/category.php"; 
require_once $project_root . "logic/admin/category_add.php";

$_title = 'Add New Category';
include $project_root . "components/header.php";
?>

<div class="container" style="max-width: 600px; margin: 40px auto; padding: 20px;">
    
    <div style="margin-bottom: 20px;">
        <a href="/pages/admin/category_list.php" style="color: #666; text-decoration: none; font-size: 0.9rem;">&larr; Back to Categories</a>
    </div>

    <div style="background: #fff; border: 1px solid var(--border-ultra-light, #eee); border-radius: 8px; padding: 30px;">
        <h1 style="text-transform: uppercase; letter-spacing: 1px; margin-bottom: 25px;">Add New Category</h1>

        <form action="" method="POST">
            <div style="margin-bottom: 20px;">
                <label for="name" style="display: block; font-weight: 700; margin-bottom: 8px; font-size: 0.85rem; text-transform: uppercase;">Category Name</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($name ?? '') ?>" 
                       style="width: 100%; padding: 10px 15px; border: 1px solid <?= !empty($errors['name']) ? '#dc3545' : '#ccc' ?>; border-radius: 4px; font-family: inherit; font-size: 1rem;">
                
                <?php if (!empty($errors['name'])): ?>
                    <div style="color: #dc3545; font-size: 0.85rem; margin-top: 5px;">
                        <?= htmlspecialchars($errors['name']) ?>
                    </div>
                <?php endif; ?>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 30px;">
                <a href="/pages/admin/category_list.php" class="btn" style="background: transparent; color: #555; border: 1px solid #ccc; padding: 10px 20px; text-decoration: none;">Cancel</a>
                <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">Add</button>
            </div>
        </form>
    </div>

</div>

<?php include $project_root . "components/footer.php"; ?>