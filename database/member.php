<?php
require_once "db.php";

function getMemberById($id){
    $stmt = db()->prepare("SELECT * FROM tb_member WHERE id=?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}