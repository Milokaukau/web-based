<?php
// pages/admin/category_add.php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require_once $project_root . "config.php";
require_once $project_root . "logic/auth_helper.php";
require_once $project_root . "database/category.php"; 
require_once $project_root . "logic/admin/category_add.php";

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
    <title>NOAIR — Add New Category</title>
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
        <section class="section-container" style="max-width: 800px; margin: 0 auto;">
            
            <div class="section-header">
                <a href="/pages/admin/category_list.php" class="btn-outline" style="margin-bottom: 20px; padding: 6px 14px; font-size: 0.8rem;">← Back</a>
                <h1 class="admin-section-title">Add New Category</h1>
                <p class="admin-section-sub">Create a new product grouping</p>
                <div class="line"></div>
            </div>

            <div style="background: var(--bg-card); border: 1px solid var(--border-card); border-radius: 16px; padding: 24px;">
                <form action="" method="POST">
                    
                    <div class="form-row" style="margin-bottom: 32px;">
                        <label for="name" style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Category Name</label>
                        <input type="text" id="name" name="name" value="<?= htmlspecialchars($name ?? '') ?>" 
                               style="width: 100%; max-width: 500px; padding: 10px 14px; border: 1px solid var(--border-input); border-radius: 9px; background: var(--bg-input);">
                        
                        <?php if (!empty($errors['name'])): ?>
                            <div style="color: var(--danger-text); font-size: 0.8rem; margin-top: 5px;">
                                <?= htmlspecialchars($errors['name']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div style="margin-bottom: 32px;">
                        <h3 style="font-size: 0.9rem; font-weight: 800; color: var(--text-dark); text-transform: uppercase; border-bottom: 2px solid var(--border-card); padding-bottom: 8px; margin-bottom: 16px;">Add Products Under This Category (Optional)</h3>
                        
                        <?php if (empty($uncategorized_products)): ?>
                            <div class="empty-state" style="padding: 24px; background: var(--bg-page); border: 1px dashed var(--border-input); border-radius: 9px;">
                                No uncategorized products available.
                            </div>
                        <?php else: ?>
                            <div class="table-wrap" style="max-height: 400px; overflow-y: auto; overflow-x: visible; border-radius: 9px;">
                                <table>
                                    <thead style="position: sticky; top: 0; z-index: 10;">
                                        <tr>
                                            <th style="width: 5%;"><input type="checkbox" onclick="toggleAllCheckboxes(this)" style="cursor: pointer;"></th>
                                            <th style="width: 10%;">ID</th>
                                            <th style="width: 45%;">Product Name</th>
                                            <th style="width: 20%;">Color</th>
                                            <th style="width: 20%;">Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($uncategorized_products as $product): ?>
                                            <tr>
                                                <td><input type="checkbox" class="row-checkbox" name="selected_products[]" value="<?= $product['id'] ?>" style="cursor: pointer;"></td>
                                                <td><?= htmlspecialchars($product['id']) ?></td>
                                                <td style="font-weight: 600; color: var(--text-dark);"><?= htmlspecialchars($product['name']) ?></td>
                                                <td style="color: var(--text-muted);"><?= htmlspecialchars($product['color_name'] ?? 'No Color') ?></td>
                                                <td style="color: var(--text-muted);">RM <?= number_format($product['price'], 2) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                        <button type="submit" class="btn-primary">Add Category</button>
                    </div>
                </form>
            </div>

        </section>
    </div>
</div>

<script>
function toggleAllCheckboxes(source) {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => cb.checked = source.checked);
}
</script>

<?php include $project_root . "components/admin_footer.php"; ?>
<script src="/js/admin.js"></script>
</body>
</html>