<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title> <?= 'NOAIR | '.$_title ?? 'NOAIR' ?></title>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <link rel="stylesheet" href="/css/style.css">  
    </head>

    <body>
        <header class="site-header">
            <div class="header-left">
                <a href="index.php" class="logo">
                    <strong>LOGO</strong>
                </a>

                <div class="category-dropdown">
                    <button class="dropbtn">CATEGORY ▼</button>
                    <div class="dropdown-content">
                        <a href="#">CAT 1</a>
                        <a href="#">CAT 2</a>
                        <a href="#">CAT 3</a>
                    </div>
                </div>
            </div>

            <div class="header-right">
                <form class="search-form" action="" method="GET">
                    <input type="text" name="query" placeholder="Search...">
                    <button type="submit">🔍</button>
                </form>

                <div class="nav-links">
                    <a href="">PROFILE</a>
                    <span>/</span> <a href="">LOGIN</a>
                    <a href="../pages/cart.php" class="cart-link">CART 🛒</a>
                </div>
            </div>
        </header>

        <main>
