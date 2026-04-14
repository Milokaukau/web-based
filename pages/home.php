<?php
$project_root = $_SERVER['DOCUMENT_ROOT']."/";
require $project_root . "config.php";
require_once $project_root."logic/auth_helper.php";
require $project_root."logic/home.php";

$_title = "Home";
require $project_root."components/header.php";
?>

<link rel="stylesheet" href="../css/style.css">

<div class="home-page-container">
    <!-- Dynamic Photos Slider (Hero Section) -->
    <section class="hero-slider-section">
        <div class="hero-carousel">
            <!-- Slide 1 -->
            <div class="slide active">
                <div class="slide-background" style="background-color: var(--bg-soft, #FDFBFA);"></div>
                <div class="slide-content">
                    <p>BREATHABLE DESIGN</p>
                    <h2>Hydration in its purest form</h2>
                    <a href="/pages/shop.php" class="primary-btn-link">SHOP NOW</a>
                </div>
            </div>
            
            <!-- Slide 2 -->
            <div class="slide">
                <div class="slide-background" style="background-color: #faf5f5;"></div>
                <div class="slide-content">
                    <p>CRAFTED FOR EVERYDAY</p>
                    <h2>Premium Urban Quality</h2>
                    <a href="/pages/shop.php" class="primary-btn-link">VIEW COLLECTION</a>
                </div>
            </div>
            
            <!-- Slide 3 -->
            <div class="slide">
                <div class="slide-background" style="background-color: #f6f8fb;"></div>
                <div class="slide-content">
                    <p>FOR A GREENER FUTURE</p>
                    <h2>Sustainable Materials</h2>
                    <a href="/pages/shop.php" class="primary-btn-link">LEARN MORE</a>
                </div>
            </div>
        </div>
        
        <!-- Navigation Dots -->
        <div class="slider-dots">
            <span class="dot active" onclick="currentSlide(1)"></span>
            <span class="dot" onclick="currentSlide(2)"></span>
            <span class="dot" onclick="currentSlide(3)"></span>
        </div>
    </section>

    <!-- New Arrivals Section -->
    <section class="section-container">
        <div class="section-header">
            <h3>New Arrivals</h3>
            <div class="line"></div>
        </div>
        
        <div class="new-arrival-grid">
            <?php if(isset($arr) && count($arr) > 0): ?>
                <?php foreach($arr as $row): ?>
                    <div class="product-card">
                        <a href="product.php?id=<?= $row->id ?>" class="product-link" style="text-decoration: none;">
                            <div class="img-wrapper">
                                <div class="tag-container">
                                    <?php if ($row->stock <= 0): ?>
                                        <span class="product-tag tag-sold-out">SOLD OUT</span>
                                    <?php elseif ($row->id >= 4): ?>
                                        <span class="product-tag tag-new">NEW</span>
                                    <?php endif; ?>
                                </div>
                                <!-- Fixed Image Path with fallback -->
                                <img src="<?= !empty($row->photo) ? '../' . htmlspecialchars($row->photo) : 'https://placehold.co/600x600/FDFBFA/F39E9E?text=' . urlencode($row->name) ?>" 
                                     alt="<?= htmlspecialchars($row->name) ?>">
                            </div>
                            <div class="product-info-row">
                                <h5 class="product-name"><?= htmlspecialchars($row->name) ?></h5>
                                <p class="product-cat">NOAIR Series</p>
                                <div class="product-price">RM <?= number_format($row->price, 2) ?></div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state" style="text-align: center; width: 100%; grid-column: 1 / -1; padding: 40px 0;">
                    <p style="color: var(--text-muted); font-size: 1.1rem;">No new arrivals at the moment. Stay tuned!</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Why Choose Us? Section -->
    <section class="section-container">
        <div class="why-choose-us">
            <div class="section-header">
                <h3>Why Choose Us?</h3>
                <div class="line"></div>
            </div>
            <div class="features-grid">
                <div class="feature-item">
                    <div class="feature-icon">🌿</div>
                    <h4>Eco-Friendly</h4>
                    <p>All our products are made from sustainably sourced materials, ensuring a greener footprint for our planet without compromising style.</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">🛡️</div>
                    <h4>Premium Quality</h4>
                    <p>Designed to last a lifetime. We never compromise on build quality or finish, delivering an unmatched premium experience.</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">🚚</div>
                    <h4>Fast Delivery</h4>
                    <p>Get your favorite products securely packaged and delivered right to your door with our reliable shipping partners.</p>
                </div>
            </div>
        </div>
    </section>

</div>

<?php 
// Ensure footer is placed at the most bottom
require $project_root."components/footer.php"; 
?>

<script src="../js/home.js"></script>