<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$project_root = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . DIRECTORY_SEPARATOR;

require_once $project_root . "database/product.php";
require_once $project_root . "database/category.php";
require_once $project_root . "database/cart.php";
require_once $project_root . "database/wishlist.php";
require_once $project_root . "logic/auth_helper.php";

$isLoggedIn = isMember();

$wishlistCount = 0;
$cartCount = 0;

if ($isLoggedIn) {
    // If logged in, fetch live totals from the database
    $member_id = $_SESSION['member_id'];
    
    // Cart total
    $cart_items = getCartByMemberId($member_id);
    if (!empty($cart_items)) {
        foreach ($cart_items as $item) {
            $cartCount += $item->quantity; 
        }
    }
    
    // Wishlist total
    $wishlist_items = getWishlistByMemberId($member_id);
    if (!empty($wishlist_items)) {
        $wishlistCount = count($wishlist_items);
    }
} else {
    // If not logged in, rely on the guest session cache
    $wishlistCount = isset($_SESSION['wishlist']) ? count($_SESSION['wishlist']) : 0;
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $cartCount += $item['qty'] ?? 0;
        }
    }
}

$categories = getAllCategories() ?: []; // Ensure it's at least an empty array
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= 'NOAIR | ' . ($_title ?? 'NOAIR') ?></title>
        <link rel="stylesheet" href="/css/style.css">  
        <link rel="stylesheet" href="/css/auth.css">
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    </head>
<body>
    <header class="site-header">
        <div class="header-left">
            <a href="/index.php" class="logo">NOAIR</a>

            <div class="category-dropdown">
                <button class="dropbtn">Products <small>▼</small></button>
                <div class="dropdown-content">
<?php foreach ($categories as $cat): ?>
    <?php
        $catId   = is_object($cat) ? $cat->id   : $cat['id'];
        $catName = is_object($cat) ? $cat->name : $cat['name'];
        $first_prod = getFirstProductByCategory($catId);
        $target_id  = $first_prod ? (is_object($first_prod) ? $first_prod->id : $first_prod['id']) : 1;
    ?>
    <a href="/pages/product.php?id=<?= $target_id ?>">
        <?= htmlspecialchars(ucfirst($catName)) ?>
    </a>
<?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="header-right">
            <!-- Search: name="q" matches search.php, press Enter submits -->
            <div class="search-wrapper">
                <form class="search-form" action="/pages/search.php" method="GET">
                    <input type="text" name="q" placeholder="SEARCH" autocomplete="off">
                </form>
            </div>

            <div class="nav-links">
                <?php if(isMember()): ?>
                    <div class="category-dropdown" style="display:inline-block;">
                        <a href="/pages/profile.php" class="dropbtn">PROFILE <small>▼</small></a>
                        <div class="dropdown-content" style="right: 0; left: auto;">
                            <a href="/pages/profile.php">MY ACCOUNT</a>
                            <a href="/pages/order_history.php">MY ORDERS</a>
                            <a href="/pages/logout.php" style="border-top: 1px solid #f8f8f8; color: #d9534f;">LOGOUT</a>
                        </div>
                    </div>
                    <span>|</span>
                    <a href="/pages/wishlist.php" class="wishlist-link">
                        WISHLIST <?= $wishlistCount > 0 ? "<span class='count-badge'>$wishlistCount</span>" : "" ?>
                    </a>
                    <a href="/pages/cart.php" class="cart-link">
                        CART 🛒 <span class="cart-count"><?= $cartCount > 0 ? $cartCount : "" ?></span>
                    </a>

                <?php elseif(isAdmin()): ?>
                    <a href="/pages/admin/admin.php">ADMIN PANEL</a>
                    <span>|</span>
                    <a href="/pages/logout.php" style="color: #d9534f;">LOGOUT</a>

                <?php else: ?>
                    <a href="/pages/login.php">PROFILE</a>
                    <span>|</span>
                    <a href="/pages/login.php">LOGIN</a>
                    <a href="/pages/cart.php" class="cart-link">CART 🛒</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main>