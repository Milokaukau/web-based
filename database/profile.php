<?php
require_once "db.php";

// MEMBER PROFILE

function updateMemberProfile($id, $name, $email, $gender, $phone){
    $stmt = db()->prepare("
    UPDATE tb_member
    SET fullname = ?, email = ?, gender = ?, phone = ?
    WHERE id = ?
    ");
    $stmt->execute([$name, $email, $gender, $phone, $id]);
}

function updateMemberPassword($id, $hashed_password){
    $stmt = db()->prepare("UPDATE tb_member SET password = ? WHERE id = ?");
    $stmt->execute([$hashed_password, $id]);
}

function updateMemberPhoto($id, $photo){
    $stmt = db()->prepare("UPDATE tb_member SET photo = ? WHERE id = ?");
    $stmt->execute([$photo, $id]);
}

function getMemberById($id){
    $stmt = db()->prepare("SELECT * FROM tb_member WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// ADMIN PROFILE

function updateAdminProfile($id, $name, $email){
    $stmt = db()->prepare("UPDATE tb_admin SET name = ?, email = ? WHERE id = ?");
    $stmt->execute([$name, $email, $id]);
}

function updateAdminPassword($id, $hashed_password){
    $stmt = db()->prepare("UPDATE tb_admin SET password = ? WHERE id = ?");
    $stmt->execute([$hashed_password, $id]);
}

function updateAdminPhoto($id, $photo){
    $stmt = db()->prepare("UPDATE tb_admin SET photo = ? WHERE id = ?");
    $stmt->execute([$photo, $id]);
}

function getAdminById($id){
    $stmt = db()->prepare("SELECT * FROM tb_admin WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}