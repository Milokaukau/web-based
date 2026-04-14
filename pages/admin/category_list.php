<?php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require_once $project_root . "config.php";
require_once $project_root . "logic/auth_helper.php";
require_once $project_root . "database/category.php"; 
require_once $project_root . "logic/admin/category_list.php";

$_title = 'Category Maintenance';
include $project_root . "components/header.php";
// echo "<pre>";
// var_dump($categories);
// echo "</pre>";
?>

<div class="container" style="max-width: 900px; margin: 40px auto; padding: 20px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="text-transform: uppercase; letter-spacing: 1px;">Category Maintenance</h1>
        <a href="/pages/admin/category_add.php" class="btn btn-primary" style="padding: 10px 20px; text-decoration: none; display: inline-block;">+ Add New Category</a>
    </div>

    <?php if (!empty($_SESSION['flash'])): ?>
        <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type']) ?>" style="margin-bottom: 20px; padding: 15px; border-radius: 4px; background-color: #d4edda; color: #155724;">
            <?= htmlspecialchars($_SESSION['flash']['message']) ?>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <div style="background: #fff; border: 1px solid var(--border-ultra-light, #eee); border-radius: 8px; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead style="background-color: #fafafa; border-bottom: 2px solid #eee;">
                <tr>
                    <th style="padding: 15px 20px; font-weight: 700; color: #555; width: 10%;">ID</th>
                    <th style="padding: 15px 20px; font-weight: 700; color: #555; width: 35%;">Category Name</th>
                    <th style="padding: 15px 20px; font-weight: 700; color: #555; width: 15%;">Items Count</th>
                    <th style="padding: 15px 20px; font-weight: 700; color: #555; width: 10%;">Status</th>
                    <th style="padding: 15px 20px; font-weight: 700; color: #555; width: 30%; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($admin_categories)): ?>
                    <tr>
                        <td colspan="5" style="padding: 30px; text-align: center; color: #888;">No categories found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($admin_categories as $category): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 15px 20px; color: #555;">
                                <?= htmlspecialchars($category['id']) ?>
                            </td>
                            <td style="padding: 15px 20px; font-weight: 500;">
                                <?= htmlspecialchars($category['name']) ?>
                            </td>
                            
                            <td style="padding: 15px 20px; font-weight: 500; text-align: right;">
                                <?= htmlspecialchars($category['product_count']) ?>
                            </td>

                            <td style="padding: 15px 20px;">
                                <?php if ($category['is_active'] == 1): ?>
                                    <span style="background: #d4edda; color: #155724; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Active</span>
                                <?php else: ?>
                                    <span style="background: #f8d7da; color: #721c24; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Inactive</span>
                                <?php endif; ?>
                            </td>

                            <td style="padding: 15px 20px;">
                                <div style="display: flex; justify-content: flex-end; gap: 8px;">
                                    <a href="/pages/admin/category_items.php?id=<?= htmlspecialchars($category['id']) ?>" class="btn" style="background: transparent; border: 1px solid #ccc; color: #333; padding: 6px 12px; text-decoration: none; white-space: nowrap;">View Items</a>
                                    
                                    <?php if ($category['is_active'] == 1): ?>
                                        <button class="btn" style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 6px 12px; white-space: nowrap;">Deactivate</button>
                                    <?php else: ?>
                                        <button class="btn" style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 6px 12px; white-space: nowrap;">Activate</button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php include $project_root . "components/footer.php"; ?>