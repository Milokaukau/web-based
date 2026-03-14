<?php
$project_root = $_SERVER['DOCUMENT_ROOT']."/";
require $project_root."config.php";
require $project_root."logic/cart.php";

$_title = "Cart"; 
include '../components/header.php';  

// echo "<pre>";
// var_export($arr);
// echo "</pre>";
?>

<link rel="stylesheet" href="../css/style.css">

<div class="cart-outer-wrapper">
    <?php if (empty($_SESSION['cart']) || $item_count == 0): ?>
        <div class="empty-cart-display" style="text-align: center; padding: 100px 0;">
            <h1 style="font-size: 2rem; margin-bottom: 20px; letter-spacing: 2px;">YOUR CART IS EMPTY</h1>
            <p style="color: #967B78; margin-bottom: 40px; font-size: 0.9rem;">Items you add to your cart will appear here.</p>
            <a href="<?= $home_path ?>" class="btn-primary" style="display: inline-block; text-decoration: none;">
                Continue Shopping
            </a>
        </div>

    <?php else: ?>
        <div class="cart-container">
            <div class="cart-main">
                <div class="cart-header-title">
                    <h1>Shopping Cart</h1>
                    <span class="item-counter"><?= $item_count ?> Items</span>
                </div>

                <div class="cart-labels">
                    <span class="lbl-details">Product Details</span>
                    <span class="lbl-center">Quantity</span>
                    <span class="lbl-center">Price</span>
                    <span class="lbl-center">Total</span>
                </div>
                
                <?php foreach ($_SESSION['cart'] as $id => $item): 
                    $line_total = $item['price'] * $item['qty'];
                ?>

                <div class="cart-product-row">
                    <div class="product-col">
                        <div class="product-img-box">
                            <img src="../<?= htmlspecialchars($item['photo']) ?>" alt="Product" style="width:100%; height:100%; object-fit:contain;">
                        </div>
                        <div class="product-meta">
                            <h3><?= htmlspecialchars($item['name']) ?></h3>
                            <p class="product-cat">NOAIR Series</p>
                            <a href="cart.php?action=remove&id=<?= $id ?>" 
                               class="remove-link" 
                               onclick="return confirmRemove('<?= htmlspecialchars($item['name']) ?>')">
                               REMOVE
                            </a>
                        </div>
                    </div>

                    <div class="qty-col">
                        <div class="qty-controls">
                            <a href="cart.php?action=minus&id=<?= $id ?>" class="qty-btn">−</a>
                            <span class="qty-val"><?= $item['qty'] ?></span>
                            <a href="cart.php?action=plus&id=<?= $id ?>" class="qty-btn">+</a>
                        </div>
                    </div>

                    <div class="price-col">RM <?= number_format($item['price'], 2) ?></div>
                    <div class="total-col">RM <?= number_format($line_total, 2) ?></div>
                </div>
                <?php endforeach; ?>

                <a href="<?= $home_path ?>" class="back-shop">← CONTINUE SHOPPING</a>
            </div>

            <div class="cart-sidebar">
                <h2>Order Summary</h2>
                <hr>
                
                <div class="summary-details">
                    <div class="summary-line">
                        <span>Subtotal</span>
                        <span>RM <?= number_format($subtotal, 2) ?></span>
                    </div>

                    <div class="summary-line">
                        <span>Shipping</span>
                        <span style="color: var(--main-coral); font-weight: 700;">FREE</span>
                    </div>

                    <div class="promo-section">
                        <label class="label">Promo Code</label>
                        <div style="display: flex; gap: 5px; margin-top: 8px;">
                            <input type="text" placeholder="Enter code" style="flex: 1;">
                            <button class="apply-btn" style="background: var(--brand-accent); color: white; padding: 0 15px; border-radius: 4px;">Apply</button>
                        </div>
                    </div>
                </div>

                <div class="final-total-section">
                    <div class="summary-line total-line">
                        <span style="font-weight: 800; font-size: 1.1rem;">Total Cost</span>
                        <span style="font-weight: 800; font-size: 1.1rem;">RM <?= number_format($subtotal, 2) ?></span>
                    </div>

                    <a href="payment.php" class="btn-primary" style="display: block; text-align: center; text-decoration: none; margin-top: 20px; padding: 20px; border-radius: 4px;">
                        CHECKOUT
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
/**
 * Validation for removing items
 * Prevents default link action unless user confirms
 */
function confirmRemove(productName) {
    const response = confirm("Do you want to remove " + productName + " from your cart?");
    return response; 
}
</script>

<?php include '../components/footer.php'; ?>