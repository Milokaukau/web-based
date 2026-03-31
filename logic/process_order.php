<?php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require_once $project_root . "database/order.php"; // insertOrder()

// ─── Guard: POST only ─────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: payment.php");
    exit;
}

// ─── Guard: cart must not be empty ────────────────────────────────────────────
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

// ─── Collect & sanitise POST fields ──────────────────────────────────────────
$pay_input = $_POST['pay_method'] ?? '';
$pay_method = 'credit_card'; // default
if ($pay_input === 'tng') {
    $pay_method = 'e_wallet';
} elseif ($pay_input === 'card') {
    $pay_method = 'credit_card';
}

$email      = filter_var($_POST['email']    ?? '', FILTER_SANITIZE_EMAIL);
$fullname   = htmlspecialchars($_POST['fullname']  ?? '', ENT_QUOTES, 'UTF-8');
$address    = htmlspecialchars($_POST['address']   ?? '', ENT_QUOTES, 'UTF-8');
$city       = htmlspecialchars($_POST['city']      ?? '', ENT_QUOTES, 'UTF-8');
$postcode   = htmlspecialchars($_POST['postcode']  ?? '', ENT_QUOTES, 'UTF-8');

// ─── Recalculate totals server-side (never trust the client) ─────────────────
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['qty'];
}
$shipping = 0.00;
$total    = $subtotal + $shipping;

// ─── Determine logged-in member ID (0 = guest) ───────────────────────────────
$member_id = $_SESSION['user_id'] ?? 0;
// Fallback: If guest (0), assign to default guest handler (ID 1) to satisfy DB FK rule
if ($member_id == 0) $member_id = 1;

// ─── Persist order ───────────────────────────────────────────────────────────
$order_id    = null;
$error       = null;
$order_items = $_SESSION['cart']; // snapshot before clearing

try {
    $status = ($pay_method === 'credit_card') ? 'success' : 'failed';
    $order_id = insertOrder(
        $member_id,
        $total,
        $pay_method,
        $status,
        $order_items,
        $address,
        $city,
        $postcode
    );

    if ($status === 'failed') {
        $error = "Payment failed. Touch 'n Go eWallet payment is currently unavailable.";
    } else {
        // Clear cart only on success
        $_SESSION['cart'] = [];
    }

} catch (Exception $e) {
    $error = "Something went wrong while placing your order. Please try again.";
    // Uncomment for debug: $error .= " (" . $e->getMessage() . ")";
}

// ─── Delivery date (3 working days) ──────────────────────────────────────────
$delivery_date = date('d M Y', strtotime('+3 days'));
