<?php
// logic/admin/admin_add.php
requireSuperAdmin();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name          = trim($_POST['name'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $password      = $_POST['password'] ?? '';
    // Check if the superadmin box was checked
    $is_superadmin = isset($_POST['is_superadmin']) ? 1 : 0; 
    
    // Validation
    if (empty($name)) {
        $errors['name'] = 'Name is required.';
    }
    
    if (empty($email)) {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    } elseif (isAdminEmailTaken($email)) {
        $errors['email'] = 'This email is already associated with an admin account.';
    }

    if (empty($password)) {
        $errors['password'] = 'Password is required.';
    } elseif (strlen($password) < 8) { 
        $errors['password'] = 'Password must be at least 8 characters.'; 
    }
    
    // Insert if no errors
    if (empty($errors)) {
        insertAdmin($name, $email, $password, $is_superadmin);
        redirectWith('/pages/admin/admin_list.php', 'success', 'New admin added successfully.');
    }
}
?>