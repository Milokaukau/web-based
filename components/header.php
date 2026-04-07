<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']); 

$wishlistCount = isset($_SESSION['wishlist']) ? count($_SESSION['wishlist']) : 0;
$cartCount = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['qty'];
    }
}
// Fetch categories for the shop dropdown
$project_root = $_SERVER['DOCUMENT_ROOT']."/";
require_once $project_root . "database/category.php";
$categories = getAllCategories();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= 'NOAIR | '.($_title ?? 'HOME') ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/merged.css">
</head>
<body>

<header class="site-header">
    <div class="header-left">
        <a href="../index.php" class="logo">NOAIR</a>

        <div class="category-dropdown">
            <button class="dropbtn">Products<small>▼</small></button>
            <div class="dropdown-content">
                <?php foreach ($categories as $cat): ?>
                    <?php
                        $stmt = db()->prepare("SELECT id FROM tb_product WHERE category_id = ? LIMIT 1");
                        $stmt->execute([$cat['id']]);
                        $first_prod = $stmt->fetch();
                        $target_id = $first_prod ? $first_prod->id : 1;
                    ?>
                    <a href="/pages/product.php?id=<?= $target_id ?>"><?= htmlspecialchars(ucfirst($cat['name'])) ?></a>
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
            <?php if ($isLoggedIn): ?>
                <a href="/pages/profile.php">ACCOUNT</a>
            <?php else: ?>
                <a href="/pages/login.php">LOGIN</a>
            <?php endif; ?>

            <a href="/pages/wishlist.php" class="wishlist-link">
                WISHLIST <?php if ($wishlistCount > 0): ?><span class="count-badge"><?= $wishlistCount ?></span><?php endif; ?>
            </a>

            <a href="/pages/cart.php" class="cart-link">
                CART <?php if ($cartCount > 0): ?><span class="count-badge"><?= $cartCount ?></span><?php endif; ?>
            </a>
        </div>
    </div>
</header>

<main>