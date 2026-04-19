<?php

require_once __DIR__ . '/../database/auth.php';
require_once __DIR__ . '/../database/profile.php';

define('REMEMBER_COOKIE', 'remember_token');
define('REMEMBER_DAYS',   30);

// SESSION HELPER

if (!function_exists('loginMember')) {
    function loginMember($member) {
        $_SESSION['member_id']   = $member->id;
        $_SESSION['member_name'] = $member->name;
        $_SESSION['role']        = 'member';
    }
}

if (!function_exists('loginAdmin')) {
    function loginAdmin($admin) {
        $_SESSION['admin_id']   = $admin->id;
        $_SESSION['admin_name'] = $admin->name;
        $_SESSION['role']       = 'admin';
        $_SESSION['is_superadmin'] = $admin->is_superadmin;
    }
}

// REMEMBER ME — set a secure cookie and persist token in DB
if (!function_exists('setRememberMeCookie')) {
    function setRememberMeCookie($member_id) {
        $token      = bin2hex(random_bytes(32));          // 64-char hex token
        $token_hash = hash('sha256', $token);
        $expires_at = date('Y-m-d H:i:s', strtotime('+' . REMEMBER_DAYS . ' days'));

        saveRememberToken($member_id, $token_hash, $expires_at);

        setcookie(
            REMEMBER_COOKIE,
            $token,
            [
                'expires'  => strtotime('+' . REMEMBER_DAYS . ' days'),
                'path'     => '/',
                'httponly' => true,       // JS cannot access it
                'samesite' => 'Lax',
            ]
        );
    }
}

// REMEMBER ME — check cookie on every page load (called from config.php)
if (!function_exists('checkRememberMeCookie')) {
    function checkRememberMeCookie() {
        if (isLoggedIn()) return;   // already in session, nothing to do

        $token = $_COOKIE[REMEMBER_COOKIE] ?? null;
        if (!$token) return;

        $token_hash = hash('sha256', $token);
        $row        = getRememberToken($token_hash);

        if (!$row) {
            // invalid / expired — clear the cookie
            clearRememberMeCookie();
            return;
        }

        // valid — restore the session
        $member = getMemberById($row->member_id);
        if ($member) {
            loginMember($member);
            // rotate the token for security (rolling tokens)
            deleteRememberToken($token_hash);
            setRememberMeCookie($member->id);
        } else {
            clearRememberMeCookie();
        }
    }
}

if (!function_exists('clearRememberMeCookie')) {
    function clearRememberMeCookie() {
        setcookie(REMEMBER_COOKIE, '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
}

if (!function_exists('logout')) {
    function logout() {
        // clear remember-me token from DB and cookie
        if (isset($_SESSION['member_id'])) {
            deleteAllRememberTokensForMember($_SESSION['member_id']);
        }
        clearRememberMeCookie();

        session_unset();
        session_destroy();
    }
}

if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        return isset($_SESSION['role']);
    }
}

if (!function_exists('isMember')) {
    function isMember() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'member';
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
}

function isSuperAdmin(){
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin' && isset($_SESSION['is_superadmin']) && $_SESSION['is_superadmin'] == 1;
}

if (!function_exists('redirectWith')) {
    function redirectWith($url, $type, $message){
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
        header("Location: $url");
        exit;
    }
}

// force the member to login first
if (!function_exists('requireMember')) {
    function requireMember() {
        if (!isMember()) {
            redirectWith('/pages/login.php', 'error', 'Please log in to continue.');
        }
    }
}

// force admin to login also
if (!function_exists('requireAdmin')) {
    function requireAdmin() {
        if (!isAdmin()) {
            redirectWith('/pages/admin/login.php', 'error', 'Unauthorised access.');
        }
    }
}

function requireSuperAdmin(){
    if(!isSuperAdmin()){
        header("Location: /pages/not_found.php");
        exit;
    }
}

// photo upload
if (!function_exists('uploadPhoto')) {
    function uploadPhoto($file, $upload_dir, &$errors) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size      = 2 * 1024 * 1024; // 2mb

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Photo upload failed. Please try again.';
            return null;
        }

        if (!in_array($file['type'], $allowed_types)) {
            $errors[] = 'Only JPG, PNG, GIF, WEBP images are allowed.';
            return null;
        }

        if ($file['size'] > $max_size) {
            $errors[] = "Photo must be under 2MB.";
            return null;
        }

        // create upload directory if not exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('photo_', true) . '.' . $ext;
        $dest     = $upload_dir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            $errors[] = 'Could not save photo. Please try again.';
            return null;
        }

        return $filename;
    }
}

