<?php
// logic/admin/category_items.php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require_once $project_root . "config.php";
require_once $project_root . "logic/auth_helper.php";
require_once $project_root . "database/category.php"; 
require_once $project_root . "database/product.php"; 

requireAdmin(); 

// 1. Process Actions (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $current_category_id = $_POST['current_category_id'] ?? null;

    // A. Single Move
    if ($action === 'move') {
        $product_id = $_POST['product_id'];
        $new_category_id = $_POST['new_category_id'];
        updateProductCategory($product_id, $new_category_id);
        redirectWith('/pages/admin/category_items.php?id=' . $current_category_id, 'success', 'Product successfully moved.');
    }
    
    // B. Batch Move
    // UPDATED: Replaced !empty() with isset() and is_numeric() to safely allow ID '0'
    if ($action === 'batch_move' && !empty($_POST['selected_products']) && isset($_POST['new_category_id']) && is_numeric($_POST['new_category_id'])) {
        $product_ids = $_POST['selected_products'];
        $new_category_id = $_POST['new_category_id'];
        
        foreach ($product_ids as $pid) {
            updateProductCategory($pid, $new_category_id);
        }
        redirectWith('/pages/admin/category_items.php?id=' . $current_category_id, 'success', count($product_ids) . ' products successfully moved.');
    }
}

// 2. Fetch Data for the UI (GET)
// UPDATED: Check isset and numeric instead of truthiness so '0' passes
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirectWith('/pages/admin/category_list.php', 'error', 'Invalid category ID.');
}

$category_id = (int)$_GET['id'];
$category = getCategoryById($category_id);
$products = getProductsByCategoryId($category_id); 
$all_categories = getAllCategories(); 

if (!$category) {
    redirectWith('/pages/admin/category_list.php', 'error', 'Category not found.');
}
?>