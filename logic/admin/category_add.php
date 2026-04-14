<?php
// logic/admin/category_add.php
requireAdmin();

$errors = [];
$name = '';

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

    // If no errors, insert into DB and redirect
    if (empty($errors)) {
        insertCategory($name);
        // Using your redirectWith helper!
        redirectWith('/pages/admin/category_list.php', 'success', 'New category added successfully!');
    }
}
?>