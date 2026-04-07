<?php
// ─── Bootstrap ────────────────────────────────────────────────────────────────
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require $project_root . "config.php";              // session_start() + DB constants
require $project_root . "logic/process_order.php"; // guards, sanitise, DB insert, totals

// ─── Page output ─────────────────────────────────────────────────────────────
$_title = "Order Confirmation";
require $project_root . "components/header.php";
?>

<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="../css/process_order.css">

<?php if ($error): ?>
<!-- ══ ERROR STATE ══════════════════════════════════════════════════════════ -->
<div class="confirm-wrapper">
    <div class="confirm-card error-card">
        <div class="confirm-icon error-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="15" y1="9" x2="9" y2="15"/>
                <line x1="9" y1="9" x2="15" y2="15"/>
            </svg>
        </div>
        <h1 class="confirm-title">Payment Failed</h1>
        <p class="confirm-subtitle"><?= htmlspecialchars($error) ?></p>
        <a href="payment.php" class="confirm-btn outline-btn">← Back to Payment</a>
    </div>
</div>

<?php else: ?>
<!-- ══ SUCCESS STATE ════════════════════════════════════════════════════════ -->

<!-- Decorative blobs -->
<div class="decor-blob blob-1">
    <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
        <path fill="none" stroke="var(--main-coral-soft,#ffb8b8)" stroke-width="8"
              d="M42.7,-73.4C55.6,-66.1,66.6,-54.6,73.6,-41.3C80.5,-28,83.4,-14,84.1,0.4C84.8,14.8,83.4,29.6,76.5,42.4C69.6,55.2,57.1,66,43.3,71.4C29.4,76.8,14.7,76.7,0.7,75.4C-13.3,74.2,-26.7,71.7,-39.9,65.8C-53.1,59.9,-66.3,50.7,-74.6,37.8C-83,24.9,-86.6,8.4,-84.9,-7.4C-83.2,-23.3,-76.3,-38.5,-65.4,-49.6C-54.6,-60.7,-39.8,-67.7,-25.9,-72.1C-12,-76.5,1,-78.3,14.5,-76.3C28.1,-74.3,42,-68.5,42.7,-73.4Z"
              transform="translate(100 100)"/>
    </svg>
</div>
<div class="decor-blob blob-2">
    <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
        <path fill="none" stroke="var(--main-coral-soft,#ffb8b8)" stroke-width="6"
              d="M37,-60.7C48.6,-52.6,59,-42.6,67.6,-30.4C76.2,-18.2,83.1,-3.9,81.1,9.4C79.1,22.7,68.2,35,56,43.6C43.8,52.2,30.2,57.1,16.5,61.7C2.9,66.3,-10.8,70.5,-23.9,67.2C-37,63.9,-49.4,53,-59.1,40.1C-68.8,27.2,-75.7,12.3,-75,-2C-74.4,-16.2,-66.1,-29.9,-55.8,-41C-45.6,-52.1,-33.4,-60.5,-20.5,-64.1C-7.6,-67.6,6.1,-66.3,18.9,-63.3C31.7,-60.3,43.6,-55.6,37,-60.7Z"
              transform="translate(100 100)"/>
    </svg>
</div>

<div class="confirm-wrapper">

    <!-- Progress bar — step 2 active -->
    <div class="progress-bar confirm-progress">
        <div class="step">
            <div class="step-icon done-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
            <span>PAYMENT</span>
        </div>
        <div class="step-line step-line-done"></div>
        <div class="step active">
            <div class="step-icon"></div>
            <span>CONFIRMATION</span>
        </div>
    </div>

    <!-- ── Main confirmation card ── -->
    <div class="confirm-card">

        <!-- Animated checkmark -->
        <div class="confirm-icon">
            <svg class="checkmark" viewBox="0 0 52 52">
                <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
                <path  class="checkmark-check"  fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
            </svg>
        </div>

        <h1 class="confirm-title">Order Confirmed!</h1>
        <p class="confirm-subtitle">
            Thank you<?= $fullname ? ", <strong>" . $fullname . "</strong>" : "" ?>!
            Your payment was successful and your order is now being prepared.
        </p>

        <!-- Order meta pill row -->
        <div class="confirm-meta-row">
            <div class="meta-pill">
                <span class="meta-label">Order ID</span>
                <span class="meta-value">#<?= str_pad($order_id, 5, '0', STR_PAD_LEFT) ?></span>
            </div>
            <div class="meta-pill">
                <span class="meta-label">Payment</span>
                <span class="meta-value"><?= $pay_method === 'tng' ? "Touch 'n Go" : "Credit / Debit Card" ?></span>
            </div>
            <div class="meta-pill">
                <span class="meta-label">Estimated Delivery</span>
                <span class="meta-value"><?= $delivery_date ?></span>
            </div>
        </div>

        <!-- ── Order summary table ── -->
        <div class="confirm-summary">
            <h2 class="summary-heading">Order Summary</h2>

            <div class="confirm-items">
                <?php foreach ($order_items as $product_id => $item): ?>
                <div class="confirm-item">
                    <div class="confirm-item-img"
                         style="background: url('<?= htmlspecialchars($item['image'] ?? $item['photo'] ?? '') ?>') center/cover no-repeat; background-color: var(--bg-peach,#F0C9A3);">
                    </div>
                    <div class="confirm-item-info">
                        <div class="confirm-item-name"><?= htmlspecialchars($item['name']) ?></div>
                        <div class="confirm-item-sub">
                            Qty: <?= (int)$item['qty'] ?> &nbsp;·&nbsp; RM <?= number_format($item['price'], 2) ?> each
                        </div>
                    </div>
                    <div class="confirm-item-total">
                        RM <?= number_format($item['price'] * $item['qty'], 2) ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Totals -->
            <div class="confirm-totals">
                <div class="confirm-total-line">
                    <span>Subtotal</span>
                    <span>RM <?= number_format($subtotal, 2) ?></span>
                </div>
                <div class="confirm-total-line">
                    <span>Shipping</span>
                    <span><?= $shipping > 0 ? 'RM ' . number_format($shipping, 2) : 'FREE' ?></span>
                </div>
                <div class="confirm-total-line grand-total">
                    <span>Total Paid</span>
                    <span>RM <?= number_format($total, 2) ?></span>
                </div>
            </div>
        </div>

        <!-- ── CTA buttons ── -->
        <div class="confirm-actions">
            <a href="home.php" class="confirm-btn primary-btn">Continue Shopping</a>
            <a href="order_listing.php" class="confirm-btn outline-btn">View My Orders</a>
        </div>
    </div>

</div>
<?php endif; ?>

<?php include $project_root . 'components/footer.php'; ?>
