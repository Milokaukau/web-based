<?php
require_once __DIR__ . '/db.php';

function addToWishlist($member_id, $product_id) {
    try {
        $stmt = db()->prepare("INSERT INTO tb_wishlist (member_id, product_id) VALUES (?, ?)");
        return $stmt->execute([$member_id, $product_id]);
    } catch (PDOException $e) {
        return false; // Handle duplicate insertion errors gracefully
    }
}

function removeFromWishlist($member_id, $product_id) {
    $stmt = db()->prepare("DELETE FROM tb_wishlist WHERE member_id = ? AND product_id = ?");
    return $stmt->execute([$member_id, $product_id]);
}

function getWishlistByMemberId($member_id) {
    $stmt = db()->prepare("SELECT product_id FROM tb_wishlist WHERE member_id = ?");
    $stmt->execute([$member_id]);
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}
