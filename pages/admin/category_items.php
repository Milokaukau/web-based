<?php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require_once $project_root . "config.php";
require_once $project_root . "logic/auth_helper.php";
require_once $project_root . "database/category.php"; 
require_once $project_root . "database/product.php"; 
require_once $project_root . "logic/admin/category_items.php";

$_title = 'Category Details';
include $project_root . "components/header.php";
?>

<div class="container" style="max-width: 1000px; margin: 40px auto; padding: 20px; padding-bottom: 100px;">
    
    <div style="margin-bottom: 20px;">
        <a href="/pages/admin/category_list.php" style="color: #666; text-decoration: none; font-size: 0.9rem;">&larr; Back to Categories</a>
    </div>

    <?php if (!empty($_SESSION['flash'])): ?>
        <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type']) ?>" style="margin-bottom: 20px; padding: 15px; border-radius: 4px; background-color: <?= $_SESSION['flash']['type'] === 'error' ? '#f8d7da' : '#d4edda' ?>; color: <?= $_SESSION['flash']['type'] === 'error' ? '#721c24' : '#155724' ?>;">
            <?= htmlspecialchars($_SESSION['flash']['message']) ?>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <div style="background: #fff; border: 1px solid var(--border-ultra-light, #eee); border-radius: 8px; padding: 30px; margin-bottom: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <h1 style="text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px;">Category Details</h1>
                <p style="margin: 5px 0; color: #555;"><strong>ID:</strong> <?= htmlspecialchars($category['id']) ?></p>
                <p style="margin: 5px 0; color: #555;"><strong>Name:</strong> <?= htmlspecialchars($category['name']) ?></p>
            </div>
            <div>
                <?php if ($category['is_active'] == 1): ?>
                    <span style="background: #d4edda; color: #155724; padding: 6px 12px; border-radius: 4px; font-size: 0.85rem; font-weight: 700; text-transform: uppercase;">Active</span>
                <?php else: ?>
                    <span style="background: #f8d7da; color: #721c24; padding: 6px 12px; border-radius: 4px; font-size: 0.85rem; font-weight: 700; text-transform: uppercase;">Inactive</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <h3 style="text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; color: #333;">Products in this Category</h3>
    
    <form id="batchForm" action="" method="POST">
        <input type="hidden" name="action" id="batchActionType" value="">
        <input type="hidden" name="current_category_id" value="<?= $category['id'] ?>">
        <input type="hidden" name="new_category_id" id="batchNewCategoryId" value="">

        <div style="background: #fff; border: 1px solid var(--border-ultra-light, #eee); border-radius: 8px;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead style="background-color: #fafafa; border-bottom: 2px solid #eee;">
                    <tr>
                        <th style="padding: 15px; width: 5%; border-top-left-radius: 8px;">
                            <input type="checkbox" id="selectAll" onclick="toggleAllCheckboxes(this)" style="cursor: pointer; width: 16px; height: 16px;">
                        </th>
                        <th style="padding: 15px 20px; font-weight: 700; color: #555; width: 10%;">ID</th>
                        <th style="padding: 15px 20px; font-weight: 700; color: #555; width: 25%;">Product Name</th>
                        <th style="padding: 15px 20px; font-weight: 700; color: #555; width: 10%;">Color</th>
                        <th style="padding: 15px 20px; font-weight: 700; color: #555; width: 15%;">Price</th>
                        <th style="padding: 15px 20px; font-weight: 700; color: #555; width: 10%;">Stock</th>
                        <th style="padding: 15px 20px; font-weight: 700; color: #555; width: 25%; text-align: right; border-top-right-radius: 8px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="7" style="padding: 30px; text-align: center; color: #888;">No products found in this category.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 15px;">
                                    <input type="checkbox" class="row-checkbox" name="selected_products[]" value="<?= $product['id'] ?>" onchange="updateBatchUI()" style="cursor: pointer; width: 16px; height: 16px;">
                                </td>
                                <td style="padding: 15px 20px; color: #555;">
                                    <?= htmlspecialchars($product['id']) ?>
                                </td>
                                <td style="padding: 15px 20px; font-weight: 500;">
                                    <?= htmlspecialchars($product['name']) ?>
                                </td>
                                <td style="padding: 15px 20px; color: #555;">
                                    <?= htmlspecialchars($product['color_name'] ?? 'No Color') ?>
                                </td>
                                <td style="padding: 15px 20px; color: #555;">
                                    $<?= number_format($product['price'], 2) ?>
                                </td>
                                <td style="padding: 15px 20px; color: #555;">
                                    <?= htmlspecialchars($product['stock']) ?>
                                </td>
                                <td style="padding: 15px 20px;">
                                    <div style="display: flex; justify-content: flex-end; gap: 8px; align-items: center;">
                                        
                                        <button type="button" class="btn" style="background: transparent; border: 1px solid #ccc; color: #333; padding: 6px 12px; font-size: 0.85rem;">Edit</button>
                                        <button type="button" class="btn" onclick="alert('Remove functionality handled by another module.'); return false;" style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 6px 12px; font-size: 0.85rem;">Remove</button>
                                        
                                        <div class="cat-dropdown-container" style="position: relative; display: inline-block;">
                                            <button type="button" class="btn" onclick="toggleCatDropdown(event, <?= $product['id'] ?>)" style="background: #e2e3e5; border: 1px solid #d6d8db; color: #383d41; padding: 6px 12px; font-size: 0.85rem; cursor: pointer;">Change Category</button>
                                            
                                            <div id="cat-dropdown-<?= $product['id'] ?>" class="category-dropdown" style="display: none; position: absolute; right: 0; top: 100%; margin-top: 4px; background: #fff; border: 1px solid #ccc; border-radius: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); width: 240px; z-index: 100;">
                                                <div style="padding: 10px 15px; border-bottom: 1px solid #eee;">
                                                    <div style="font-weight: 700; font-size: 0.85rem; color: #555; text-transform: uppercase; margin-bottom: 8px;">Move to..</div>
                                                    <input type="text" placeholder="Search category..." onkeyup="filterCategories('<?= $product['id'] ?>', this.value)" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 0.85rem; box-sizing: border-box;">
                                                </div>
                                                <div id="cat-list-<?= $product['id'] ?>" style="max-height: 200px; overflow-y: auto; padding: 5px 0;">
                                                    <?php foreach ($all_categories as $cat): ?>
                                                        <?php if ($cat['id'] != $category['id'] && $cat['is_active'] == 1): ?>
                                                            <div class="cat-form-item" style="margin: 0;">
                                                                <button type="button" onclick="submitSingleMove(<?= $product['id'] ?>, <?= $cat['id'] ?>, '<?= addslashes(htmlspecialchars($cat['name'])) ?>')" style="width: 100%; text-align: left; background: none; border: none; padding: 10px 15px; font-size: 0.85rem; cursor: pointer; color: #333; transition: background 0.2s;" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='none'">
                                                                    <?= htmlspecialchars($cat['name']) ?>
                                                                </button>
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                    <div class="no-cat-found" style="display: none; padding: 15px; text-align: center; color: #888; font-size: 0.85rem;">Category not found</div>
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

        <div id="batchActionBar" style="display: none; position: fixed; bottom: 0; left: 0; right: 0; background: #fff; box-shadow: 0 -4px 15px rgba(0,0,0,0.1); padding: 15px 40px; z-index: 1000; align-items: center; justify-content: space-between; border-top: 1px solid #ddd;">
            <div style="font-weight: 700; color: #333; font-size: 1.1rem;">
                <span id="selectedCountDisplay" style="background: #007bff; color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.9rem; margin-right: 8px;">0</span> Items Selected
            </div>
            
            <div style="display: flex; gap: 15px; align-items: center;">
                <button type="button" onclick="alert('Remove functionality handled by another module.'); return false;" class="btn" style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 8px 20px; font-size: 0.95rem; cursor: pointer; border-radius: 4px;">Remove Selected</button>
                
                <div class="cat-dropdown-container" style="position: relative; display: inline-block;">
                    <button type="button" onclick="toggleCatDropdown(event, 'batch')" class="btn" style="background: #e2e3e5; border: 1px solid #d6d8db; color: #383d41; padding: 8px 20px; font-size: 0.95rem; cursor: pointer; border-radius: 4px;">Change Category</button>
                    
                    <div id="cat-dropdown-batch" class="category-dropdown" style="display: none; position: absolute; right: 0; bottom: 100%; margin-bottom: 8px; background: #fff; border: 1px solid #ccc; border-radius: 4px; box-shadow: 0 -4px 12px rgba(0,0,0,0.15); width: 260px; z-index: 1001;">
                        <div style="padding: 12px 15px; border-bottom: 1px solid #eee;">
                            <div style="font-weight: 700; font-size: 0.85rem; color: #555; text-transform: uppercase; margin-bottom: 8px;">Move Selected To..</div>
                            <input type="text" placeholder="Search category..." onkeyup="filterCategories('batch', this.value)" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 0.85rem; box-sizing: border-box;">
                        </div>
                        <div id="cat-list-batch" style="max-height: 250px; overflow-y: auto; padding: 5px 0;">
                            <?php foreach ($all_categories as $cat): ?>
                                <?php if ($cat['id'] != $category['id'] && $cat['is_active'] == 1): ?>
                                    <div class="cat-form-item" style="margin: 0;">
                                        <button type="button" onclick="submitBatchMove(<?= $cat['id'] ?>, '<?= addslashes(htmlspecialchars($cat['name'])) ?>')" style="width: 100%; text-align: left; background: none; border: none; padding: 12px 15px; font-size: 0.9rem; cursor: pointer; color: #333; transition: background 0.2s;" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='none'">
                                            <?= htmlspecialchars($cat['name']) ?>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <div class="no-cat-found" style="display: none; padding: 15px; text-align: center; color: #888; font-size: 0.85rem;">Category not found</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="/js/category_items.js"></script>

<?php include $project_root . "components/footer.php"; ?>