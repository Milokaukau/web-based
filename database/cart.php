<?php
require_once __DIR__ . '/db.php';

function getCartByMemberId($member_id) {
    $stmt = db()->prepare("SELECT * FROM tb_cart WHERE member_id = ?");
    $stmt->execute([$member_id]);
    return $stmt->fetchAll();
}

function cartItemExists($member_id, $product_id) {
    $stmt = db()->prepare("SELECT id FROM tb_cart WHERE member_id = ? AND product_id = ?");
    $stmt->execute([$member_id, $product_id]);
    return $stmt->fetch();
}

function insertCartItem($member_id, $product_id, $quantity) {
    $stmt = db()->prepare("INSERT INTO tb_cart (member_id, product_id, quantity) VALUES (?, ?, ?)");
    return $stmt->execute([$member_id, $product_id, $quantity]);
}

function updateCartQuantity($member_id, $product_id, $quantity) {
    $stmt = db()->prepare("UPDATE tb_cart SET quantity = ? WHERE member_id = ? AND product_id = ?");
    return $stmt->execute([$quantity, $member_id, $product_id]);
}

function incrementCartQuantity($member_id, $product_id, $amount = 1) {
    $stmt = db()->prepare("UPDATE tb_cart SET quantity = quantity + ? WHERE member_id = ? AND product_id = ?");
    return $stmt->execute([$amount, $member_id, $product_id]);
}

function decrementCartQuantity($member_id, $product_id, $amount = 1) {
    $stmt = db()->prepare("UPDATE tb_cart SET quantity = quantity - ? WHERE member_id = ? AND product_id = ?");
    return $stmt->execute([$amount, $member_id, $product_id]);
}

function deleteCartItem($member_id, $product_id) {
    $stmt = db()->prepare("DELETE FROM tb_cart WHERE member_id = ? AND product_id = ?");
    return $stmt->execute([$member_id, $product_id]);
}

function clearCart($member_id) {
    $stmt = db()->prepare("DELETE FROM tb_cart WHERE member_id = ?");
    return $stmt->execute([$member_id]);
}
