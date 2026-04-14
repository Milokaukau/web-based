<?php
// pages/admin/category_items.php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require_once $project_root . "config.php";
require_once $project_root . "logic/auth_helper.php";
require_once $project_root . "database/category.php"; 
require_once $project_root . "database/product.php"; 
require_once $project_root . "logic/admin/category_items.php";

$_title = 'Category Details';
include $project_root . "components/header.php";
?>

<div class="container" style="max-width: 900px; margin: 40px auto; padding: 20px;">
    
    <div style="margin-bottom: 20px;">
        <a href="/pages/admin/category_list.php" style="color: #666; text-decoration: none; font-size: 0.9rem;">&larr; Back to Categories</a>
    </div>

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
    
    <div style="background: #fff; border: 1px solid var(--border-ultra-light, #eee); border-radius: 8px; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead style="background-color: #fafafa; border-bottom: 2px solid #eee;">
                <tr>
                    <th style="padding: 15px 20px; font-weight: 700; color: #555; width: 10%;">ID</th>
                    <th style="padding: 15px 20px; font-weight: 700; color: #555; width: 40%;">Product Name</th>
                    <th style="padding: 15px 20px; font-weight: 700; color: #555; width: 15%;">Price</th>
                    <th style="padding: 15px 20px; font-weight: 700; color: #555; width: 15%;">Stock</th>
                    <th style="padding: 15px 20px; font-weight: 700; color: #555; width: 20%; text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="5" style="padding: 30px; text-align: center; color: #888;">No products found in this category.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 15px 20px; color: #555;">
                                <?= htmlspecialchars($product['id']) ?>
                            </td>
                            <td style="padding: 15px 20px; font-weight: 500;">
                                <?= htmlspecialchars($product['name']) ?>
                            </td>
                            <td style="padding: 15px 20px; color: #555;">
                                $<?= number_format($product['price'], 2) ?>
                            </td>
                            <td style="padding: 15px 20px; color: #555;">
                                <?= htmlspecialchars($product['stock']) ?>
                            </td>
                            <td style="padding: 15px 20px;">
                                <div style="display: flex; justify-content: flex-end; gap: 8px;">
                                    <button class="btn" style="background: transparent; border: 1px solid #ccc; color: #333; padding: 5px 10px;">Edit</button>
                                    <button class="btn" style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 5px 10px;">Remove</button>
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