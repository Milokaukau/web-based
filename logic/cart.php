<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/database/cart.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/database/product.php";

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$isLoggedIn = isset($_SESSION['role']) && $_SESSION['role'] === 'member';
$user_id    = $_SESSION['member_id'] ?? null;

// ── Handle Actions ────────────────────────────────────────────────────────────
if (isset($_GET['action']) && isset($_GET['id'])) {

    // Require login
    if (!$isLoggedIn) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header("Location: /pages/login.php");
        exit;
    }

    $id       = (int)$_GET['id'];
    $color    = isset($_GET['color']) ? (int)$_GET['color'] : 1;
    $cart_key = $id . "_" . $color;

    // ── ADD ──────────────────────────────────────────────────────────────────
    if ($_GET['action'] === 'add') {
        $name  = urldecode($_GET['name']  ?? '');
        $price = (float)($_GET['price']   ?? 0);
        $photo = urldecode($_GET['photo'] ?? '');
        $qty   = max(1, (int)($_GET['qty'] ?? 1));

        // Update session
        if (isset($_SESSION['cart'][$cart_key])) {
            $_SESSION['cart'][$cart_key]['qty'] += $qty;
        } else {
            $_SESSION['cart'][$cart_key] = [
                'id'    => $id,
                'name'  => $name,
                'price' => $price,
                'qty'   => $qty,
                'photo' => $photo,
                'color' => $color,
            ];
        }

        // Sync to DB — INSERT or UPDATE
        $existing = cartItemExists($user_id, $id);
        if ($existing) {
            incrementCartQuantity($user_id, $id, $qty); // UPDATE quantity + qty
        } else {
            insertCartItem($user_id, $id, $qty);        // INSERT new row
        }
    }

    // ── PLUS ─────────────────────────────────────────────────────────────────
    elseif ($_GET['action'] === 'plus' && isset($_SESSION['cart'][$cart_key])) {
        $_SESSION['cart'][$cart_key]['qty']++;

        // UPDATE quantity + 1 in DB
        $existing = cartItemExists($user_id, $id);
        if ($existing) {
            incrementCartQuantity($user_id, $id, 1);
        } else {
            // Safety: row missing in DB, re-insert
            insertCartItem($user_id, $id, $_SESSION['cart'][$cart_key]['qty']);
        }
    }

    // ── MINUS ────────────────────────────────────────────────────────────────
    elseif ($_GET['action'] === 'minus' && isset($_SESSION['cart'][$cart_key])) {
        if ($_SESSION['cart'][$cart_key]['qty'] > 1) {
            $_SESSION['cart'][$cart_key]['qty']--;

            // UPDATE quantity - 1 in DB
            decrementCartQuantity($user_id, $id, 1);
        }
        // If qty would hit 0, do nothing (let user press Remove instead)
    }

    // ── REMOVE ───────────────────────────────────────────────────────────────
    elseif ($_GET['action'] === 'remove' && isset($_SESSION['cart'][$cart_key])) {
        unset($_SESSION['cart'][$cart_key]);

        // DELETE row from DB
        deleteCartItem($user_id, $id);
    }

    header("Location: cart.php");
    exit;
}

// ── DB Sync on Initial Page Load (runs once per session) ─────────────────────
if ($isLoggedIn && empty($_SESSION['cart_synced'])) {
    $db_items = getCartByMemberId($user_id);

    foreach ($db_items as $item) {
        $prod = getProductById($item->product_id);
        if (!$prod) continue;

        $ckey = $item->product_id . "_" . $prod->color_id;

        if (!isset($_SESSION['cart'][$ckey])) {
            // Load from DB into session
            $_SESSION['cart'][$ckey] = [
                'id'    => $item->product_id,
                'name'  => $prod->name,
                'price' => $prod->price,
                'qty'   => $item->quantity,
                'photo' => $prod->photo,
                'color' => $prod->color_id,
            ];
        } else {
            // Session already has it — make sure DB quantity matches session
            updateCartQuantity($user_id, $item->product_id, $_SESSION['cart'][$ckey]['qty']);
        }
    }

    // Push any session items not yet in DB
    foreach ($_SESSION['cart'] as $cart_key => $cart_item) {
        $existing = cartItemExists($user_id, $cart_item['id']);
        if (!$existing) {
            insertCartItem($user_id, $cart_item['id'], $cart_item['qty']);
        }
    }

    $_SESSION['cart_synced'] = true;
}

// ── View Variables ────────────────────────────────────────────────────────────
$home_path  = "home.php";
$subtotal   = 0;
$item_count = 0;
$shipping   = 0.00;

if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $item_count += $item['qty'];
        $subtotal   += $item['price'] * $item['qty'];
    }
}

$total = $subtotal + $shipping;
