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

/**
 * Fetch all products in a specific category (including their Color Name)
 */
function getProductsByCategoryId($category_id) {
    // We use a LEFT JOIN so products without a color still show up
    $sql = "SELECT p.*, c.name AS color_name 
            FROM tb_product p 
            LEFT JOIN tb_color c ON p.color_id = c.id 
            WHERE p.category_id = ?";
            
    $stmt = db()->prepare($sql);
    $stmt->execute([$category_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Move a product to a new category.
 */
function updateProductCategory($product_id, $new_category_id) {
    $stmt = db()->prepare("UPDATE tb_product SET category_id = ? WHERE id = ?");
    return $stmt->execute([$new_category_id, $product_id]);
}