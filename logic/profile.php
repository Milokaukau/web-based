<?php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require_once $project_root . "database/profile.php";
require_once $project_root . "database/auth.php";
require_once $project_root . "logic/auth_helper.php";

requireMember();

$errors = [];
$member = getMemberById($_SESSION['member_id']);

if (isset($_POST['update_profile'])){
    $name   = trim($_POST['name']   ?? '');
    $email      = trim($_POST['email']      ?? '');
    $gender     = trim($_POST['gender']     ?? '');
    $phone      = trim($_POST['phone']      ?? '');

    if ($name === '')
        $errors['name'] = 'Full name is required.';

    if ($email === '')
        $errors['email'] = 'Email is required.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
        $errors['email'] = 'Invalid email format.';

    if ($gender === '')
        $errors['gender'] = 'Please select a gender.';

    if ($phone === '')
        $errors['phone'] = 'Phone number is required.';
    elseif (!preg_match('/^[0-9+\-\s]{7-15}$/', $phone))
        $errors['phone'] = 'Invalid phone number format.';

    // MAKE SURE EMAIL NOT TAKEN ALREADY

    if (empty($errors['email'])){
        $existing = getMemberByEmail($email);
        if ($existing && $existing->id != $_SESSION['member_id']){
            $errors['email'] = 'Email already exists.';
        }
    }

    if(empty($errors)){
        updateMemberProfile($_SESSION['member_id'], $name, $email, $gender, $phone);
        $member = getMemberById($_SESSION['member_id']);
        $_SESSION['member_name'] = $member->name;
        redirectWith('/pages/profile.php', 'success', 'Profile updated successfully.');
    }
}

// handle password update

if (isset($_POST['update_password'])){
    $current    = $_POST['current_password'] ?? '';
    $new_pass   = $_POST['new_password']     ?? '';
    $confirm    = $_POST['confirm_password'] ?? '';

    if ($current === '')
        $errors['current_password'] = 'Current password is required.';
    elseif (!password_verify($current, $member->password)) 
        $errors['current_password'] = 'Current password is incorrect.';

    if ($new_pass === '')
        $errors['new_password'] = 'New password is required.';
    elseif (strlen($new_pass) < 8)
        $errors['new_password'] = 'Password must be at least 8 characters.';

    if ($confirm === '')
        $errors['confirm_password'] = 'Please confirm your new password.';
    elseif($new_pass !== $confirm)
        $errors['confirm_password'] = 'Passwords do not match.';

    if (empty($errors)){
        updateMemberPassword($_SESSION['member_id'], password_hash($new_pass, PASSWORD_DEFAULT));
        redirectWith('/pages/profile.php', 'success', 'Password updated successfully.');
    }
}

//handle photo upload

if (isset($_POST['update_photo'])){
    if (empty($_FILES['photo']['name'])){
        $errors['photo'] = 'Please upload a photo.';
    }else{
        $upload_dir = $project_root . 'uploads/members/';
        $filename   = uploadPhoto($_FILES['photo'], $upload_dir, $errors);

        if ($filename){
            if(!empty($member->photo) && file_exists($upload_dir . $member->photo)){
                unlink($upload_dir . $member->photo);
            }
            updateMemberPhoto($_SESSION['member_id'], $filename);
            redirectWith('/pages/profile.php', 'success', 'Photo updated successfully.');
        }
    }
}

