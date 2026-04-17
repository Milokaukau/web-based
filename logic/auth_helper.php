<?php

// SESSION HELPER

function loginMember($member){
    $_SESSION['member_id'] = $member->id;
    $_SESSION['member_name'] = $member->name;
    $_SESSION['role']        = 'member';
}

function loginAdmin($admin){
    $_SESSION['admin_id']    = $admin->id;
    $_SESSION['admin_name']  = $admin->name;
    $_SESSION['role']        = 'admin';
}

function logout(){
    session_unset();
    session_destroy();
}

function isLoggedIn(){
    return isset($_SESSION['role']);
}

function isMember(){
    return isset($_SESSION['role']) && $_SESSION['role'] === 'member';
}

function isAdmin(){
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function redirectWith($url, $type, $message){
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    header("Location: $url");
    exit;
}

// force the member to login first
function requireMember(){
    if(!isMember()){
        redirectWith('/pages/login.php', 'error', 'Please log in to continue.');
    }
}

//force admin to login also
function requireAdmin(){
    if(!isAdmin()){
        redirectWith('/pages/admin/login.php', 'error', 'Unauthorised access.');
    }
}

// photo upload
function uploadPhoto($file, $upload_dir, &$errors){
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $max_size      = 2 * 1024 * 1024; //2mb

    if($file['error'] !== UPLOAD_ERR_OK){
        $errors[] = 'Photo upload failed. Please try again.';
        return null;
    }

    if (!in_array($file['type'], $allowed_types)){
        $errors[] = 'Only JPG, PNG, GIF, WEBP images are allowed.';
        return null;
    }

    if ($file['size'] > $max_size){
        $errors[] = "Photo must be under 2MB.";
        return null;
    }

    //create upload directory if not exists
    if (!is_dir($upload_dir)){
        mkdir($upload_dir, 0755, true);
    }

    $ext        = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename   = uniqid('photo_', true) . '.' . $ext;
    $dest       = $upload_dir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)){
        $errors[] = 'Could not save photo. Please try again.';
        return null;
    }

    return $filename;

    }
