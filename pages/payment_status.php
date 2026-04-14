<?php

require __DIR__.'/../vendor/autoload.php';

$stripe_secret_key = "sk_test_51TLwrrJ29uQhAS4VbDUHOBhzQ6o4csZjo3zPdGFWRBc4RovsmI9uoE3jVqbhrIDa0hmJFNrH5hKS0vYE73hRzhf200GG6Zna2G";
\Stripe\Stripe::setApiKey($stripe_secret_key);

$project_root = $_SERVER['DOCUMENT_ROOT']."/";
require $project_root."config.php";

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
require $project_root."components/header.php";
?>

<link rel="stylesheet" href="../css/style.css">
<style>
.status-wrapper {
    min-height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
}

.status-card {
    background: #fff;
    border-radius: 24px;
    box-shadow: 0 8px 40px rgba(0,0,0,0.08);
    padding: 56px 48px;
    max-width: 520px;
    width: 100%;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.status-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 5px;
    background: var(--status-accent);
    border-radius: 24px 24px 0 0;
}

.status-icon-wrap {
    width: 88px;
    height: 88px;
    border-radius: 50%;
    background: var(--status-icon-bg);
    border: 3px solid var(--status-accent);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 28px;
}

.status-icon-wrap svg {
    width: 44px;
    height: 44px;
    stroke: var(--status-accent);
}

.status-title {
    font-size: 26px;
    font-weight: 700;
    color: #222;
    margin: 0 0 10px;
    letter-spacing: -0.3px;
}

.status-msg {
    font-size: 15px;
    color: #777;
    margin: 0 0 32px;
    line-height: 1.6;
}

.status-details {
    background: #fafafa;
    border-radius: 14px;
    padding: 20px 24px;
    margin-bottom: 32px;
    text-align: left;
}

.status-detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    font-size: 14px;
    border-bottom: 1px solid #f0f0f0;
}

.status-detail-row:last-child { border-bottom: none; }
.status-detail-row .label     { color: #999; font-weight: 500; }
.status-detail-row .value     { color: #333; font-weight: 600; }

.status-actions {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.btn-primary {
    display: block;
    width: 100%;
    padding: 14px;
    background: var(--main-coral, #ff7b7b);
    color: #fff;
    border: none;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: background 0.2s, transform 0.15s;
}

.btn-primary:hover  { background: #e06060; transform: translateY(-1px); }

.btn-secondary {
    display: block;
    width: 100%;
    padding: 14px;
    background: transparent;
    color: #888;
    border: 1.5px solid #ddd;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    transition: border-color 0.2s, color 0.2s;
}

.btn-secondary:hover { border-color: #bbb; color: #555; }

/* Theming */
.status-card.success {
    --status-accent:  #4caf89;
    --status-icon-bg: #f0faf5;
}

.status-card.fail {
    --status-accent:  #e05c5c;
    --status-icon-bg: #fff0f0;
}

@keyframes pop {
    0%   { transform: scale(0); opacity: 0; }
    60%  { transform: scale(1.15); }
    100% { transform: scale(1); opacity: 1; }
}

.status-card.success .status-icon-wrap {
    animation: pop 0.45s cubic-bezier(.26,1.48,.71,.99) forwards;
}

/* Progress bar centering */
.progress-bar { max-width: 520px; margin: 0 auto 32px; }
</style>

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
                Thank you for your purchase. A confirmation has been sent to
                <?= $customer_email ? '<strong>' . htmlspecialchars($customer_email) . '</strong>' : 'your email' ?>.
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
                    <span class="value" style="color:#4caf89;">Paid ✓</span>
                </div>
            </div>

            <div class="status-actions">
                <a href="/index.php" class="btn-primary">Continue Shopping</a>
                <a href="/orders.php" class="btn-secondary">View My Orders</a>
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