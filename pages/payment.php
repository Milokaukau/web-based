<?php
$project_root = $_SERVER['DOCUMENT_ROOT']."/";
require $project_root."config.php";
require $project_root."logic/payment.php"; // vars $subtotal, $total, $shipping

$_title = "Payment";
require $project_root."components/header.php";
?>

<link rel="stylesheet" href="../css/style.css">

<!-- Background decorative blobs using global coral tones -->
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

<div class="checkout-wrapper">
    <div class="checkout-layout">
        
        <!-- Left Section -->
        <div class="checkout-left">
            
            <!-- Progress Bar (2 Steps only) -->
            <div class="progress-bar">
                <div class="step active">
                    <div class="step-icon"></div>
                    <span>PAYMENT</span>
                </div>
                <div class="step-line"></div>
                <div class="step">
                    <div class="step-icon"></div>
                    <span>CONFIRMATION</span>
                </div>
            </div>

            <h1 class="main-title">ENTER YOUR PAYMENT INFORMATION</h1>

            <form action="process_order.php" method="POST" class="payment-form">
                
                <!-- Payment methods -->
                <div class="payment-methods-grid">
                    <label class="pay-method active">
                        <input type="radio" name="pay_method" value="card" checked>
                        <div class="pay-method-content">
                            <!-- Dual Visa/Mastercard representation -->
                            <img src="../images/card_logo.jpg" alt="Visa" height="80";>
                        </div>
                    </label>
                    <label class="pay-method">
                        <input type="radio" name="pay_method" value="tng">
                        <div class="pay-method-content">
                            <!-- TNG eWallet logo representation -->
                            <img src="../images/tng_logo.png" alt="TNG eWallet" height="70" style="border-radius: 4px;">
                        </div>
                    </label>
                </div>

                <!-- Credit Card Details Section -->
                <div id="card-details-section">
                    <div class="form-row">
                        <div class="input-field-group">
                            <div class="input-container half">
                                <label class="floating-label">Email Address</label>
                                <div class="input-box">
                                    <svg class="left-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    <input type="email" name="email" required placeholder="example@gmail.com">
                                </div>
                            </div>
                        </div>
                        <div class="input-field-group">
                            <div class="input-container half">
                                <label class="floating-label">Card Holder</label>
                                <div class="input-box">
                                    <svg class="left-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <input type="text" name="fullname" required placeholder="Jane Cooper">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="input-field-group">
                            <div class="input-container full" id="cc-num-container">
                                <label class="floating-label" id="cc-num-label">Card Number</label>
                                <div class="input-box">
                                    <svg class="left-icon" id="cc-num-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                    <input type="text" id="cc-num" name="card_number" required placeholder="4356 xxxx xxxx xxxx">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="input-field-group">
                            <div class="input-container half" id="cc-exp-container">
                                <label class="floating-label">Expiration Date</label>
                                <div class="input-box">
                                    <svg class="left-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <input type="text" id="cc-exp" name="exp_date" required placeholder="MM/YY">
                                </div>
                            </div>
                        </div>
                        <div class="input-field-group">
                            <div class="input-container half" id="cvv-container">
                                <label class="floating-label">CVV</label>
                                <div class="input-box">
                                    <svg class="left-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    <input type="text" name="cvv" required placeholder="* * *" maxlength="3" inputmode="numeric">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Touch n Go Details Section -->
                <div id="tng-details-section" style="display: none; text-align: center; padding: 40px 20px;">
                    <p style="color: var(--text-dark); margin-bottom: 20px; font-weight: 500;">
                        Scan the QR code below using your Touch 'n Go eWallet app to complete the payment.
                    </p>
                    <div style="background: white; padding: 20px; display: inline-block; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 2px solid var(--border-light, #eee);">
                        <img src="../images/payment_qr.png" alt="TNG QR Code" width="160">
                    </div>
                </div>
                
                <input type="hidden" name="address" value="Default Address">
                <input type="hidden" name="city" value="Default City">
                <input type="hidden" name="postcode" value="00000">

                <!-- Actions -->
                <button type="submit" class="pay-btn">Confirm Payment</button>
            </form>
        </div>

        <!-- Right Section: Order Summary -->
        <div class="checkout-right">
            <h2>ORDER SUMMARY</h2>
            
            <div class="summary-totals">
                <div class="summary-line">
                    <span>Subtotal</span>
                    <span>RM <?= number_format($subtotal, 2) ?></span>
                </div>
                <div class="summary-line">
                    <span>Shipping fee</span>
                    <span>RM <?= number_format($shipping, 2) ?></span>
                </div>
                <div class="summary-line total-amount">
                    <span>Total amount</span>
                    <span>RM <?= number_format($total, 2) ?></span>
                </div>
            </div>

            <div class="delivery-date-card">
                <span>Delivery date</span>
                <span><?= date('d/m/Y', strtotime('+3 days')) ?></span>
            </div>

            <div class="summary-products-list" style="margin-top: 20px;">
                <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                <?php 
                    $color_map_names = [1 => 'Signature Coral', 2 => 'Onyx Black', 3 => 'Pearl White', 4 => 'Amethyst'];
                    $color_name = isset($item['color']) ? ($color_map_names[$item['color']] ?? 'Default') : 'Default';
                ?>
                <div class="summary-product-modern" style="display: flex; align-items: center; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid var(--border-light, #eee);">
                    <div style="display: flex; align-items: center; gap: 16px;">
                        <div class="sp-img-box" style="position: relative; width: 65px; height: 65px; border-radius: 12px; border: 2px solid var(--main-coral-soft, #ffb8b8); background: #fff; display: flex; align-items: center; justify-content: center;">
                            <img style="max-height: 80%; max-width: 80%; object-fit: contain;" src="<?= !empty($item['photo']) ? '../' . htmlspecialchars($item['photo']) : '../assets/placeholder.png' ?>" alt="">
                            <span class="sp-qty-badge" style="position: absolute; top: -10px; right: -10px; background: #888; color: #fff; font-size: 12px; font-weight: bold; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; border-radius: 50%; border: 2px solid #fff;"><?= $item['qty'] ?></span>
                        </div>
                        <div class="sp-info" style="display: flex; flex-direction: column;">
                            <span class="sp-name" style="font-weight: 600; font-size: 15px; color: var(--text-dark, #333);"><?= htmlspecialchars($item['name']) ?></span>
                            <span class="sp-variant" style="font-size: 13px; color: #888; margin-top: 4px;"><?= $color_name ?></span>
                        </div>
                    </div>
                    <div class="sp-price" style="font-weight: 500; font-size: 15px; color: var(--text-dark, #333);">RM <?= number_format($item['price'], 2) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            
        </div>

    </div>
</div>

<?php include '../components/footer.php'; ?>
<script src="../js/payment.js"></script>