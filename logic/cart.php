<?php
// Handle cart actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];

    if ($_GET['action'] == 'add') {
        $name  = $_GET['name']  ?? '';
        $price = $_GET['price'] ?? 0;
        $photo = $_GET['photo'] ?? '';
        $qty   = isset($_GET['qty']) ? intval($_GET['qty']) : 1;

        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['qty'] += $qty;
        } else {
            $_SESSION['cart'][$id] = [
                "name"  => $name,
                "price" => $price,
                "qty"   => $qty,
                "photo" => $photo
            ];
        }
    }

    if (isset($_SESSION['cart'][$id])) {
        if ($_GET['action'] == 'plus') {
            $_SESSION['cart'][$id]['qty']++;
        } elseif ($_GET['action'] == 'minus' && $_SESSION['cart'][$id]['qty'] > 1) {
            $_SESSION['cart'][$id]['qty']--;
        } elseif ($_GET['action'] == 'remove') {
            unset($_SESSION['cart'][$id]);
        }
    }

    header("Location: cart.php");
    exit;
}

// Variables for cart view
$home_path  = "home.php"; // ← this is what the button uses
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