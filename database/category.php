<?php
require_once "db.php";

function getAllCategories() {
    $stmt = db()->query("
        SELECT DISTINCT c.* 
        FROM tb_category c
        JOIN tb_product p ON c.id = p.category_id
        WHERE c.id != 0
        ORDER BY c.id ASC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
