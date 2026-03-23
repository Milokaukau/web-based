<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']); 

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
</head>
<body>

<header class="site-header">
    <div class="header-left">
        <a href="../index.php" class="logo">NOAIR</a>

        <div class="category-dropdown">
            <button class="dropbtn">Products<small>▼</small></button>
            <div class="dropdown-content">
                <?php foreach ($categories as $cat): ?>
                    <a href="/pages/product.php?id=<?= $cat['id'] ?>"><?= htmlspecialchars(ucfirst($cat['name'])) ?></a>
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
                WISHLIST <span class="wishlist-count-dot"></span>
            </a>

            <a href="/pages/cart.php" class="cart-link">
                CART <span class="cart-count-dot"></span>
            </a>
        </div>
    </div>
</header>

<main>