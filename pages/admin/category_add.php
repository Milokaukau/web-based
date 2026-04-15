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

<div class="container" style="max-width: 900px; margin: 40px auto; padding: 20px;">
    
    <div style="margin-bottom: 20px;">
        <a href="/pages/admin/category_list.php" style="color: #666; text-decoration: none; font-size: 0.9rem;">&larr; Back to Categories</a>
    </div>

    <div style="background: #fff; border: 1px solid var(--border-ultra-light, #eee); border-radius: 8px; padding: 30px;">
        <h1 style="text-transform: uppercase; letter-spacing: 1px; margin-bottom: 25px;">Add New Category</h1>

        <form action="" method="POST">
            
            <div style="margin-bottom: 30px;">
                <label for="name" style="display: block; font-weight: 700; margin-bottom: 8px; font-size: 0.85rem; text-transform: uppercase;">Category Name</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($name ?? '') ?>" 
                       style="width: 100%; max-width: 500px; padding: 10px 15px; border: 1px solid <?= !empty($errors['name']) ? '#dc3545' : '#ccc' ?>; border-radius: 4px; font-family: inherit; font-size: 1rem;">
                
                <?php if (!empty($errors['name'])): ?>
                    <div style="color: #dc3545; font-size: 0.85rem; margin-top: 5px;">
                        <?= htmlspecialchars($errors['name']) ?>
                    </div>
                <?php endif; ?>
            </div>

            <div style="margin-bottom: 30px;">
                <h3 style="font-size: 1rem; margin-bottom: 15px; color: #333; border-bottom: 2px solid #eee; padding-bottom: 10px;">Add products under this category (Optional)</h3>
                
                <?php if (empty($uncategorized_products)): ?>
                    <p style="color: #666; font-size: 0.9rem; padding: 15px; background: #fafafa; border-radius: 4px; border: 1px dashed #ccc;">No uncategorized products available.</p>
                <?php else: ?>
                    <div style="border: 1px solid #eee; border-radius: 8px; overflow: hidden; max-height: 400px; overflow-y: auto;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left;">
                            <thead style="background-color: #f8f9fa; position: sticky; top: 0; z-index: 10; box-shadow: 0 1px 0 #eee;">
                                <tr>
                                    <th style="padding: 12px 15px; width: 5%;">
                                        <input type="checkbox" onclick="toggleAllCheckboxes(this)" style="cursor: pointer; width: 16px; height: 16px;">
                                    </th>
                                    <th style="padding: 12px 15px; font-weight: 700; color: #555; width: 10%;">ID</th>
                                    <th style="padding: 12px 15px; font-weight: 700; color: #555; width: 45%;">Product Name</th>
                                    <th style="padding: 12px 15px; font-weight: 700; color: #555; width: 20%;">Color</th>
                                    <th style="padding: 12px 15px; font-weight: 700; color: #555; width: 20%;">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($uncategorized_products as $product): ?>
                                    <tr style="border-bottom: 1px solid #eee;">
                                        <td style="padding: 12px 15px;">
                                            <input type="checkbox" class="row-checkbox" name="selected_products[]" value="<?= $product['id'] ?>" style="cursor: pointer; width: 16px; height: 16px;">
                                        </td>
                                        <td style="padding: 12px 15px; color: #555;">
                                            <?= htmlspecialchars($product['id']) ?>
                                        </td>
                                        <td style="padding: 12px 15px; font-weight: 500;">
                                            <?= htmlspecialchars($product['name']) ?>
                                        </td>
                                        <td style="padding: 12px 15px; color: #555;">
                                            <?= htmlspecialchars($product['color_name'] ?? 'No Color') ?>
                                        </td>
                                        <td style="padding: 12px 15px; color: #555;">
                                            $<?= number_format($product['price'], 2) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 30px;">
                <a href="/pages/admin/category_list.php" class="btn" style="background: transparent; color: #555; border: 1px solid #ccc; padding: 10px 20px; text-decoration: none;">Cancel</a>
                <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">Add Category</button>
            </div>
        </form>
    </div>

</div>

<script>
function toggleAllCheckboxes(source) {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => cb.checked = source.checked);
}
</script>

<?php include $project_root . "components/footer.php"; ?>