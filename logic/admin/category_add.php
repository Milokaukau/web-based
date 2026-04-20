<?php
// logic/admin/category_add.php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require_once $project_root . "database/category.php"; 
require_once $project_root . "database/product.php"; 

requireAdmin();

$errors = [];
$name = '';

// Fetch all uncategorized products (Category ID: 0)
$uncategorized_products = getProductsByCategoryId(0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $name = trim($_POST['name'] ?? '');

    // Validation
    if (empty($name)) {
        $errors['name'] = 'Category name is required.';
    } else {
        // Check for duplicate
        $existing = getCategoryByName($name);
        if ($existing) {
            $errors['name'] = 'A category with this name already exists.';
        }
    }

    // If no errors, insert into DB, assign products, and redirect
    if (empty($errors)) {
        // 1. Insert the new category and get its new ID
        $new_category_id = insertCategory($name);
        
        // 2. Assign any selected uncategorized products to this new category
        $assigned_count = 0;
        if (!empty($_POST['selected_products']) && is_array($_POST['selected_products'])) {
            foreach ($_POST['selected_products'] as $product_id) {
                updateProductCategory($product_id, $new_category_id);
                $assigned_count++;
            }
        }
        
        // 3. Dynamic success message
        $msg = 'New category added successfully!';
        if ($assigned_count > 0) {
            $msg = "New category added and $assigned_count product(s) assigned to it.";
        }
        
        redirectWith('/pages/admin/category_list.php', 'success', $msg);
    }
}
?>