<?php
// 1. Start session first
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Define paths safely
$project_root = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . DIRECTORY_SEPARATOR;

// 3. Load dependencies BEFORE using them
require_once $project_root . "database/category.php";
require_once $project_root . "logic/auth_helper.php";

// 4. Logic
$isLoggedIn = isset($_SESSION['user_id']); 

$wishlistCount = isset($_SESSION['wishlist']) ? count($_SESSION['wishlist']) : 0;
$cartCount = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['qty'] ?? 0; // Added null coalesce to prevent 'undefined index'
    }
}

$categories = getAllCategories() ?: []; // Ensure it's at least an empty array
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title> <?= 'NOAIR | '.($_title ?? 'NOAIR') ?></title>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <link rel="stylesheet" href="/css/style.css">  
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
        // 1. Get the category ID (Works whether $cat is an object or array)
        $catId = is_object($cat) ? $cat->id : $cat['id'];
        $catName = is_object($cat) ? $cat->name : $cat['name'];

        $stmt = db()->prepare("SELECT id FROM tb_product WHERE category_id = ? LIMIT 1");
        $stmt->execute([$catId]);
        $first_prod = $stmt->fetch();

        // 2. Get the product ID (Works whether $first_prod is an object or array)
        $target_id = 1; // Default fallback
        if ($first_prod) {
            $target_id = is_object($first_prod) ? $first_prod->id : ($first_prod['id'] ?? 1);
        }
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
                <?php if(isMember()): ?>
                    <a href="/pages/profile.php">PROFILE</a>
                    <span>|</span>
                    <a href="/pages/logout.php">LOGOUT</a>
                    <a href="/pages/wishlist.php" class="wishlist-link">
                        WISHLIST <?= $wishlistCount > 0 ? "<span class='count-badge'>$wishlistCount</span>" : "" ?>
                    </a>
                    <a href="/pages/cart.php" class="cart-link">
                        CART 🛒 <span class="cart-count"><?= $cartCount > 0 ? $cartCount : "" ?></span>
                    </a>

                <?php elseif(isAdmin()): ?>
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
