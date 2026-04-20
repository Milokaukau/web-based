<?php
require_once $project_root . "logic/auth_helper.php";

requireMember(); 

require_once $project_root . "database/order.php";

$member_id = $_SESSION['member_id']; 
$order_id = $_GET['id'] ?? null;

// ==========================================
// Handle the Cancel Order POST request
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel_order') {
    $cancel_id = $_POST['order_id'] ?? null;

    if ($cancel_id == $order_id) {
        $order_to_cancel = getOrderById($cancel_id, $member_id);
        
        if ($order_to_cancel && in_array($order_to_cancel->order_status, ['pending_payment', 'confirmed'])) {
            $db = db(); 

            if ($order_to_cancel->order_status === 'pending_payment') {
                // SCENARIO A: Unpaid Order
                updateOrderStatus($cancel_id, 'cancelled');
                $flash_message = 'Your order has been successfully cancelled.';
                
            } elseif ($order_to_cancel->order_status === 'confirmed') {
                // SCENARIO B: Paid Order (Uses our new database function!)
                cancelAndRefundOrder($cancel_id);
                $flash_message = 'Your order has been cancelled and your refund will be processed shortly.';
            }
            
            $_SESSION['flash'] = [
                'type' => 'success', 
                'message' => $flash_message
            ];
            
            header("Location: /pages/order_details.php?id=" . urlencode($cancel_id));
            exit;
        }
    }
}
// ==========================================

if (!$order_id) {
    header("Location: order_history.php");
    exit;
}

$order = getOrderById($order_id, $member_id);

if (!$order) {
    header("Location: order_history.php");
    exit;
}

$items = getOrderItems($order_id);

// Pre-defined labels for the UI
$payment_methods = [
    'e_wallet'       => 'E-Wallet',
    'online_banking' => 'Online Banking',
    'card'    => 'Card'
];

$order_status_labels = [
    'pending_payment' => 'Pending Payment',
    'confirmed'       => 'Confirmed',
    'cancelled'       => 'Cancelled',
    'in_delivery'     => 'In Delivery',
    'delivered'       => 'Delivered',
    'completed'       => 'Completed'
];

$payment_status_labels = [
    'pending'        => 'Pending',
    'processing'     => 'Processing',
    'success'        => 'Success',
    'failed'         => 'Failed',
    'pending_refund' => 'Refund Pending',
    'refunded'       => 'Refunded'
];
?>