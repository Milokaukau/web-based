<?php 
/**
 * NOAIR Landing Page - Polished Version
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Connection
$conn = new mysqli("localhost:3306", "root", "", "db_noair");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch arrivals (limiting to 4 for a single clean row)
$sql = "SELECT * FROM tb_product ORDER BY id DESC LIMIT 4";
$result = $conn->query($sql);

// Define root path if not already set in header
$root_path = $root_path ?? '../'; 

include 'components/header.php'; 
?>

<link rel="stylesheet" href="<?= $root_path ?>css/pages/home.css">

<main>
    <section class="hero-slider">
        <div class="swiper mainSwiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <img src="<?= $root_path ?>assets/hero-bg-1.jpg" alt="NOAIR Hydration">
                    <div class="hero-overlay">
                        <p class="hero-subtitle">Breathable Design</p>
                        <h2 class="hero-title">Hydration in its purest form</h2>
                        <a href="<?= $root_path ?>pages/products/all.php" class="btn-main">Explore Shop</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-container">
        <div class="container-wide">
            <div class="section-header">
                <span class="sub-heading">Latest Drop</span>
                <h3>New Arrivals</h3>
            </div>
            
            <div class="new-arrival-grid">
                <?php if($result && $result->num_rows > 0): while($p = $result->fetch_assoc()): ?>
                <div class="product-card">
                    <a href="<?= $root_path ?>pages/products/details.php?id=<?= $p['id'] ?>">
                        <div class="img-wrapper">
                            <div class="tag-container">
                                <?php if ($p['stock'] <= 0): ?>
                                    <span class="product-tag tag-sold-out">SOLD OUT</span>
                                <?php endif; ?>
                                
                                <?php if ($p['id'] >= 4): ?>
                                    <span class="product-tag tag-new">NEW</span>
                                <?php endif; ?>
                            </div>

                            <img src="<?= $root_path . ($p['photo'] ?? 'assets/placeholder.png') ?>" 
                                 alt="<?= htmlspecialchars($p['name']) ?>" 
                                 class="product-img">
                        </div> 

                        <div class="product-info-row">
                            <h5 class="product-name"><?= htmlspecialchars($p['name']) ?></h5>
                            <span class="product-price">RM<?= number_format($p['price'], 2) ?></span>
                        </div>
                    </a>
                </div>
                <?php endwhile; else: ?>
                    <p class="empty-msg">No new arrivals at the moment.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php $conn->close(); ?>
</body>
</html>