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
        
        <div class="product-gallery">
            <div class="main-image">
                <img src="<?= $arr->photo ? '/uploads/' . $arr->photo : '/assets/placeholder.png' ?>" 
                     alt="<?= htmlspecialchars($arr->name) ?>" id="mainProductImg">
            </div>
        </div>

        <div class="product-info-panel">
            
            <div class="product-header-row">
                <h1 class="p-title"><?= htmlspecialchars($arr->name) ?><?= isset($current_color_name) && $current_color_name != 'Default' ? ' - ' . htmlspecialchars($current_color_name) : '' ?></h1>
                <span class="p-price">RM<?= number_format($arr->price, 2) ?></span>
            </div>

            <div class="option-group">
                <?php 
                    $color_map = [1 => '#F39E9E', 2 => '#2D2D2D', 3 => '#faf5f5', 4 => '#A280A8'];
                    $name_map = [1 => 'Signature Coral', 2 => 'Onyx Black', 3 => 'Pearl White', 4 => 'Amethyst'];
                    $current_color_name = $name_map[$arr->color_id] ?? 'Default';
                ?>
                <span class="option-label">Colour: <span class="selected-value" id="selectedColorLabel"><?= htmlspecialchars($current_color_name) ?></span></span>
                <div class="color-swatches">
                    <?php if (isset($variants) && count($variants) > 0): ?>
                        <?php foreach($variants as $variant): ?>
                            <?php 
                                $v_color = $color_map[$variant->color_id] ?? '#ccc'; 
                                $v_name = $name_map[$variant->color_id] ?? 'Default';
                            ?>
                            <a href="product.php?id=<?= $variant->id ?>" 
                               class="swatch <?= ($variant->id == $arr->id) ? 'active' : '' ?>" 
                               style="background-color: <?= $v_color ?>;"
                               title="<?= htmlspecialchars($v_name) ?>"
                               data-color-id="<?= $variant->color_id ?>"
                               data-color-name="<?= htmlspecialchars($v_name) ?>">
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="swatch active" style="background-color: #ccc;"></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="option-group">
                <div style="display: flex; justify-content: space-between; align-items: baseline;">
                    <span class="option-label">Size: <span class="selected-value"><?= htmlspecialchars($arr->category_name ?? 'Standard') ?></span></span>
                </div>
            </div>

            <div class="option-group">
                <span class="option-label" style="text-transform: none;">Quantity:</span>
                <div class="quantity-picker">
                    <button type="button" onclick="changeQty(-1)">−</button>
                    <input type="text" id="qty" value="1" min="1" max="<?= $arr->stock ?>" readonly>
                    <button type="button" onclick="changeQty(1)">+</button>
                </div>
            </div>

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
                        <span>ADD TO CART</span>
                        <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" fill="none" class="cart-icon">
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
    </div> 

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
                    <li><strong>Weight:</strong> <?= htmlspecialchars($arr->weight_g) ?>g</li>
                    <li><strong>Dimensions:</strong> <?= htmlspecialchars($arr->height_cm) ?>cm Height x <?= htmlspecialchars($arr->base_diameter_cm) ?>cm Base</li>
                    <li><strong>Material:</strong> <?= htmlspecialchars($arr->material) ?></li>
                </ul>
            </div>
        </div>

        <div class="detail-block">
            <h3 class="detail-title">SHIPPING & RETURNS</h3>
            <div class="detail-content">
                <p>Orders are processed within 1-2 business days. <strong>All products include free standard shipping!</strong></p>
                <p>If you aren't perfectly satisfied with your NOAIR product, return it within 30 days in unused condition for a full refund.</p>
            </div>
        </div>
    </div>
</main>

<script>
    const isLoggedIn = <?= isMember() ? 'true' : 'false' ?>;
</script>
<script src="../js/product.js"></script>

<?php 
require $project_root."components/footer.php"; 
?>
