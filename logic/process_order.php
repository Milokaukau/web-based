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
$pay_method = in_array($_POST['pay_method'] ?? '', ['card', 'tng'])
    ? $_POST['pay_method']
    : 'card';

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
$member_id = $_SESSION['member_id'] ?? 0;

// ─── Persist order ───────────────────────────────────────────────────────────
$order_id    = null;
$error       = null;
$order_items = $_SESSION['cart']; // snapshot before clearing

try {
    $order_id = insertOrder(
        $member_id,
        $total,
        $pay_method,
        'paid',
        $order_items,
        $address,
        $city,
        $postcode
    );

    // Clear cart only on success
    $_SESSION['cart'] = [];

} catch (Exception $e) {
    $error = "Something went wrong while placing your order. Please try again.";
    // Uncomment for debug: $error .= " (" . $e->getMessage() . ")";
}

// ─── Delivery date (3 working days) ──────────────────────────────────────────
$delivery_date = date('d M Y', strtotime('+3 days'));
