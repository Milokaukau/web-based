<?php
// logic/admin/category_list.php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require_once $project_root . "database/category.php"; 
require_once $project_root . "database/product.php"; 

requireAdmin(); 

// 1. Process Actions (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    // A. Single Actions
    if ($action === 'deactivate' && isset($_POST['category_id'])) {
        $cat_id = $_POST['category_id'];
        $product_count = isset($_POST['product_count']) ? (int)$_POST['product_count'] : 0;
        
        if ($cat_id != 0) { 
            moveAllProductsToCategory($cat_id, 0); 
            setCategoryActiveStatus($cat_id, 0);
        }
        
        $msg = ($product_count > 0) 
            ? "Category deactivated and $product_count product(s) moved to Uncategorized." 
            : "Category deactivated successfully.";
            
        redirectWith('/pages/admin/category_list.php', 'success', $msg);
    } elseif ($action === 'activate' && isset($_POST['category_id'])) {
        $cat_id = $_POST['category_id'];
        setCategoryActiveStatus($cat_id, 1);
        redirectWith('/pages/admin/category_list.php', 'success', 'Category activated.');
    }
    
    // B. Batch Actions
    if ($action === 'batch_deactivate' && !empty($_POST['selected_categories'])) {
        $total_moved = isset($_POST['total_products_moved']) ? (int)$_POST['total_products_moved'] : 0;
        
        foreach ($_POST['selected_categories'] as $cat_id) {
            if ($cat_id != 0) {
                moveAllProductsToCategory($cat_id, 0);
                setCategoryActiveStatus($cat_id, 0);
            }
        }
        
        $count_cats = count($_POST['selected_categories']);
        $msg = ($total_moved > 0)
            ? "$count_cats categories deactivated and $total_moved product(s) moved to Uncategorized."
            : "$count_cats categories deactivated successfully.";
            
        redirectWith('/pages/admin/category_list.php', 'success', $msg);
    } elseif ($action === 'batch_activate' && !empty($_POST['selected_categories'])) {
        foreach ($_POST['selected_categories'] as $cat_id) {
            setCategoryActiveStatus($cat_id, 1);
        }
        
        $count_cats = count($_POST['selected_categories']);
        redirectWith('/pages/admin/category_list.php', 'success', "$count_cats categories activated successfully.");
    }
}

// -------------------------------------------------------------
// 2. Fetch Data for the UI (GET) 
// -------------------------------------------------------------
$filters = [
    'status' => $_GET['status'] ?? ''
];
$admin_categories = getAllCategoriesWithCount($filters);
?>