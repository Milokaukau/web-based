<?php 
// 1. HANDLE ACTIONS FIRST (Must happen before any HTML/Include output)
session_start(); 

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    
    if (isset($_SESSION['cart'][$id])) {
        if ($_GET['action'] == 'plus') {
            $_SESSION['cart'][$id]['qty']++;
        } 
        elseif ($_GET['action'] == 'minus' && $_SESSION['cart'][$id]['qty'] > 1) {
            $_SESSION['cart'][$id]['qty']--;
        } 
        elseif ($_GET['action'] == 'remove') {
            unset($_SESSION['cart'][$id]);
        }
    }
    // Refresh to update totals
    header("Location: cart.php");
    exit;
}

// 2. Include Header (Relative path to components folder)
include '../components/header.php'; 

$subtotal = 0;
$item_count = 0;

if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $item_count += $item['qty'];
    }
}

// Path to reach the root index from the /pages/ folder
$home_path = "../index.php"; 
?>

<link rel="stylesheet" href="../css/components/cart.css">

<div class="cart-outer-wrapper">
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
            
            <?php if (empty($_SESSION['cart'])): ?>
                <div class="empty-msg" style="padding: 50px 0; text-align: center;">
                    <p>Your bag is empty. <a href="<?= $home_path ?>" style="color: #000; font-weight: bold;">Explore NOAIR.</a></p>
                </div>
            <?php else: ?>
                <?php foreach ($_SESSION['cart'] as $id => $item): 
                    $line_total = $item['price'] * $item['qty'];
                    $subtotal += $line_total;
                ?>
                <div class="cart-product-row">
                    <div class="product-col">
                        <div class="product-img-box">
                            <span class="img-placeholder">NOAIR</span>
                        </div>
                        <div class="product-meta">
                            <h3><?= htmlspecialchars($item['name']) ?></h3>
                            <p class="product-cat">Series</p>
                            <a href="cart.php?action=remove&id=<?= $id ?>" class="remove-link">Remove</a>
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
            <?php endif; ?>

            <a href="<?= $home_path ?>" class="back-shop">← Continue Shopping</a>
        </div>

        <div class="cart-sidebar">
            <h2>Order Summary</h2>
            <hr>
            
            <div class="summary-details">
                <div class="summary-line">
                    <span>Subtotal</span>
                    <span>RM <?= number_format($subtotal, 2) ?></span>
                </div>

                <div class="shipping-section">
                    <label>Shipping</label>
                    <select class="shipping-select">
                        <option>Standard Delivery - FREE</option>
                        <option>Express Delivery - RM 15.00</option>
                    </select>
                </div>

                <div class="promo-section">
                    <label>Promo Code</label>
                    <div style="display: flex; gap: 5px;">
                        <input type="text" placeholder="Enter code" style="flex: 1; padding: 8px;">
                        <button class="apply-btn">Apply</button>
                    </div>
                </div>
            </div>

            <div class="final-total-section">
                <div class="summary-line total-line">
                    <span style="font-weight: bold;">Total Cost</span>
                    <span style="font-weight: bold;">RM <?= number_format($subtotal, 2) ?></span>
                </div>

                <?php if (!empty($_SESSION['cart'])): ?>
                    <a href="pages/payment.php" class="checkout-main-btn" style="display: block; text-align: center; text-decoration: none; background: #000; color: #fff; padding: 15px; margin-top: 20px; border-radius: 4px;">
                        Checkout
                    </a>
                <?php else: ?>
                    <button class="checkout-main-btn" disabled style="width: 100%; opacity: 0.5; cursor: not-allowed;">
                        Checkout
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../components/footer.php'; ?>