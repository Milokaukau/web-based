<?php
// logic/admin/admin_edit.php
requireSuperAdmin();

$admin_data = [];
$errors = [];

// 1. Process Update (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_id = $_POST['admin_id'] ?? '';
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    
    // Basic validation
    if (empty($name)) {
        $errors['name'] = 'Name is required.';
    }
    if (empty($email)) {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    }
    
    // If no errors, update the database
    if (empty($errors)) {
        updateAdmin($admin_id, $name, $email);
        redirectWith('/pages/admin/admin_list.php', 'success', 'Admin details updated successfully.');
    }
}

// 2. Fetch Admin Data for UI (GET or POST fallback)
$target_id = $_GET['id'] ?? ($_POST['admin_id'] ?? null);

if ($target_id && is_numeric($target_id)) {
    $admin_data = getAdminById($target_id);
    
    // If someone manipulates the URL to a non-existent ID
    if (!$admin_data) {
        redirectWith('/pages/admin/admin_list.php', 'error', 'Admin account not found.');
    }
} else {
    // If loaded without an ID parameter
    redirectWith('/pages/admin/admin_list.php', 'error', 'Invalid Admin ID.');
}
?>