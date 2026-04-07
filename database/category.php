<?php
require_once "db.php";

function getAllCategories() {
    $stmt = db()->query("SELECT * FROM tb_category ORDER BY id ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
