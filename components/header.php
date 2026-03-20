<?php
require_once $project_root."logic/auth_helper.php";
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
                <button class="dropbtn">SHOP <small>▼</small></button>
                <div class="dropdown-content">
                    <a href="#">500 ML</a>
                    <a href="#">1000 ML</a>
                    <a href="#">1500 ML</a>
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
                        <a href="/pages/cart.php" class="cart-link">CART 🛒<span class="cart-count"></span></a>

                    <?php elseif(isAdmin()): ?>
                        <a href="/pages/admin/dashboard.php">DASHBOARD</a>
                        <span>|</span>
                        <a href="/pages/logout.php">LOGOUT</a>

                    <?php else: ?>
                        <a href="/pages/login.php">PROFILE</a>
                        <span>|</span>
                        <a href="/pages/login.php">LOGIN</a>
                        <a href="/pages/cart.php" class="cart-link">CART 🛒<span class="cart-count"></span></a>
                    <?php endif; ?>
                </div>
            </div>
        </header>
