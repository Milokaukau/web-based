<?php
if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/database/product.php";
$isLoggedIn = isset($_SESSION['role']) && $_SESSION['role'] === 'member';
$user_id = $_SESSION['user_id'] ?? null;

if (isset($_GET['action'])) {
    // Require login for all wishlist actions
    if (!$isLoggedIn) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header("Location: /pages/login.php");
        exit;
    }

    $id = $_GET['id'] ?? null;
    $color = $_GET['color'] ?? 1;
    
    if ($id && is_numeric($id)) {
        $wish_key = $id . "_" . $color; // Unique key for product + color
        
        if ($_GET['action'] == 'add') {
            if (!in_array($wish_key, $_SESSION['wishlist'])) {
                $_SESSION['wishlist'][] = $wish_key;
                
                // --- DB SYNC (Add) ---
                if ($isLoggedIn) {
                    $stmt = db()->prepare("INSERT INTO tb_wishlist (member_id, product_id) VALUES (?, ?)");
                    $stmt->execute([$user_id, $id]);
                }
            }
        } elseif ($_GET['action'] == 'remove') {
            if (($key = array_search($wish_key, $_SESSION['wishlist'])) !== false) {
                unset($_SESSION['wishlist'][$key]);
                
                // --- DB SYNC (Remove) ---
                if ($isLoggedIn) {
                    $stmt = db()->prepare("DELETE FROM tb_wishlist WHERE member_id = ? AND product_id = ?");
                    $stmt->execute([$user_id, $id]);
                }
            } else {
                // Fallback for old items
                if (($key = array_search($id, $_SESSION['wishlist'])) !== false) {
                    unset($_SESSION['wishlist'][$key]);
                    if ($isLoggedIn) {
                        $stmt = db()->prepare("DELETE FROM tb_wishlist WHERE member_id = ? AND product_id = ?");
                        $stmt->execute([$user_id, $id]);
                    }
                }
            }
        }
    }
    header("Location: wishlist.php");
    exit;
}

// --- DB SYNC (On Initial Load) ---
if ($isLoggedIn && empty($_SESSION['wishlist_synced'])) {
    // 1. Fetch current DB items
    $stmt = db()->prepare("SELECT product_id FROM tb_wishlist WHERE member_id = ?");
    $stmt->execute([$user_id]);
    $db_items_raw = $stmt->fetchAll();
    
    $db_keys = [];
    foreach ($db_items_raw as $item) {
        $prod = getProductById($item->product_id);
        $db_keys[] = $item->product_id . "_" . ($prod ? $prod->color_id : 1);
    }
    
    // 2. Sync From DB -> Session
    foreach ($db_keys as $db_k) {
        if (!in_array($db_k, $_SESSION['wishlist'])) {
            $_SESSION['wishlist'][] = $db_k;
        }
    }

    // 3. Sync From Session -> DB (Push anything that was only in session)
    foreach ($_SESSION['wishlist'] as $sess_item) {
        if (!in_array($sess_item, $db_keys)) {
            $parts = explode('_', $sess_item);
            $pid = $parts[0];
            $cid = $parts[1] ?? 1;
            
            $ins = db()->prepare("INSERT INTO tb_wishlist (member_id, product_id) VALUES (?, ?)");
            $ins->execute([$user_id, $pid]);
        }
    }

    $_SESSION['wishlist_synced'] = true; 
}

// --- Prepare Data for View ---
$wishlistProducts = [];
if (!empty($_SESSION['wishlist'])) {
    foreach ($_SESSION['wishlist'] as $wish_item) {
        $parts = explode('_', $wish_item);
        $wid = $parts[0];
        $selected_color = $parts[1] ?? null;

        $product = getProductById($wid);
        if ($product) {
            $product->selected_color_id = $selected_color ?? $product->color_id;
            $wishlistProducts[] = $product;
        }
    }
}
?>