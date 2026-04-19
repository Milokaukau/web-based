<?php
require __DIR__.'/../vendor/autoload.php';

$stripe_secret_key = "sk_test_51TLwrrJ29uQhAS4VbDUHOBhzQ6o4csZjo3zPdGFWRBc4RovsmI9uoE3jVqbhrIDa0hmJFNrH5hKS0vYE73hRzhf200GG6Zna2G";
\Stripe\Stripe::setApiKey($stripe_secret_key);

$project_root = $_SERVER['DOCUMENT_ROOT']."/";
require_once $project_root."config.php";
require_once $project_root."logic/auth_helper.php";

// Force user to log in before payment
requireMember();

require_once $project_root."logic/payment.php"; // vars $subtotal, $total, $shipping

try {
    $line_items = [];
    foreach ($_SESSION['cart'] as $item) {
        $line_items[] = [
            'price_data' => [
                'currency'     => 'myr',
                'product_data' => ['name' => $item['name']],
                'unit_amount'  => intval($item['price'] * 100),
            ],
            'quantity' => $item['qty'],
        ];
    }

    // No shipping fee since it's free if buying at least 1 item

    $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];

    $checkout_session = \Stripe\Checkout\Session::create([
        'mode'                 => 'payment',
        'payment_method_types' => ['card', 'grabpay'],
        'line_items'           => $line_items,
        'success_url'          => $base_url . '/pages/payment_status.php?status=success&session_id={CHECKOUT_SESSION_ID}',
        'cancel_url'           => $base_url . '/pages/cart.php',
        // Optional: Let Stripe collect the billing address
        'billing_address_collection' => 'required',
    ]);

    $_SESSION['stripe_session_id'] = $checkout_session->id;

    // Immediately redirect to Stripe's hosted checkout page
    header('Location: ' . $checkout_session->url);
    exit;

} catch (\Stripe\Exception\ApiErrorException $e) {
    // If Stripe throws an error, display it simply and provide a way back
    die("<div style='font-family:sans-serif; text-align:center; margin-top:50px;'><h1>Payment Error</h1><p>" . htmlspecialchars($e->getMessage()) . "</p><a href='/pages/cart.php'>Return to Cart</a></div>");
}
?>
