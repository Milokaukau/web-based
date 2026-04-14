<?php
// logic/admin/category_list.php
requireAdmin();

// Changed variable name to prevent conflict with header.php
$admin_categories = getAllCategoriesWithCount(); 
?>