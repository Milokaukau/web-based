<?php
require_once "db.php";

function getAllMembers(){
    $stmt = db()->query("SELECT * FROM tb_member");
    return $stmt->fetchAll();
}

function getMemberById($id){
    $stmt = db()->prepare("SELECT * FROM tb_member WHERE id=?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}