<?php
$project_root = $_SERVER['DOCUMENT_ROOT']."/";
require $project_root."config.php";
require $project_root."logic/home.php";

$_title = "Home";
require $project_root."components/header.php";

// echo "<pre>";
// var_export($arr);
// echo "</pre>";
?>

<link rel="stylesheet" href="../css/style.css">

<main>
    <section class="hero-slider">
        <div class="swiper mainSwiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <img src="<?= $project_root ?>assets/hero-bg-1.jpg" alt="NOAIR Hydration" class="hero-img">
                    <div class="hero-overlay">
                        <div class="hero-content">
                            <p class="hero-subtitle">BREATHABLE DESIGN</p>
                            <h2 class="hero-title">Hydration in its purest form</h2>
                            <a href="" class="btn-main">EXPLORE SHOP</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-container">
        <div class="container-wide">
            <div class="section-header">
                <span class="sub-heading">Latest Drop</span>
                <h3 class="main-heading">New Arrivals</h3>
            </div>
            
            <div class="new-arrival-grid">
                <?php if($arr && count($arr) > 0): ?>
                    <?php foreach($arr as $row): ?>
                        <div class="product-card">
                            <a href="product.php?id=<?= $row->id ?>" class="product-link">
                                <div class="img-wrapper">
                                    <div class="tag-container">
                                        <?php if ($row->stock <= 0): ?>
                                            <span class="product-tag tag-sold-out">SOLD OUT</span>
                                        <?php elseif ($row->id >= 4): ?>
                                            <span class="product-tag tag-new">NEW</span>
                                        <?php endif; ?>
                                    </div>
                                    <img src="<?= $project_root . ($row->photo ?? 'assets/placeholder.png') ?>" 
                                        alt="<?= htmlspecialchars($row->name) ?>" 
                                        class="product-img">
                                </div>
                                <div class="product-info-row">
                                    <div class="product-meta">
                                        <h5 class="product-name"><?= htmlspecialchars($row->name) ?></h5>
                                        <p class="product-cat">NOAIR Series</p>
                                    </div>
                                    <span class="product-price">RM <?= number_format($row->price, 2) ?></span>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <p class="empty-msg">No new arrivals at the moment. Stay tuned!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>