<?php
require_once "db.php";

function getAllCategories() {
    $stmt = db()->query("SELECT * FROM tb_category ORDER BY id ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Checks if a category with the same name already exists.
 */
function getCategoryByName($name) {
    $stmt = db()->prepare("SELECT * FROM tb_category WHERE name = ? LIMIT 1");
    $stmt->execute([$name]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Inserts a new category into the database.
 */
function insertCategory($name) {
    $pdo = db();
    $stmt = $pdo->prepare("INSERT INTO tb_category (name, is_active) VALUES (?, 1)");
    $stmt->execute([$name]);
    
    // Return the ID of the newly inserted row
    return $pdo->lastInsertId(); 
}

/**
 * Get a specific category by its ID.
 */
function getCategoryById($id) {
    $stmt = db()->prepare("SELECT * FROM tb_category WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Fetches all categories along with the count of products in each category.
 * Used for the Admin Category List.
 */
function getAllCategoriesWithCount() {
    $stmt = db()->query("
        SELECT c.*, COUNT(p.id) AS product_count 
        FROM tb_category c 
        LEFT JOIN tb_product p ON c.id = p.category_id 
        GROUP BY c.id 
        ORDER BY is_active DESC, id ASC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Update the active status of a category.
 */
function setCategoryActiveStatus($category_id, $is_active) {
    $stmt = db()->prepare("UPDATE tb_category SET is_active = ? WHERE id = ?");
    return $stmt->execute([$is_active, $category_id]);
}