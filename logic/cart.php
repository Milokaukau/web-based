<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/database/db.php";

$isLoggedIn = isset($_SESSION['role']) && $_SESSION['role'] === 'member';
$user_id = $_SESSION['user_id'] ?? null;

// Handle cart actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    // Require login for all cart actions
    if (!$isLoggedIn) {
        // Preserve the intended destination so user can be sent back after login
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header("Location: /pages/login.php");
        exit;
    }

    $id = $_GET['id'];
    $color = $_GET['color'] ?? 1;
    $cart_key = $id . "_" . $color;

    if ($_GET['action'] == 'add') {
        $name  = $_GET['name']  ?? '';
        $price = $_GET['price'] ?? 0;
        $photo = $_GET['photo'] ?? '';
        $qty   = isset($_GET['qty']) ? intval($_GET['qty']) : 1;

        if (isset($_SESSION['cart'][$cart_key])) {
            $_SESSION['cart'][$cart_key]['qty'] += $qty;
        } else {
            $_SESSION['cart'][$cart_key] = [
                "id"    => $id,
                "name"  => $name,
                "price" => $price,
                "qty"   => $qty,
                "photo" => $photo,
                "color" => $color
            ];
        }

        // --- DB Sync (Add/Update) ---
        if ($isLoggedIn) {
            $check = db()->prepare("SELECT id FROM tb_cart WHERE member_id = ? AND product_id = ?");
            $check->execute([$user_id, $id]);
            $existing = $check->fetch();
            
            if ($existing) {
                $upd = db()->prepare("UPDATE tb_cart SET quantity = quantity + ? WHERE id = ?");
                $upd->execute([$qty, $existing->id]);
            } else {
                $ins = db()->prepare("INSERT INTO tb_cart (member_id, product_id, quantity) VALUES (?, ?, ?)");
                $ins->execute([$user_id, $id, $qty]);
            }
        }
    }

    if (isset($_SESSION['cart'][$cart_key])) {
        if ($_GET['action'] == 'plus') {
            $_SESSION['cart'][$cart_key]['qty']++;
            if ($isLoggedIn) {
                db()->prepare("UPDATE tb_cart SET quantity = quantity + 1 WHERE member_id = ? AND product_id = ?")->execute([$user_id, $id]);
            }
        } elseif ($_GET['action'] == 'minus' && $_SESSION['cart'][$cart_key]['qty'] > 1) {
            $_SESSION['cart'][$cart_key]['qty']--;
            if ($isLoggedIn) {
                db()->prepare("UPDATE tb_cart SET quantity = quantity - 1 WHERE member_id = ? AND product_id = ?")->execute([$user_id, $id]);
            }
        } elseif ($_GET['action'] == 'remove') {
            unset($_SESSION['cart'][$cart_key]);
            if ($isLoggedIn) {
                db()->prepare("DELETE FROM tb_cart WHERE member_id = ? AND product_id = ?")->execute([$user_id, $id]);
            }
        }
    }

    header("Location: cart.php");
    exit;
}

// --- DB Sync (Initial Load) ---
if ($isLoggedIn && empty($_SESSION['cart_synced'])) {
    require_once $_SERVER['DOCUMENT_ROOT'] . "/database/product.php";
    $stmt = db()->prepare("SELECT * FROM tb_cart WHERE member_id = ?");
    $stmt->execute([$user_id]);
    $db_items = $stmt->fetchAll();
    
    foreach ($db_items as $item) {
        $prod = getProductById($item->product_id);
        $ckey = $item->product_id . "_" . ($prod ? $prod->color_id : 1);
        if (!isset($_SESSION['cart'][$ckey])) {
            if ($prod) {
                $_SESSION['cart'][$ckey] = [
                    "id"    => $item->product_id,
                    "name"  => $prod->name,
                    "price" => $prod->price,
                    "qty"   => $item->quantity,
                    "photo" => $prod->photo,
                    "color" => $prod->color_id ?? 1
                ];
            }
        }
    }
    $_SESSION['cart_synced'] = true;
}

// Variables for cart view
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
