<?php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require_once $project_root . "config.php";
require_once $project_root . "logic/auth_helper.php";
require_once $project_root . "database/category.php"; 
require_once $project_root . "logic/admin/category_list.php";

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
    <title>NOAIR — Category Maintenance</title>
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

        <section class="section-container">
            <div class="section-header" style="display: flex; justify-content: space-between; align-items: flex-end;">
                <div>
                    <h1 class="admin-section-title">Category Maintenance</h1>
                    <p class="admin-section-sub">Manage product categories and bulk actions</p>
                    <div class="line"></div>
                </div>
                <a href="/pages/admin/category_add.php" class="btn-primary" style="margin-bottom: 20px;">+ Add New Category</a>
            </div>

            <!-- ── Search & Filter Toolbar ────────────────────────────────────────── -->
            <form method="GET" action="">
                <div class="toolbar" style="margin-bottom: 20px;">
                    <select name="status" class="filter-sel" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="1" <?= (isset($_GET['status']) && $_GET['status'] === '1') ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= (isset($_GET['status']) && $_GET['status'] === '0') ? 'selected' : '' ?>>Inactive</option>
                    </select>
                    <?php if (isset($_GET['status']) && $_GET['status'] !== ''): ?>
                        <a href="category_list.php" class="btn-outline">Clear</a>
                    <?php endif; ?>
                </div>
            </form>

            <form id="batchForm" action="" method="POST">
                <input type="hidden" name="action" id="batchActionType" value="">

                <div class="table-wrap" style="overflow: visible;">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 5%;">
                                    <input type="checkbox" id="selectAll" onclick="toggleAllCheckboxes(this)" style="cursor: pointer;">
                                </th>
                                <th style="width: 10%;">ID</th>
                                <th style="width: 35%;">Category Name</th>
                                <th style="width: 15%;">Items Count</th>
                                <th style="width: 10%;">Status</th>
                                <th style="width: 25%; text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($admin_categories)): ?>
                                <tr>
                                    <td colspan="6"><div class="empty-state">No categories found.</div></td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($admin_categories as $category): ?>
                                    <tr>
                                        <td>
                                            <?php if ($category['id'] != 0): ?>
                                                <input type="checkbox" class="row-checkbox" name="selected_categories[]" value="<?= $category['id'] ?>" data-product-count="<?= $category['product_count'] ?>" onchange="updateBatchUI()" style="cursor: pointer;">
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($category['id']) ?></td>
                                        <td style="font-weight: 600; color: var(--text-dark);">
                                            <?= htmlspecialchars($category['name']) ?>
                                        </td>
                                        <td style="color: var(--text-muted);">
                                            <?= htmlspecialchars($category['product_count']) ?>
                                        </td>
                                        <td>
                                            <?php if ($category['is_active'] == 1): ?>
                                                <span class="badge badge-valid">Active</span>
                                            <?php else: ?>
                                                <span class="badge badge-invalid">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div style="display: flex; justify-content: flex-end; gap: 8px;">
                                                <?php if ($category['is_active'] != 0): ?>
                                                    <a href="/pages/admin/category_items.php?id=<?= htmlspecialchars($category['id']) ?>" class="btn-outline" style="padding: 4px 12px; font-size: 0.75rem;">View Items</a>
                                                <?php endif; ?>
                                                
                                                <?php if ($category['id'] != 0): ?>
                                                    <?php if ($category['is_active'] == 1): ?>
                                                        <button type="button" onclick="submitSingleDeactivate(<?= $category['id'] ?>, <?= $category['product_count'] ?>)" class="btn-outline" style="padding: 4px 12px; font-size: 0.75rem; border-color: #FCA5A5; color: var(--danger-text);">Deactivate</button>
                                                    <?php else: ?>
                                                        <button type="button" onclick="submitSingleActivate(<?= $category['id'] ?>)" class="btn-outline" style="padding: 4px 12px; font-size: 0.75rem; border-color: #86EFAC; color: var(--success-text);">Activate</button>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Floating Toolbar for batch actions -->
                <div id="batchActionBar" style="display: none; position: fixed; bottom: 0; left: 0; right: 0; background: var(--bg-card); box-shadow: 0 -4px 16px rgba(0,0,0,0.06); padding: 16px 40px; z-index: 1000; align-items: center; justify-content: space-between; border-top: 1px solid var(--border-card);">
                    <div style="font-weight: 700; color: var(--text-dark); font-size: 0.9rem;">
                        <span id="selectedCountDisplay" class="badge badge-locked" style="padding: 4px 10px; font-size: 0.9rem; margin-right: 8px; border-radius: 9999px;">0</span> Categories Selected
                    </div>
                    
                    <div style="display: flex; gap: 12px; align-items: center;">
                        <button type="button" onclick="submitBatchActivate()" class="btn-outline" style="padding: 8px 20px; font-size: 0.85rem; border-color: #86EFAC; color: var(--success-text);">Activate Selected</button>
                        <button type="button" onclick="submitBatchDeactivate()" class="btn-outline" style="padding: 8px 20px; font-size: 0.85rem; border-color: #FCA5A5; color: var(--danger-text);">Deactivate Selected</button>
                    </div>
                </div>
            </form>

        </section>
    </div>
</div>

<?php include $project_root . "components/footer.php"; ?>
<script src="/js/category_list.js"></script>
<script src="/js/admin.js"></script>
</body>
</html>