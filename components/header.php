<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$project_root = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . DIRECTORY_SEPARATOR;

require_once $project_root . "database/product.php";
require_once $project_root . "database/category.php";
require_once $project_root . "logic/auth_helper.php";

$isLoggedIn = isset($_SESSION['user_id']);

$wishlistCount = isset($_SESSION['wishlist']) ? count($_SESSION['wishlist']) : 0;
$cartCount = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['qty'] ?? 0;
    }
}

$categories = getAllCategories() ?: [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= 'NOAIR | ' . ($_title ?? 'HOME') ?></title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/merged.css">
    <link rel="stylesheet" href="/css/auth.css">
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
                            $catId = is_object($cat) ? $cat->id : $cat['id'];
                            $catName = is_object($cat) ? $cat->name : $cat['name'];
                            $first_prod = getFirstProductByCategory($catId);
                            $target_id = $first_prod ? (is_object($first_prod) ? $first_prod->id : $first_prod['id']) : 1;
                        ?>
                        <a href="/pages/product.php?id=<?= $target_id ?>">
                            <?= htmlspecialchars(ucfirst($catName)) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="header-right">
            <div class="search-wrapper">
                <form class="search-form" action="/pages/search.php" method="GET">
                    <input type="text" name="query" placeholder="SEARCH">
                </form>
            </div>

            <div class="nav-links">
                <?php if (isMember()): ?>
                    <a href="/pages/profile.php">PROFILE</a>
                    <span>|</span>
                    <a href="/pages/logout.php">LOGOUT</a>
                    <a href="/pages/wishlist.php" class="wishlist-link">
                        WISHLIST <?= $wishlistCount > 0 ? "<span class='count-badge'>$wishlistCount</span>" : "" ?>
                    </a>
                    <a href="/pages/cart.php" class="cart-link">
                        CART 🛒 <span class="cart-count"><?= $cartCount > 0 ? $cartCount : "" ?></span>
                    </a>

                <?php elseif (isAdmin()): ?>
                    <a href="/pages/admin/dashboard.php">DASHBOARD</a>
                    <span>|</span>
                    <a href="/pages/logout.php">LOGOUT</a>

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