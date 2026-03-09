<?php 
/**
 * NOAIR Landing Page
 */
// 1. Database Connection (using your provided SQL details)
$conn = new mysqli("localhost:3306", "root", "", "db_noair"); //

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. Fetch arrivals from tb_product
// We pull name, price, stock, and photo directly from your table
$sql = "SELECT * FROM tb_product ORDER BY id DESC LIMIT 4";
$result = $conn->query($sql);

include 'components/header.php'; 
?>

<link rel="stylesheet" href="../css/style.css">

<main>
    <section class="hero-slider">
        <div class="swiper mainSwiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <img src="<?= $root_path ?>assets/hero-bg-1.jpg" alt="NOAIR Hydration">
                    <div class="hero-overlay">
                        <p>Breathable Design</p>
                        <h2>Hydration in its purest form</h2>
                        <a href="<?= $root_path ?>pages/products/all.php" class="btn-main">Explore Shop</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-container" style="background: #fff; max-width: 100%;">
        <div style="max-width: 1300px; margin: 0 auto;">
            <div class="section-header">
                <h3>New Arrivals</h3>
                <div class="line"></div>
            </div>
            
            <div class="new-arrival-grid">
                <?php if($result && $result->num_rows > 0): while($p = $result->fetch_assoc()): ?>
                <div class="product-card">
                    <a href="<?= $root_path ?>pages/products/test.php">
                        <div class="img-wrapper">
                            <div class="tag-container">
                                <?php 
                                // Tag logic following your database stock column
                                if ($p['stock'] <= 0): ?>
                                    <span class="product-tag tag-sold-out">SOLD OUT</span>
                                <?php endif; ?>
                                
                                <?php if ($p['id'] >= 4): // Example logic for 'New' products ?>
                                    <span class="product-tag tag-new">NEW!</span>
                                <?php endif; ?>
                            </div>

                            <img src="<?= $root_path . ($p['photo'] ?? 'assets/placeholder.png') ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                        </div> 

                        <div class="product-meta">
                            <div class="meta-top">
                                <h5 class="product-name"><?= htmlspecialchars($p['name']) ?></h5>
                                <span class="product-price">RM<?= number_format($p['price'], 2) ?></span>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endwhile; endif; ?>
            </div>
        </div>
    </section>
</main>

</body>
</html>