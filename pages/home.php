<?php
$project_root = $_SERVER['DOCUMENT_ROOT']."/";
require $project_root."config.php";
require $project_root."logic/home.php";

$_title = "Home";
require $project_root."components/header.php";
?>

<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="../css/home.css">

<div class="home-page-container">

    <!-- Hero Slider -->
    <section class="hero-slider-section">
        <div class="hero-carousel">

            <!-- Slide 1: Brand hero — Warm Coral -->
            <div class="slide active" id="slide-1">
                <div class="slide-bg slide-bg-1">
                    <div class="s1-blob"></div>
                    <div class="s1-ring"></div>
                </div>
                <div class="slide-content">
                    <p class="slide-tag">BREATHABLE DESIGN</p>
                    <h2>Hydration in its<br>purest form</h2>
                    <a href="/pages/shop.php" class="primary-btn-link">SHOP NOW</a>
                </div>
            </div>

            <!-- Slide 2: Bottle Pro Max — Dark Charcoal -->
            <div class="slide" id="slide-2">
                <div class="slide-bg slide-bg-2">
                    <div class="s2-ring"></div>
                    <div class="s2-glow"></div>
                </div>
                <div class="slide-content">
                    <p class="slide-tag" style="color:#5a8aad;">PREMIUM SERIES</p>
                    <h2>Bottle<br>Pro Max</h2>
                    <p class="slide-desc">Ultimate hydration, elevated.<br>Built for those who demand more.</p>
                    <?php if ($slide_product_1): ?>
                        <a href="/pages/product.php?id=<?= $slide_product_1->id ?>" class="primary-btn-link" style="background:#5a8aad;">
                            DISCOVER — RM<?= number_format($slide_product_1->price, 2) ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Slide 3: Bottle LiteFlow — Sage Green -->
            <div class="slide" id="slide-3">
                <div class="slide-bg slide-bg-3">
                    <svg class="s3-svg" viewBox="0 0 700 500" fill="none" preserveAspectRatio="xMaxYMid slice">
                        <line x1="700" y1="0"   x2="0"   y2="500" stroke="#b5c9b7" stroke-width="1.5" opacity="0.4"/>
                        <line x1="700" y1="80"  x2="80"  y2="500" stroke="#b5c9b7" stroke-width="1.5" opacity="0.3"/>
                        <line x1="700" y1="160" x2="160" y2="500" stroke="#b5c9b7" stroke-width="1.5" opacity="0.2"/>
                        <line x1="700" y1="240" x2="240" y2="500" stroke="#b5c9b7" stroke-width="1"   opacity="0.15"/>
                        <circle cx="540" cy="250" r="180" stroke="#7a9e7e" stroke-width="2" fill="none" opacity="0.2"/>
                        <circle cx="540" cy="250" r="110" fill="#7a9e7e" opacity="0.08"/>
                        <circle cx="540" cy="250" r="50"  fill="#7a9e7e" opacity="0.1"/>
                    </svg>
                </div>
                <div class="slide-content">
                    <p class="slide-tag" style="color:#7a9e7e;">EVERYDAY ESSENTIAL</p>
                    <h2>Coffee<br>Cup</h2>
                    <p class="slide-desc">The perfect companion for your morning brew.<br>Double-walled insulation keeps it hot for hours.</p>
                <?php if ($slide_product_2): ?>
                    <a href="/pages/product.php?id=<?= $slide_product_2->id ?>" class="primary-btn-link" style="background:#7a9e7e;">
                    DISCOVER — RM<?= number_format($slide_product_2->price, 2) ?>
                    </a>
                <?php endif; ?>
                </div>
            </div>

        </div>

        <!-- Dot navigation -->
        <div class="slider-dots">
            <span class="dot active" onclick="goToSlide(0)"></span>
            <span class="dot"        onclick="goToSlide(1)"></span>
            <span class="dot"        onclick="goToSlide(2)"></span>
        </div>

        <!-- Arrow navigation -->
        <button class="slider-arrow arrow-prev" onclick="changeSlide(-1)" aria-label="Previous slide">&#8592;</button>
        <button class="slider-arrow arrow-next" onclick="changeSlide(1)"  aria-label="Next slide">&#8594;</button>
    </section>

    <!-- New Arrivals -->
    <section class="section-container">
        <div class="section-header">
            <h3>New Arrivals</h3>
            <div class="line"></div>
        </div>
        <div class="new-arrival-grid">
            <?php if(isset($arr) && count($arr) > 0): ?>
                <?php foreach($arr as $row): ?>
                    <div class="product-card">
                        <a href="product.php?id=<?= $row->id ?>" class="product-link" style="text-decoration:none;">
                            <div class="img-wrapper">
                                <div class="tag-container">
                                    <?php if ($row->stock <= 0): ?>
                                        <span class="product-tag tag-sold-out">SOLD OUT</span>
                                    <?php elseif ($row->id >= 4): ?>
                                        <span class="product-tag tag-new">NEW</span>
                                    <?php endif; ?>
                                </div>
                                <img src="<?= !empty($row->photo) ? '/images/' . htmlspecialchars($row->photo) : 'https://placehold.co/600x600/FDFBFA/F39E9E?text=' . urlencode($row->name) ?>"
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
                <div class="empty-state" style="text-align:center;width:100%;grid-column:1/-1;padding:40px 0;">
                    <p style="color:var(--text-muted);font-size:1.1rem;">No new arrivals at the moment. Stay tuned!</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Why Choose Us -->
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

</main>

<?php require $project_root."components/footer.php"; ?>

<script>
let current = 0;
const slides = document.querySelectorAll('.slide');
const dots   = document.querySelectorAll('.dot');
let timer    = null;

function showSlide(n) {
    slides[current].classList.remove('active');
    dots[current].classList.remove('active');
    current = (n + slides.length) % slides.length;
    slides[current].classList.add('active');
    dots[current].classList.add('active');
}

function goToSlide(n)   { showSlide(n); resetTimer(); }
function changeSlide(n) { showSlide(current + n); resetTimer(); }

function resetTimer() {
    clearInterval(timer);
    timer = setInterval(() => showSlide(current + 1), 5000);
}

timer = setInterval(() => showSlide(current + 1), 5000);
</script>
