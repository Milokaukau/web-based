<?php
$project_root = $_SERVER['DOCUMENT_ROOT']."/";
require $project_root."config.php";
require $project_root."logic/cart.php";

$_title = "Cart"; 
require $project_root."components/header.php";
?>

<link rel="stylesheet" href="../css/style.css">

<!-- Background decorative blobs matching Payment Style -->
<div class="decor-blob blob-1">
    <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
        <path fill="none" stroke="var(--main-coral-soft, #ffb8b8)" stroke-width="8" d="M42.7,-73.4C55.6,-66.1,66.6,-54.6,73.6,-41.3C80.5,-28,83.4,-14,84.1,0.4C84.8,14.8,83.4,29.6,76.5,42.4C69.6,55.2,57.1,66,43.3,71.4C29.4,76.8,14.7,76.7,0.7,75.4C-13.3,74.2,-26.7,71.7,-39.9,65.8C-53.1,59.9,-66.3,50.7,-74.6,37.8C-83,24.9,-86.6,8.4,-84.9,-7.4C-83.2,-23.3,-76.3,-38.5,-65.4,-49.6C-54.6,-60.7,-39.8,-67.7,-25.9,-72.1C-12,-76.5,1,-78.3,14.5,-76.3C28.1,-74.3,42,-68.5,42.7,-73.4Z" transform="translate(100 100)" />
    </svg>
</div>
<div class="decor-blob blob-2">
    <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
        <path fill="none" stroke="var(--main-coral-soft, #ffb8b8)" stroke-width="6" d="M37,-60.7C48.6,-52.6,59,-42.6,67.6,-30.4C76.2,-18.2,83.1,-3.9,81.1,9.4C79.1,22.7,68.2,35,56,43.6C43.8,52.2,30.2,57.1,16.5,61.7C2.9,66.3,-10.8,70.5,-23.9,67.2C-37,63.9,-49.4,53,-59.1,40.1C-68.8,27.2,-75.7,12.3,-75,-2C-74.4,-16.2,-66.1,-29.9,-55.8,-41C-45.6,-52.1,-33.4,-60.5,-20.5,-64.1C-7.6,-67.6,6.1,-66.3,18.9,-63.3C31.7,-60.3,43.6,-55.6,37,-60.7Z" transform="translate(100 100)" />
    </svg>
</div>

<div class="cart-wrapper">
    <?php if (empty($_SESSION['cart']) || $item_count == 0): ?>
        <div class="empty-cart-display" style="text-align: center; padding: 120px 0; position: relative; z-index: 2;">
            <h1 class="main-title" style="margin-bottom: 20px;">YOUR CART IS EMPTY</h1>
            <p style="color: var(--text-muted, #967B78); margin-bottom: 40px; font-size: 1rem;">Looks like you haven't added anything yet.</p>
            <a href="<?= $home_path ?>" class="primary-btn-link">
                Continue Shopping
            </a>
        </div>
    <?php else: ?>
        <div class="cart-layout">
            
            <!-- Left Section: Cart Items -->
            <div class="cart-left">
                <h1 class="main-title">SHOPPING CART</h1>

                <div class="cart-labels">
                    <div class="lbl-product">Product Details</div>
                    <div class="lbl-center">Price</div>
                    <div class="lbl-center">Quantity</div>
                    <div class="lbl-center">Total Price</div>
                    <div></div> <!-- Remove btn col -->
                </div>

                <div class="cart-items-wrapper">
                    <?php 
                    $color_map = [1 => '#F39E9E', 2 => '#2D2D2D', 3 => '#faf5f5', 4 => '#A280A8'];
                    foreach ($_SESSION['cart'] as $key => $item): 
                        $line_total = $item['price'] * $item['qty'];
                        $item_color_code = $color_map[$item['color']] ?? '#F39E9E';
                        $real_id = $item['id'] ?? $key;
                    ?>
                    <div class="cart-row">
                        <!-- Column 1: Product -->
                        <div class="col-product">
                            <div class="product-thumb">
                                <img src="../<?= htmlspecialchars($item['photo'] ?? 'assets/placeholder.jpg') ?>" alt="Product">
                            </div>
                            <div class="product-info">
                                <h3><?= htmlspecialchars($item['name']) ?></h3>
                                <div class="product-variant">
                                    <span class="color-dot" style="background-color: <?= $item_color_code ?>;"></span> 
                                </div>
                            </div>
                        </div>
                        
                        <!-- Column 2: Price -->
                        <div class="col-price">
                            RM <?= number_format($item['price'], 2) ?>
                        </div>
                        
                        <!-- Column 3: Quantity Pill -->
                        <div class="col-qty">
                            <div class="qty-pill">
                                <a href="cart.php?action=minus&id=<?= $real_id ?>&color=<?= $item['color'] ?>" class="qty-btn" title="Decrease">−</a>
                                <span class="qty-val"><?= $item['qty'] ?></span>
                                <a href="cart.php?action=plus&id=<?= $real_id ?>&color=<?= $item['color'] ?>" class="qty-btn" title="Increase">+</a>
                            </div>
                        </div>
                        
                        <!-- Column 4: Total -->
                        <div class="col-total">
                            RM <?= number_format($line_total, 2) ?>
                        </div>
                        
                        <!-- Column 5: Remove (X) -->
                        <div class="col-remove">
                            <a href="cart.php?action=remove&id=<?= $real_id ?>&color=<?= $item['color'] ?>" class="remove-btn" onclick="return confirmRemove('<?= htmlspecialchars($item['name']) ?>')" title="Remove Item">
                                Remove
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="cart-footer">
                    <a href="<?= $home_path ?>" class="continue-shopping">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Continue Shopping
                    </a>
                </div>
            </div>

            <!-- Right Section: Order Summary (Consistent with payment.php) -->
            <div class="cart-right">
                <h2>ORDER SUMMARY</h2>
                
                <div class="summary-totals">
                    <div class="summary-line">
                        <span>Subtotal</span>
                        <span>RM <?= number_format($subtotal, 2) ?></span>
                    </div>
                    <div class="summary-line">
                        <span>Shipping fee</span>
                        <span><?= $shipping == 0 ? 'FREE' : 'RM '.number_format($shipping, 2) ?></span>
                    </div>
                    <div class="summary-line total-amount">
                        <span>Total amount</span>
                        <span>RM <?= number_format($total, 2) ?></span>
                    </div>
                </div>



                <a href="payment.php" class="checkout-main-btn" style="display: block; text-align: center; text-decoration: none;">CHECKOUT</a>
            </div>
            
        </div>
    <?php endif; ?>
</div>

<?php require $project_root."components/footer.php"; ?>
<script src="../js/cart.js"></script>