<?php
$project_root = $_SERVER['DOCUMENT_ROOT']."/";
require $project_root."config.php";
require $project_root."logic/product.php";

$_title = "Product"; 
require $project_root."components/header.php";
?>

<link rel="stylesheet" href="../css/style.css">

<main class="product-details-container">
    <div class="product-layout">
        
        <!-- Left: Sticky Image Gallery -->
        <div class="product-gallery">
            <div class="main-image">
                <img src="<?= $arr->photo ? '../' . $arr->photo : '../assets/placeholder.png' ?>" 
                     alt="<?= htmlspecialchars($arr->name) ?>" id="mainProductImg">
            </div>
        </div>

        <!-- Right: Detail Panel -->
        <div class="product-info-panel">
            
            <div class="product-header-row">
                <h1 class="p-title"><?= htmlspecialchars($arr->name) ?></h1>
                <span class="p-price">RM<?= number_format($arr->price, 2) ?></span>
            </div>

            <!-- Color Options -->
            <div class="option-group">
                <span class="option-label">Colour: <span class="selected-value" id="selectedColorLabel">Signature Coral</span></span>
                <div class="color-swatches">
                    <div class="swatch active" style="background-color: var(--main-coral, #F39E9E);" onclick="selectColor(this, 'Signature Coral')"></div>
                    <div class="swatch" style="background-color: var(--text-dark, #2D2D2D);" onclick="selectColor(this, 'Onyx Black')"></div>
                    <div class="swatch" style="background-color: #faf5f5;" onclick="selectColor(this, 'Pearl White')"></div>
                    <div class="swatch" style="background-color: #A280A8;" onclick="selectColor(this, 'Amethyst')"></div>
                </div>
            </div>

            <!-- Size Options -->
            <div class="option-group">
                <div style="display: flex; justify-content: space-between; align-items: baseline;">
                    <span class="option-label">Size: <span class="selected-value" id="selectedSizeLabel">500ml</span></span>
                    <a href="#" style="font-size: 0.8rem; color: #64748b; text-decoration: underline;">Size chart</a>
                </div>
                <div class="pill-buttons">
                    <button class="pill active" onclick="selectSize(this, '500ml')">500ml</button>
                    <button class="pill" onclick="selectSize(this, '1000ml')">1000ml</button>
                </div>
            </div>

            <!-- Quantity Picker -->
            <div class="option-group">
                <span class="option-label" style="text-transform: none;">Quantity:</span>
                <div class="quantity-picker">
                    <button type="button" onclick="changeQty(-1)">−</button>
                    <input type="text" id="qty" value="1" min="1" max="<?= $arr->stock ?>" readonly>
                    <button type="button" onclick="changeQty(1)">+</button>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="add-to-cart-wrapper">
                <?php if ($arr->stock > 0): ?>
                    <button type="button" 
                        class="btn-add-cart" 
                        data-id="<?= $arr->id ?>"
                        data-name="<?= htmlspecialchars($arr->name) ?>"
                        data-price="<?= $arr->price ?>"
                        data-photo="<?= $arr->photo ?? '' ?>"
                        data-stock="<?= $arr->stock ?>"
                        onclick="addToCart(this)">
                        <span>ADD TO BAG</span>
                        <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" fill="none" class="bag-icon">
                            <path stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M16 10V6a4 4 0 0 0-8 0v4m-2 0h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2z"></path>
                        </svg>
                    </button>
                <?php else: ?>
                    <button class="btn-add-cart disabled" disabled>SOLD OUT</button>
                <?php endif; ?>
                
                <button type="button" class="btn-wishlist" data-id="<?= $arr->id ?>" onclick="addToWishlist(this)">
                    ♡
                </button>
            </div>
        </div>
    </div> <!-- End Product Layout (Top Section) -->

    <!-- BOTTOM SECTION (FULL WIDTH Expanded Text) -->
    <div class="product-bottom-section">
        <div class="detail-block">
            <h3 class="detail-title">DESCRIPTION</h3>
            <div class="detail-content">
                <p><?= nl2br(htmlspecialchars($arr->description ?? '')) ?></p>
            </div>
        </div>

        <div class="detail-block">
            <h3 class="detail-title">SPECIFICATIONS</h3>
            <div class="detail-content">
                <ul>
                    <li><strong>Capacity:</strong> 500ml / 17oz</li>
                    <li><strong>Weight:</strong> 295g</li>
                    <li><strong>Dimensions:</strong> 22.5cm Height x 7.5cm Base</li>
                    <li><strong>Material:</strong> 18/8 Pro-Grade Stainless Steel</li>
                </ul>
            </div>
        </div>

        <div class="detail-block">
            <h3 class="detail-title">SHIPPING & RETURNS</h3>
            <div class="detail-content">
                <p>Orders are processed within 1-2 business days. <strong>All products include free standard shipping!</strong></p>
                <p>If you aren’t perfectly satisfied with your NOAIR product, return it within 30 days in unused condition for a full refund.</p>
            </div>
        </div>
    </div>
</main>

<script>
    // Component Scripts (Color, Size, Accordion)
    function selectColor(el, colorName) {
        document.querySelectorAll('.swatch').forEach(s => s.classList.remove('active'));
        el.classList.add('active');
        document.getElementById('selectedColorLabel').innerText = colorName;
    }

    function selectSize(el, sizeName) {
        document.querySelectorAll('.pill').forEach(p => p.classList.remove('active'));
        el.classList.add('active');
        document.getElementById('selectedSizeLabel').innerText = sizeName;
    }

    function toggleAccordion(btn) {
        const item = btn.closest('.accordion-item');
        const content = item.querySelector('.accordion-content');
        const icon = item.querySelector('.accordion-icon');
        
        // Is it currently open?
        const isOpen = item.classList.contains('active');
        
        // Close all
        document.querySelectorAll('.accordion-item').forEach(i => {
            i.classList.remove('active');
            i.querySelector('.accordion-content').style.display = 'none';
            i.querySelector('.accordion-icon').innerText = '+';
        });

        // Open if it wasn't open
        if (!isOpen) {
            item.classList.add('active');
            content.style.display = 'block';
            icon.innerText = '−'; // minus symbol
        }
    }

    function changeImage(thumbElement) {
        document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active'));
        thumbElement.classList.add('active');
        // Not actually replacing src since it's the same image in db, but simulates gallery structure
    }
</script>

<script src="../js/product.js"></script>

<?php 
require $project_root."components/footer.php"; 
?>