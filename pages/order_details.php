<?php
$project_root = $_SERVER['DOCUMENT_ROOT']."/";
require $project_root."config.php";
require $project_root."logic/order_details.php";

$_title = 'Order Details #' . htmlspecialchars($order->order_id);
include $project_root.'components/header.php';
?>

<div class="container details-container">
    
    <a href="order_history.php" class="back-link">&larr; Back to Order History</a>
    
    <div class="details-header">
        <h1 class="details-title">Order Details</h1>
        
        <div class="status-badges" style="display: flex; gap: 10px; align-items: center;">
            <span class="status-badge status-<?= htmlspecialchars($order->order_status) ?>">
                Order: <?= htmlspecialchars($order_status_labels[$order->order_status] ?? 'Unknown') ?>
            </span>
            
            <?php if (!empty($order->payment_status)): ?>
                <span class="status-badge status-<?= htmlspecialchars($order->payment_status) ?>">
                    Payment: <?= htmlspecialchars($payment_status_labels[$order->payment_status] ?? 'Unknown') ?>
                </span>
            <?php endif; ?>
        </div>
    </div>

    <div class="summary-box">
        <div class="summary-block">
            <span>Order Number</span>
            <strong>#<?= htmlspecialchars($order->order_id) ?></strong>
        </div>
        <div class="summary-block">
            <span>Date Placed</span>
            <strong><?= date('F d, Y', strtotime($order->created_at)) ?></strong>
        </div>
        <div class="summary-block">
            <span>Payment Method</span>
            <strong><?= htmlspecialchars($payment_methods[$order->payment_method] ?? 'N/A') ?></strong>
        </div>
        <div class="summary-block">
            <span>Total Amount</span>
            <strong>$<?= number_format($order->amount, 2) ?></strong>
        </div>
    </div>

    <div class="details-items">
        <h3 style="margin-bottom: 20px; font-weight: 800; text-transform: uppercase;">Items Ordered</h3>
        
        <?php foreach ($items as $item): ?>
            <div class="details-item-row">
                <div class="item-thumbnail">
                    <img src="/images/<?= htmlspecialchars($item->photo) ?>" alt="Product Image">
                </div>
                
                <div class="details-item-center">
                    <div class="item-details">
                        <h3 style="font-weight: 800; font-size: 1.1rem; margin: 0 0 5px 0; text-transform: uppercase;">
                            <?= htmlspecialchars($item->product_name) ?>
                        </h3>
                        <div class="item-meta">
                            Color: <?= htmlspecialchars($item->color_name) ?> <br>
                            Qty: <?= htmlspecialchars($item->quantity) ?> 
                            (@ $<?= number_format($item->purchase_price, 2) ?> each)
                        </div>
                    </div>
                </div>

                <div class="details-item-price">
                    $<?= number_format($item->purchase_price * $item->quantity, 2) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="details-total-section">
        <div class="totals-wrapper">
            <div class="total-line">
                <span>Subtotal</span>
                <span>$<?= number_format($order->amount, 2) ?></span>
            </div>
            <div class="total-line">
                <span>Shipping</span>
                <span>$0.00</span> 
            </div>
            <div class="total-line grand-total">
                <span>Total</span>
                <span>$<?= number_format($order->amount, 2) ?></span>
            </div>
        </div>
    </div>

    <?php if (!empty($_SESSION['flash'])): ?>
        <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type']) ?>" style="margin-top: 20px; padding: 15px; background: #d4edda; color: #155724; border-radius: 5px;">
            <?= htmlspecialchars($_SESSION['flash']['message']) ?>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <?php if ($order->order_status === 'pending_payment' || $order->order_status === 'confirmed'): ?>
        <div class="order-actions" style="margin-top: 30px; text-align: right;">
            <form action="" method="POST">
                <input type="hidden" name="action" value="cancel_order">
                <input type="hidden" name="order_id" value="<?= htmlspecialchars($order->order_id) ?>">
                <button type="submit" class="btn btn-primary" style="background-color: #dc3545; color: white;" onclick="return confirm('Are you sure you want to cancel this order? This action cannot be undone.');">
                    Cancel Order
                </button>
            </form>
        </div>
    <?php endif; ?>

</div>

<?php include $project_root.'components/footer.php'; ?>