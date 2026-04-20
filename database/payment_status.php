<?php
require_once "db.php";
require_once "order.php";

/**
 * Processes a successful payment by storing the order and clearing the user's cart in the DB.
 */
function processSuccessfulPayment($member_id, $amount_total, $pm, $cart) {
    if (empty($cart)) {
        return null;
    }

    $address = '';
    $city = '';
    $postcode = '';
    
    // The pay method used in Stripe is returned in arrays by payment_method_types
    $method_to_save = $pm === 'grabpay' ? 'tng' : 'card';
    
    // Insert to tb_payment, tb_order, tb_order_product via the existing logic in order.php
    $new_order_id = insertOrder(
        $member_id, 
        $amount_total, 
        $method_to_save, 
        'success', 
        $cart, 
        $address, 
        $city, 
        $postcode
    );

    if ($new_order_id && !empty($cart)) {
        $db = db();
        foreach ($cart as $item) {
        $product_id = $item['id'] ?? null;
        $quantity   = (int)($item['qty'] ?? 1); 
            if ($product_id && $quantity > 0) {
                $db->prepare("UPDATE tb_product SET stock = stock - ? WHERE id = ? AND stock >= ?")
                ->execute([$quantity, $product_id, $quantity]);
            }
        }
    }

    // Clear cart from db since it is now a confirmed order
    if ($member_id) {
        $db = db();
        $db->prepare("DELETE FROM tb_cart WHERE member_id = ?")->execute([$member_id]);
    }

    return $new_order_id;
}
