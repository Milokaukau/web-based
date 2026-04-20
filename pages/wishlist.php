<?php
// 1. Setup Paths and Session
$project_root = $_SERVER['DOCUMENT_ROOT']."/";
require $project_root."config.php";
require_once $project_root."logic/wishlist.php";

// 4. Set Header info
$_title = "Wishlist";
require $project_root . "components/header.php";
?>

<link rel="stylesheet" href="../css/style.css">

<main class="wishlist-container">
    <?php if (empty($wishlistProducts)): ?>
        <div class="wishlist-header centered">
            <h1 class="main-title">YOUR WISHLIST IS EMPTY</h1>
        </div>
        <div class="w-empty-state">
            <p>You haven't saved any items to your wishlist yet. Start shopping and add your favorite items to your wishlist.</p>
            <a href="/pages/home.php" class="w-continue-btn">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="wishlist-header">
            <h1 class="main-title">MY WISHLIST <span class="w-count">(<?= count($wishlistProducts) ?> items)</span></h1>
        </div>
        <div class="wishlist-grid">
            <?php foreach($wishlistProducts as $p): ?>
                <div class="w-card">
                    <a href="product.php?id=<?= $p->id ?>" class="w-card-img-box">
                        <img src="<?= $p->photo ? '/images/' . $p->photo : 'https://placehold.co/600x600/FDFBFA/F39E9E?text=' . urlencode($p->name) ?>" alt="<?= htmlspecialchars($p->name) ?>">
                    </a>
                    
                    <div class="w-card-content">
                        <div class="w-name"><?= htmlspecialchars($p->name) ?></div>
                        <?php 
                            $color_map = [1 => '#F39E9E', 2 => '#2D2D2D', 3 => '#faf5f5', 4 => '#A280A8'];
                            $color_code = $color_map[$p->selected_color_id] ?? '#F39E9E';
                        ?>
                        <div class="w-variant">
                             <span class="w-color-dot" style="background-color: <?= $color_code ?>"></span>
                        </div>
                        <div class="w-price">RM<?= number_format($p->price, 2) ?></div>
                        
                        <div class="w-actions">
                            <a href="product.php?id=<?= $p->id ?>" class="w-btn-view">VIEW ITEM</a>
                            <a href="wishlist.php?action=remove&id=<?= $p->id ?>&color=<?= $p->selected_color_id ?>" class="w-btn-remove" onclick="return confirmRemove('<?= htmlspecialchars($p->name) ?>')">REMOVE</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php require $project_root . "components/footer.php"; ?>
<script src="../js/wishlist.js"></script>
