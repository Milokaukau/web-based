<?php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require_once $project_root . "config.php";
require_once $project_root . "logic/auth_helper.php";
require_once $project_root . "database/category.php"; 
require_once $project_root . "database/product.php"; 
require_once $project_root . "logic/admin/category_items.php";

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
    <title>NOAIR — Category Details</title>
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
    <div class="sidebar">
        <div class="sidebar-section">Main</div>
        <a class="nav-link" href="/pages/admin/admin.php?page=members"><span class="nav-icon">&#128101;</span> Members</a>
        <a class="nav-link" href="/pages/admin/admin.php?page=orders"><span class="nav-icon">&#128230;</span> Orders</a>
        <a class="nav-link" href="/pages/admin/admin.php?page=stock"><span class="nav-icon">&#128202;</span> Stock</a>

        <div class="sidebar-section">Management</div>
        <a class="nav-link" href="/pages/admin/admin_list.php"><span class="nav-icon">&#128110;</span> Admins</a>
        <!-- Set this link as active -->
        <a class="nav-link active" href="/pages/admin/category_list.php"><span class="nav-icon">&#128193;</span> Categories</a>

        <div class="sidebar-section">Analytics</div>
        <a class="nav-link" href="/pages/admin/admin.php?page=charts"><span class="nav-icon">&#128202;</span> Data Charts</a>
        <div class="sidebar-section">Account</div>
        <a class="nav-link" href="/pages/admin/admin.php?page=profile"><span class="nav-icon">&#9881;</span> Admin Profile</a>
    </div>

    <!-- CONTENT -->
    <div class="content">

        <section class="section-container" style="max-width: 1000px; margin: 0 auto; padding-bottom: 80px;">
            
            <div class="section-header">
                <a href="/pages/admin/category_list.php" class="btn-outline" style="margin-bottom: 20px; padding: 6px 14px; font-size: 0.8rem;">← Back</a>
            </div>

            <?php if (!empty($_SESSION['flash'])): ?>
                <?php $alertType = ($_SESSION['flash']['type'] === 'error') ? 'error' : 'success'; ?>
                <div class="alert alert-<?= htmlspecialchars($alertType) ?>">
                    <?= htmlspecialchars($_SESSION['flash']['message']) ?>
                </div>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>

            <div style="background: var(--bg-card); border: 1px solid var(--border-card); border-radius: 16px; padding: 24px; margin-bottom: 28px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <h1 class="admin-section-title" style="margin-bottom: 6px;">Category Details</h1>
                        <p style="margin: 0; color: var(--text-muted); font-size: 0.85rem;"><strong>ID:</strong> <?= htmlspecialchars($category['id']) ?></p>
                        <h2 style="margin: 8px 0 0; color: var(--text-dark); font-weight: 800; font-size: 1.4rem; letter-spacing: -0.5px;">
                            <?= htmlspecialchars($category['name']) ?>
                        </h2>
                    </div>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <?php if ($category['is_active'] == 1): ?>
                            <span class="badge badge-valid" style="padding: 6px 12px;">Active</span>
                            <?php if ($category['id'] != 0): ?>
                                <form action="" method="POST" style="margin: 0;">
                                    <input type="hidden" name="action" value="deactivate_category">
                                    <input type="hidden" name="current_category_id" value="<?= $category['id'] ?>">
                                    <input type="hidden" name="product_count" value="<?= count($products) ?>">
                                    <button type="submit" onclick="return confirmCategoryDeactivate(<?= count($products) ?>);" class="btn-outline" style="border-color: #FCA5A5; color: var(--danger-text);">Deactivate</button>
                                </form>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="badge badge-invalid" style="padding: 6px 12px;">Inactive</span>
                            <?php if ($category['id'] != 0): ?>
                                <form action="" method="POST" style="margin: 0;">
                                    <input type="hidden" name="action" value="activate_category">
                                    <input type="hidden" name="current_category_id" value="<?= $category['id'] ?>">
                                    <button type="submit" onclick="return confirm('Activate this category?');" class="btn-outline" style="border-color: #86EFAC; color: var(--success-text);">Activate</button>
                                </form>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <h3 style="font-size: 0.85rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 16px;">Products in this Category</h3>
            
            <form id="batchForm" action="" method="POST">
                <input type="hidden" name="action" id="batchActionType" value="">
                <input type="hidden" name="current_category_id" value="<?= $category['id'] ?>">
                <input type="hidden" name="new_category_id" id="batchNewCategoryId" value="">

                <div class="table-wrap" style="overflow: visible;">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 5%;">
                                    <input type="checkbox" id="selectAll" onclick="toggleAllCheckboxes(this)" style="cursor: pointer;">
                                </th>
                                <th style="width: 10%;">ID</th>
                                <th style="width: 25%;">Product Name</th>
                                <th style="width: 10%;">Color</th>
                                <th style="width: 15%;">Price</th>
                                <th style="width: 10%;">Stock</th>
                                <th style="width: 25%; text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($products)): ?>
                                <tr>
                                    <td colspan="7"><div class="empty-state">No products found in this category.</div></td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="row-checkbox" name="selected_products[]" value="<?= $product['id'] ?>" onchange="updateBatchUI()" style="cursor: pointer;">
                                        </td>
                                        <td style="color: var(--text-muted);">
                                            <?= htmlspecialchars($product['id']) ?>
                                        </td>
                                        <td style="font-weight: 600; color: var(--text-dark);">
                                            <?= htmlspecialchars($product['name']) ?>
                                        </td>
                                        <td style="color: var(--text-muted);">
                                            <?= htmlspecialchars($product['color_name'] ?? 'No Color') ?>
                                        </td>
                                        <td style="font-weight: 600; color: var(--coral);">
                                            RM <?= number_format($product['price'], 2) ?>
                                        </td>
                                        <td>
                                            <?php if ($product['stock'] <= 5): ?>
                                                <span class="badge badge-locked"><?= htmlspecialchars($product['stock']) ?> Left</span>
                                            <?php else: ?>
                                                <span style="color: var(--text-body); font-weight: 500; font-size: 0.85rem;"><?= htmlspecialchars($product['stock']) ?> units</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div style="display: flex; justify-content: flex-end; gap: 8px; align-items: center;">
                                                <button type="button" class="btn-outline" style="padding: 4px 10px; font-size: 0.75rem;">Edit</button>
                                                <button type="button" class="btn-outline" onclick="alert('Remove functionality handled by another module.'); return false;" style="padding: 4px 10px; font-size: 0.75rem; border-color: #FCA5A5; color: var(--danger-text);">Remove</button>
                                                
                                                <div class="cat-dropdown-container" style="position: relative; display: inline-block;">
                                                    <button type="button" class="btn-outline dropdown-trigger" onclick="toggleCatDropdown(event, <?= $product['id'] ?>)" style="padding: 4px 10px; font-size: 0.75rem; border-color: var(--border-input); color: var(--text-body); cursor: pointer;">Change Cat</button>
                                                    
                                                    <div id="cat-dropdown-<?= $product['id'] ?>" class="category-dropdown" style="display: none; position: absolute; right: 0; top: 100%; margin-top: 6px; background: var(--bg-card); border: 1px solid var(--border-card); border-radius: 9px; box-shadow: 0 4px 16px rgba(0,0,0,0.06); width: 240px; z-index: 100; text-align: left;">
                                                        <div style="padding: 12px; border-bottom: 1px solid var(--border-card);">
                                                            <div style="font-weight: 800; font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px;">Move to..</div>
                                                            <input type="text" placeholder="Search category..." onkeyup="filterCategories('<?= $product['id'] ?>', this.value)" style="width: 100%; padding: 8px; border: 1px solid var(--border-input); border-radius: 6px; font-size: 0.8rem; outline: none; transition: box-shadow 0.2s;">
                                                        </div>
                                                        <div id="cat-list-<?= $product['id'] ?>" style="max-height: 200px; overflow-y: auto; padding: 4px 0;">
                                                            <?php foreach ($all_categories as $cat): ?>
                                                                <?php if ($cat['id'] != $category['id'] && $cat['is_active'] == 1): ?>
                                                                    <div class="cat-form-item" style="margin: 0;">
                                                                        <button type="button" onclick="submitSingleMove(<?= $product['id'] ?>, <?= $cat['id'] ?>, '<?= addslashes(htmlspecialchars($cat['name'])) ?>')" style="width: 100%; text-align: left; background: none; border: none; padding: 8px 16px; font-size: 0.8rem; font-family: 'Inter', sans-serif; cursor: pointer; color: var(--text-body); transition: background 0.15s;" onmouseover="this.style.background='var(--bg-page)'" onmouseout="this.style.background='none'">
                                                                            <?= htmlspecialchars($cat['name']) ?>
                                                                        </button>
                                                                    </div>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                            <div class="no-cat-found" style="display: none; padding: 16px; text-align: center; color: var(--text-muted); font-size: 0.8rem;">Category not found</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div id="batchActionBar" style="display: none; position: fixed; bottom: 0; left: 0; right: 0; background: var(--bg-card); box-shadow: 0 -4px 16px rgba(0,0,0,0.06); padding: 16px 40px; z-index: 1000; align-items: center; justify-content: space-between; border-top: 1px solid var(--border-card);">
                    <div style="font-weight: 700; color: var(--text-dark); font-size: 0.9rem;">
                        <span id="selectedCountDisplay" class="badge badge-locked" style="padding: 4px 10px; font-size: 0.9rem; margin-right: 8px; border-radius: 9999px;">0</span> Items Selected
                    </div>
                    
                    <div style="display: flex; gap: 12px; align-items: center;">
                        <button type="button" onclick="alert('Remove functionality handled by another module.'); return false;" class="btn-outline" style="padding: 8px 20px; font-size: 0.85rem; border-color: #FCA5A5; color: var(--danger-text);">Remove Selected</button>
                        
                        <div class="cat-dropdown-container" style="position: relative; display: inline-block;">
                            <button type="button" onclick="toggleCatDropdown(event, 'batch')" class="btn-outline" style="padding: 8px 20px; font-size: 0.85rem; border-color: var(--border-input); color: var(--text-dark);">Change Category</button>
                            
                            <div id="cat-dropdown-batch" class="category-dropdown" style="display: none; position: absolute; right: 0; bottom: 100%; margin-bottom: 12px; background: var(--bg-card); border: 1px solid var(--border-card); border-radius: 9px; box-shadow: 0 -4px 16px rgba(0,0,0,0.06); width: 260px; z-index: 1001; text-align: left;">
                                <div style="padding: 14px; border-bottom: 1px solid var(--border-card);">
                                    <div style="font-weight: 800; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px;">Move Selected To..</div>
                                    <input type="text" placeholder="Search category..." onkeyup="filterCategories('batch', this.value)" style="width: 100%; padding: 8px; border: 1px solid var(--border-input); border-radius: 6px; font-size: 0.85rem; outline: none;">
                                </div>
                                <div id="cat-list-batch" style="max-height: 250px; overflow-y: auto; padding: 4px 0;">
                                    <?php foreach ($all_categories as $cat): ?>
                                        <?php if ($cat['id'] != $category['id'] && $cat['is_active'] == 1): ?>
                                            <div class="cat-form-item" style="margin: 0;">
                                                <button type="button" onclick="submitBatchMove(<?= $cat['id'] ?>, '<?= addslashes(htmlspecialchars($cat['name'])) ?>')" style="width: 100%; text-align: left; background: none; border: none; padding: 10px 16px; font-size: 0.85rem; font-family: 'Inter', sans-serif; cursor: pointer; color: var(--text-body); transition: background 0.15s;" onmouseover="this.style.background='var(--bg-page)'" onmouseout="this.style.background='none'">
                                                    <?= htmlspecialchars($cat['name']) ?>
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    <div class="no-cat-found" style="display: none; padding: 16px; text-align: center; color: var(--text-muted); font-size: 0.8rem;">Category not found</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </div>
</div>

<script src="/js/category_items.js"></script>
<script src="/js/admin.js"></script>
</body>
</html>