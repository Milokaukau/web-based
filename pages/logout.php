<?php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require $project_root . "config.php";
require_once $project_root . "logic/auth_helper.php";

$was_admin = isAdmin();
logout();

if ($was_admin){
    header("Location: /pages/admin/login.php");
}else{
    header("Location: /pages/login.php");
}
exit;