<?php
$project_root = $_SERVER['DOCUMENT_ROOT']."/";
require $project_root."config.php";
require $project_root."logic/payment.php"; // ← variables $subtotal, $total, $shipping are now available here

$_title = "payment";
include '../components/header.php';
?>

<link rel="stylesheet" href="../css/style.css">

<div class="payment-grid">
    <div class="payment-form-section">
        <h2 class="section-title">Checkout Details</h2>
        
        <form action="process_order.php" method="POST" class="checkout-form">
            <h3 class="subsection-title">Shipping Address</h3>
            
            <div class="form-group">
                <label for="fullname">Full Name</label>
                <input type="text" id="fullname" name="fullname" required placeholder="John Doe">
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required placeholder="john@example.com">
            </div>

            <div class="form-group">
                <label for="address">Shipping Address</label>
                <input type="text" id="address" name="address" required placeholder="123 Street Name">
            </div>

            <div class="row-flex">
                <div class="form-group flex-2">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" required>
                </div>
                <div class="form-group flex-1">
                    <label for="postcode">Postcode</label>
                    <input type="text" id="postcode" name="postcode" required>
                </div>
            </div>

            <div class="payment-methods">
                <h3 class="subsection-title">Payment Method</h3>
                <div class="method-selection">
                    <label class="radio-label">
                        <input type="radio" name="pay_method" value="card" checked> 
                        Credit / Debit Card
                    </label>
                </div>

                <div class="card-inputs">
                    <div class="form-group">
                        <input type="text" placeholder="Card Number" maxlength="16">
                    </div>
                    <div class="row-flex">
                        <input type="text" placeholder="MM / YY" maxlength="5">
                        <input type="text" placeholder="CVV" maxlength="3">
                    </div>
                </div>
            </div>
            
            <button type="submit" class="payment-submit-btn">
                Confirm Payemt (RM <?= number_format($total, 2) ?>)
            </button>
        </form>
    </div>

    <div class="payment-sidebar">
        <div class="summary-card">
            <h2>Your Order</h2>
            <hr>
            
            <div class="order-items-list">
                <?php foreach ($_SESSION['cart'] as $item): ?>
                    <div class="summary-item-row">
                        <span class="item-qty-name"><?= $item['qty'] ?>x <?= htmlspecialchars($item['name']) ?></span>
                        <span class="item-price">RM <?= number_format($item['price'] * $item['qty'], 2) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <hr>
            
            <div class="summary-line">
                <span>Subtotal</span>
                <span>RM <?= number_format($subtotal, 2) ?></span>
            </div>
            
            <div class="summary-line">
                <span>Shipping</span>
                <span class="shipping-tag"><?= $shipping == 0 ? 'FREE' : 'RM '.number_format($shipping, 2) ?></span>
            </div>
            
            <div class="summary-line total-line">
                <span>Total</span>
                <span class="final-amount">RM <?= number_format($total, 2) ?></span>
            </div>
        </div>
    </div>
</div>

<?php include '../components/footer.php'; ?>