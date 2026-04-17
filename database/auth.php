<?php
require_once "db.php";

// MEMBER AUTHORIZATION

function getMemberById($id){
    $stmt = db()->prepare("SELECT * FROM tb_member WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

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

// PASSWORD RESET TOKENS

// save a reset token for a member (expires in 1 hour)
function saveResetToken($member_id, $token){
    //remove any existing token for this member first
    $stmt = db()->prepare("DELETE FROM tb_password_reset WHERE member_id = ?");
    $stmt->execute([$member_id]);

    $expires_at = date('Y-m-d H:i:s', strtotime("+15 minutes"));
    $stmt = db()->prepare("
        INSERT INTO tb_password_reset (member_id, token, expires_at)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$member_id, $token, $expires_at]);
}

// look up a valid token
function getResetToken($token){
    $now = date('Y-m-d H:i:s');
    $stmt = db()->prepare("
        SELECT * FROM tb_password_reset
        WHERE token = ? AND expires_at > ?
    ");
    $stmt->execute([$token, $now]);
    return $stmt->fetch();
}

//delete token after it is used
function deleteResetToken($token){
    $stmt = db()->prepare("DELETE FROM tb_password_reset WHERE token = ?");
    $stmt->execute([$token]);
}

//reset the member's password directly by id
function resetMemberPassword($member_id, $hashed_password){
    $stmt = db()->prepare("UPDATE tb_member SET password = ? WHERE id = ?");
    $stmt->execute([$hashed_password, $member_id]);
}

// ADMIN AUTHORIZATION

function getAdminByEmail($email){
    $stmt = db()->prepare("SELECT * FROM tb_admin WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch();
}