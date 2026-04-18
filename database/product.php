<?php
require_once "db.php";

function getProductById($id) {
    if (!$id) return null;
    $stmt = db()->prepare("SELECT p.*, cat.name AS category_name 
                            FROM tb_product p 
                            LEFT JOIN tb_category cat ON p.category_id = cat.id 
                            WHERE p.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getProductsByCategory($category_id) {
    if (!$category_id) return [];
    $stmt = db()->prepare("SELECT p.*, cat.name AS category_name 
                            FROM tb_product p 
                            LEFT JOIN tb_category cat ON p.category_id = cat.id 
                            WHERE p.category_id = ?
                            GROUP BY p.name");
    $stmt->execute([$category_id]);
    return $stmt->fetchAll();
}

function getVariantsByCategory($category_id) {
    if (!$category_id) return [];
    $stmt = db()->prepare("SELECT id, name, color_id, photo, stock FROM tb_product WHERE category_id = ?");
    $stmt->execute([$category_id]);
    return $stmt->fetchAll();
}

function getFirstProductByCategory($categoryId){
        $stmt = db()->prepare("SELECT id FROM tb_product WHERE category_id = ? LIMIT 1");
        $stmt->execute([$categoryId]);
        return $stmt->fetch();
}