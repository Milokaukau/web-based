<?php
// Start session only if it hasn't been started yet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Logic to check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']); 
?>

<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="../css/components/header.css">

<!DOCTYPE html>
<html lang="en">
<header class="site-header">
    <div class="header-left">
        <a href="index.php" class="logo">NOAIR</a>

        <div class="category-dropdown">
            <button class="dropbtn">SHOP <small>▼</small></button>
            <div class="dropdown-content">
                <a href="#">500 ML</a>
                <a href="#">1000 ML</a>
                <a href="#">1500 ML</a>
            </div>
        </div>
    </div>

    <div class="header-right">
        <form class="search-form" action="/pages/search.php" method="GET">
            <input type="text" name="query" placeholder="SEARCH">
            <button type="submit">SEARCH</button>
        </form>

        <div class="nav-links">
            <?php if ($isLoggedIn): ?>
                <a href="/pages/profile.php">ACCOUNT</a>
            <?php else: ?>
                <a href="/pages/login.php">LOGIN</a>
            <?php endif; ?>

            <a href="/pages/cart.php" class="cart-link">
                CART <span class="cart-count-dot"></span>
            </a>
        </div>
    </div>
</header>

    <main>

    