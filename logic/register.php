<?php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require_once $project_root . "database/auth.php";
require_once $project_root . "logic/auth_helper.php";

$errors = [];
$old    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name   = trim($_POST['name']   ?? '');
    $email      = trim($_POST['email']      ?? '');
    $gender     = trim($_POST['gender']     ?? '');
    $phone      = trim($_POST['phone']      ?? '');
    $password   = $_POST['password']       ?? '';
    $password2  = $_POST['password2']      ?? '';

    $old = compact( 'name', 'email', 'gender', 'phone');

    // VALIDATION
    if ($name === '')
        $errors['name'] = 'Name is required.';

    if ($email === '')
        $errors['email'] = 'Email is required.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
        $errors['email'] = 'Invalid email format.';

    if ($gender === '')
        $errors['gender'] = 'Please select a gender.';

    if ($phone === '')
        $errors['phone'] = 'Phone number is required.';
    elseif (!preg_match('/^(\+?60|0)[0-9]{1,2}[\s\-]?[0-9]{7,8}$/', $phone))
    $errors['phone'] = 'Invalid Malaysian phone number. Example: 012-3456789 or +60123456789.';
    
    if ($password === '')
        $errors['password'] = 'Password is required.';
    elseif(strlen($password) < 8)
        $errors['password'] = 'Password must be at least 8 characters.';

    if ($password2 === '')
        $errors['password2'] = 'Please enter your password again for confirmation.';
    elseif ($password !== $password2)
        $errors['password2'] = 'Passwords do not match.';

    if (empty($errors)){
        try {
            $id = registerMember($name, $email, $password, $gender, $phone);
            redirectWith('/pages/login.php', 'success', 'Account created successfully! You can now log in.');
        } catch (PDOException $e) {
            // Check if the error code is exactly the 23000 constraint violation
            if ($e->getCode() == 23000) {
                $errors['email'] = 'This email is already registered. Please log in.';
            } else {
                // Fallback for other database errors
                $errors['general'] = 'Registration failed due to a server error. Please try again.';
            }
        }
    }

}