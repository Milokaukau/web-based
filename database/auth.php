<?php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require_once $project_root . "config.php";
require_once "db.php";

// MEMBER AUTHORIZATION

function getMemberByEmail($email){
    $stmt = db()->prepare("SELECT * FROM tb_member WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch();
}
function registerMember($name, $email, $password, $gender, $phone){
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = db()->prepare("
    INSERT INTO tb_member (name, email, password, gender, phone)
    VALUES (?, ?, ?, ?, ?)
");
$stmt->execute([$name, $email, $hash, $gender, $phone]);
return db()->lastInsertId();
}

function updateMemberLoginAttempts($id, $attempts){
    $stmt = db()->prepare("UPDATE tb_member SET login_attempts = ? WHERE id = ?");
    $stmt->execute([$attempts, $id]);
}

function resetMemberLoginAttempts($id){
    $stmt = db()->prepare("UPDATE tb_member SET login_attempts = 0, locked_until = NULL WHERE id = ?");
    return $stmt->execute([$id]);
}

function lockMemberAccount($id, $until){
    $stmt = db()->prepare("UPDATE tb_member SET locked_until = ? WHERE id = ?");
    $stmt->execute([$until, $id]);
}

// ADMIN AUTHORIZATION

function getAdminByEmail($email){
    $stmt = db()->prepare("SELECT * FROM tb_admin WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch();
}