<?php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require_once $project_root . "database/auth.php";
require_once $project_root . "logic/auth_helper.php";

$errors = [];
$MAX_ATTEMPTS = 3;
$LOCK_MINUTES = 15;

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $login      = trim($_POST['login'] ??'');
    $password   = $_POST['password'] ?? '';
    $role       = $_POST['role'] ?? 'member'; 

    if ($login === '')    $errors['login']     = 'Email is required.';
    if ($password === '') $errors['password'] = 'Password is required.';

    if(empty($errors)){
        if($role === 'admin'){

        //Admin login by email only

        $user = getAdminByEmail($login);

        if(!$user||!password_verify($password, $user->password)){
            $errors['general'] = 'Invalid email or password.';
        }else{
            loginAdmin($user);
            header("Location: /pages/admin/dashboard.php");
            exit;
        }
        }else{
            
        //Member login by email

        $user = getMemberByEmail($login);

        if(!$user){
            $errors['general'] = 'Invalid email or password.';
        }else{

                // account is locked after 3rd failed attempt
                // check if account is locked

                if(!empty($user->locked_until) && strtotime($user->locked_until) > time()){
                    $mins = ceil((strtotime($user->locked_until) - time()) / 60);
                    $errors['general'] = "Account locked. Try again in {$mins} minute(s).";
                }elseif (!password_verify($password, $user->password)){

                    // if wrong fail to login again, then lock longer haha

                    $attempts = ($user->login_attempts ?? 0) + 1;
                    if ($attempts >= $MAX_ATTEMPTS){
                        $locked_until = date('Y-m-d H:i:s', strtotime("+{$LOCK_MINUTES} minutes"));

                        lockMemberAccount($user->id, $locked_until);
                        updateMemberLoginAttempts($user->id, $attempts);
                        $errors['general'] = "Too many failed attempts. Account locked for {$LOCK_MINUTES} minutes";
                    }else{
                        updateMemberLoginAttempts($user->id, $attempts);
                        $remaining = $MAX_ATTEMPTS - $attempts;
                        $errors['general'] = "Invalid email or password. {$remaining} attempt(s) remaining.";
                    }
                }else{

                    // if correct, log in and reset locked attempts
                    resetMemberLoginAttempts($user->id);
                    loginMember($user);
                    header("Location: /pages/home.php");
                    exit;
                }
            }
        }
    }
}