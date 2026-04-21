<?php

require __DIR__.'/../vendor/autoload.php';

$stripe_secret_key = "your_secret_key_here";
\Stripe\Stripe::setApiKey($stripe_secret_key);

$project_root = $_SERVER['DOCUMENT_ROOT']."/";
require_once $project_root."config.php";
// ---------------------------------------------------------------
// Determine status from Stripe redirect params
// ---------------------------------------------------------------
$status     = $_GET['status']     ?? 'cancel';  // 'success' | 'cancel'
$session_id = $_GET['session_id'] ?? null;

$is_success     = false;
$amount_total   = null;
$customer_email = null;
$order_id       = null;
$pay_method_label = null;
$error_message  = null;

if ($status === 'success' && $session_id) {
    try {
        // Always verify server-side — never trust the URL param alone
        $stripe_session = \Stripe\Checkout\Session::retrieve($session_id);

        if ($stripe_session->payment_status === 'paid') {
            $is_success     = true;
            $amount_total   = $stripe_session->amount_total / 100;
            $customer_email = $stripe_session->customer_details->email ?? null;
            $order_id       = strtoupper(substr($stripe_session->id, 3, 10));

            // Friendly label for the payment method used
            $pm = $stripe_session->payment_method_types[0] ?? 'card';
            $pay_method_label = $pm === 'grabpay' ? 'Touch \'n Go eWallet' : 'Credit / Debit Card';

            // -------------------------------------------------------
            // Save order to your database here if not already
            // handled by a Stripe webhook (recommended approach).
            // -------------------------------------------------------
            require_once $project_root."database/payment_status.php";
            if (!empty($_SESSION['cart'])) {
                $member_id = $_SESSION['member_id'] ?? 0;
                processSuccessfulPayment($member_id, $amount_total, $pm, $_SESSION['cart']);
            }

            // Clear cart from session only after confirmed payment
            unset($_SESSION['cart']);
            unset($_SESSION['stripe_session_id']);

        } else {
            $error_message = "Your payment is still processing. Please check your email for confirmation.";
        }

    } catch (\Stripe\Exception\ApiErrorException $e) {
        $error_message = "Could not verify your payment. Please contact support with reference: " . htmlspecialchars($session_id);
    }

} elseif ($status === 'cancel') {
    $error_message = "You cancelled the payment. Your cart has been saved — feel free to try again.";

} else {
    $error_message = "Something went wrong. Please try again or contact support.";
}

$_title = $is_success ? "Order Confirmed" : ($status === 'cancel' ? "Payment Cancelled" : "Payment Failed");
require_once $project_root."components/header.php";
?>

<link rel="stylesheet" href="../css/style.css">

<div class="decor-blob blob-1">
    <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
        <path fill="none" stroke="var(--main-coral-soft, #ffb8b8)" stroke-width="8" d="M42.7,-73.4C55.6,-66.1,66.6,-54.6,73.6,-41.3C80.5,-28,83.4,-14,84.1,0.4C84.8,14.8,83.4,29.6,76.5,42.4C69.6,55.2,57.1,66,43.3,71.4C29.4,76.8,14.7,76.7,0.7,75.4C-13.3,74.2,-26.7,71.7,-39.9,65.8C-53.1,59.9,-66.3,50.7,-74.6,37.8C-83,24.9,-86.6,8.4,-84.9,-7.4C-83.2,-23.3,-76.3,-38.5,-65.4,-49.6C-54.6,-60.7,-39.8,-67.7,-25.9,-72.1C-12,-76.5,1,-78.3,14.5,-76.3C28.1,-74.3,42,-68.5,42.7,-73.4Z" transform="translate(100 100)" />
    </svg>
</div>

<div class="status-wrapper">
    <div style="width:100%;max-width:560px;">

        <!-- Progress bar: CONFIRMATION active on success, PAYMENT active on fail -->
        <div class="progress-bar">
            <div class="step <?= $is_success ? 'completed' : 'active' ?>">
                <div class="step-icon"></div>
                <span>PAYMENT</span>
            </div>
            <div class="step-line"></div>
            <div class="step <?= $is_success ? 'active' : '' ?>">
                <div class="step-icon"></div>
                <span>CONFIRMATION</span>
            </div>
        </div>

        <div class="status-card <?= $is_success ? 'success' : 'fail' ?>">

            <?php if ($is_success): ?>
            <!-- ── SUCCESS ──────────────────────────────────────── -->
            <div class="status-icon-wrap">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </div>

            <h1 class="status-title">Order Confirmed!</h1>
            <p class="status-msg">
                Thank you for your purchase. Your item(s) will be shipped soon.
            </p>

            <div class="status-details">
                <?php if ($order_id): ?>
                <div class="status-detail-row">
                    <span class="label">Order reference</span>
                    <span class="value">#<?= htmlspecialchars($order_id) ?></span>
                </div>
                <?php endif; ?>
                <?php if ($amount_total !== null): ?>
                <div class="status-detail-row">
                    <span class="label">Amount paid</span>
                    <span class="value">RM <?= number_format($amount_total, 2) ?></span>
                </div>
                <?php endif; ?>
                <?php if ($pay_method_label): ?>
                <div class="status-detail-row">
                    <span class="label">Payment method</span>
                    <span class="value"><?= htmlspecialchars($pay_method_label) ?></span>
                </div>
                <?php endif; ?>
                <div class="status-detail-row">
                    <span class="label">Estimated delivery</span>
                    <span class="value"><?= date('d M Y', strtotime('+3 days')) ?></span>
                </div>
                <div class="status-detail-row">
                    <span class="label">Payment status</span>
                    <span class="value" style="color:#4caf89;">Success ✓</span>
                </div>
            </div>

            <div class="status-actions">
                <a href="/index.php" class="btn-primary">Continue Shopping</a>
                <a href="/pages/order_history.php" class="btn-secondary">View My Orders</a>
            </div>

            <?php else: ?>
            <!-- ── FAILURE / CANCEL ─────────────────────────────── -->
            <div class="status-icon-wrap">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>

            <h1 class="status-title">
                <?= $status === 'cancel' ? 'Payment Cancelled' : 'Payment Failed' ?>
            </h1>
            <p class="status-msg">
                <?= htmlspecialchars($error_message) ?>
            </p>

            <div class="status-actions">
                <a href="/payment.php" class="btn-primary">Try Again</a>
                <a href="/index.php" class="btn-secondary">Return to Shop</a>
            </div>

            <?php endif; ?>

        </div>
    </div>
</div>

<?php include '../components/footer.php'; ?>
