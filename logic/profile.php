<?php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require_once $project_root . "database/profile.php";
require_once $project_root . "database/auth.php";
require_once $project_root . "logic/auth_helper.php";

requireMember();

$errors = [];
$member = getMemberById($_SESSION['member_id']);

// handle profile info update
if (isset($_POST['update_profile'])){
    $name   = trim($_POST['name']   ?? '');
    $email  = trim($_POST['email']  ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $phone  = trim($_POST['phone']  ?? '');

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
    elseif (!preg_match('/^(\+?60|0)[0-9]{1,2}[\s\-]?[0-9]{7,8}$/', $phone))
        $errors['phone'] = 'Invalid Malaysian phone number. Example: 012-3456789 or +60123456789.';

    // make sure email is not already taken by another member
    if (empty($errors['email'])){
        $existing = getMemberByEmail($email);
        if ($existing && $existing->id != $_SESSION['member_id']){
            $errors['email'] = 'This email is already in use.';
        }
    }

    if (empty($errors)){
        updateMemberProfile($_SESSION['member_id'], $name, $email, $gender, $phone);
        $member = getMemberById($_SESSION['member_id']);
        $_SESSION['member_name'] = $member->name;
        redirectWith('/pages/profile.php', 'success', 'Profile updated successfully.');
    }
}

// handle password update
if (isset($_POST['update_password'])){
    $current  = $_POST['current_password']  ?? '';
    $new_pass = $_POST['new_password']       ?? '';
    $confirm  = $_POST['confirm_password']   ?? '';

    if ($current === '')
        $errors['current_password'] = 'Current password is required.';
    elseif (!password_verify($current, $member->password))
        $errors['current_password'] = 'Current password is incorrect.';

    if ($new_pass === '')
        $errors['new_password'] = 'New password is required.';
    elseif (strlen($new_pass) < 8)
        $errors['new_password'] = 'Password must be at least 8 characters.';
    elseif (password_verify($new_pass, $member->password))
        $errors['new_password'] = 'New password must be different from current password.';

    if ($confirm === '')
        $errors['confirm_password'] = 'Please confirm your new password.';
    elseif ($new_pass !== $confirm)
        $errors['confirm_password'] = 'Passwords do not match.';

    if (empty($errors)){
        updateMemberPassword($_SESSION['member_id'], password_hash($new_pass, PASSWORD_DEFAULT));
        redirectWith('/pages/profile.php', 'success', 'Password updated successfully.');
    }
}

// handle photo upload
if (isset($_POST['update_photo'])){
    $upload_dir   = $project_root . 'uploads/members/';
    $webcam_data  = trim($_POST['webcam_photo'] ?? '');
    $has_file     = !empty($_FILES['photo']['name']);

    // --- WEBCAM PATH: base64 data URI submitted ---
    if (!$has_file && $webcam_data !== '') {
        // Validate format: data:image/jpeg;base64,...
        if (!preg_match('/^data:image\/(jpeg|png|gif|webp);base64,/', $webcam_data, $matches)) {
            $errors['photo'] = 'Invalid webcam image data.';
        } else {
            $ext        = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];
            $raw_base64 = preg_replace('/^data:image\/[a-z]+;base64,/', '', $webcam_data);
            $img_data   = base64_decode($raw_base64);

            if ($img_data === false || strlen($img_data) === 0) {
                $errors['photo'] = 'Failed to decode webcam image.';
            } elseif (strlen($img_data) > 2 * 1024 * 1024) {
                $errors['photo'] = 'Webcam photo exceeds 2MB limit.';
            } else {
                $filename = 'photo_' . uniqid() . '.' . $ext;
                if (file_put_contents($upload_dir . $filename, $img_data) !== false) {
                    if (!empty($member->photo) && file_exists($upload_dir . $member->photo)) {
                        unlink($upload_dir . $member->photo);
                    }
                    updateMemberPhoto($_SESSION['member_id'], $filename);
                    redirectWith('/pages/profile.php', 'success', 'Photo updated successfully.');
                } else {
                    $errors['photo'] = 'Failed to save webcam photo. Check folder permissions.';
                }
            }
        }

    // --- FILE UPLOAD PATH ---
    } elseif ($has_file) {
        $filename = uploadPhoto($_FILES['photo'], $upload_dir, $errors);

        if ($filename){
            if (!empty($member->photo) && file_exists($upload_dir . $member->photo)){
                unlink($upload_dir . $member->photo);
            }
            updateMemberPhoto($_SESSION['member_id'], $filename);
            redirectWith('/pages/profile.php', 'success', 'Photo updated successfully.');
        }

    } else {
        $errors['photo'] = 'Please select a photo or take one with your webcam.';
    }
}
