<?php
$project_root = $_SERVER['DOCUMENT_ROOT']."/";
require $project_root."config.php";
require $project_root."logic/product.php";

$_title = "Product"; 
include '../components/header.php';  
?>

<link rel="stylesheet" href="../css/style.css">

<main class="product-details-container">
    <div class="product-layout">
        
        <div class="product-image-stage">
            <div class="image-wrapper">
                <!-- Fixed: removed ../ and use project_root only for PHP includes, use relative for HTML -->
                <img src="<?= $arr->photo ? '../' . $arr->photo : '../assets/placeholder.png' ?>" 
                     alt="<?= htmlspecialchars($arr->name) ?>">
            </div>
        </div>

        <div class="product-content">
            <div class="content-header">
                <h1 class="p-name"><?= htmlspecialchars($arr->name) ?></h1>
                <span class="p-price">RM<?= number_format($arr->price, 2) ?></span>
            </div>

            <div class="p-selection-area">
                <div class="selection-group">
                    <p class="label">Collection : <strong>NOAIR Series</strong></p>
                    <div class="pill-buttons">
                        <button class="pill active">Classic</button>
                        <button class="pill">Limited Edition</button>
                    </div>
                </div>

                <div class="selection-group">
                    <p class="label">Color : <strong>Default</strong></p>
                    <div class="color-swatches">
                        <div class="swatch active" style="background: var(--main-coral);"></div>
                        <div class="swatch" style="background: var(--brand-accent);"></div>
                        <div class="swatch" style="background: var(--bg-cream);"></div>
                    </div>
                </div>

                <div class="selection-group">
                    <p class="label">Quantity:</p>
                    <div class="quantity-picker">
                        <button type="button" onclick="changeQty(-1)">−</button>
                        <input type="number" id="qty" value="1" min="1" max="<?= $arr->stock ?>" readonly>
                        <button type="button" onclick="changeQty(1)">+</button>
                    </div>
                </div>
            </div>

            <div class="p-actions">
                <?php if ($arr->stock > 0): ?>
                    <button type="button" 
                        class="btn-add-to-cart" 
                        data-id="<?= $arr->id ?>"
                        data-name="<?= htmlspecialchars($arr->name) ?>"
                        data-price="<?= $arr->price ?>"
                        data-photo="<?= $arr->photo ?? '' ?>"
                        data-stock="<?= $arr->stock ?>"
                        onclick="addToCart(this)">
                        ADD TO CART
                    </button>
                <?php else: ?>
                    <button class="btn-out-of-stock" disabled>SOLD OUT</button>
                <?php endif; ?>
            </div>

            <div class="p-description">
                <p>Designed for daily hydration with the signature NOAIR breathable technology. High-quality build with a focus on minimalist aesthetics.</p>
            </div>
        </div>
    </div>
</main>

<!-- Fixed: use relative path for JS, remove PROJECT_ROOT -->
<script src="../js/product.js"></script>