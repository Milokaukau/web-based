<?php
// logic/admin/category_items.php
requireAdmin();

$category_id = $_GET['id'] ?? null;

// If no ID is provided, kick them back to the list
if (!$category_id) {
    redirectWith('/pages/admin/category_list.php', 'error', 'Invalid category ID.');
}

// Fetch category details
$category = getCategoryById($category_id);

// If the ID doesn't exist in the database, kick them back
if (!$category) {
    redirectWith('/pages/admin/category_list.php', 'error', 'Category not found.');
}

// Fetch products for this category
$products = getProductsByCategoryId($category_id);
?>