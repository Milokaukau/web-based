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
        <span class="status-badge status-<?= htmlspecialchars($order->payment_status) ?>">
            <?= htmlspecialchars($order->payment_status) ?>
        </span>
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
            <strong><?= htmlspecialchars($payment_labels[$order->payment_method] ?? 'Unknown') ?></strong>
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
                    <img src="/images/tmp.jpg" alt="Product Image">
                </div>
                
                <div class="details-item-center">
                    <div class="item-details">
                        <h3 style="font-weight: 800; font-size: 1.1rem; margin: 0 0 5px 0; text-transform: uppercase;">
                            <?= htmlspecialchars($item->product_name) ?>
                        </h3>
                        <div class="item-meta">
                            Color: <?= htmlspecialchars($item->color_name) ?> <br>
                            Qty: <?= htmlspecialchars($item->quantity) ?> 
                            (@ $<?= number_format($item->price, 2) ?> each)
                        </div>
                    </div>
                </div>

                <div class="details-item-price">
                    $<?= number_format($item->price * $item->quantity, 2) ?>
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
                <span>$0.00</span> </div>
            <div class="total-line grand-total">
                <span>Total</span>
                <span>$<?= number_format($order->amount, 2) ?></span>
            </div>
        </div>
    </div>

</div>

<?php include $project_root.'components/footer.php'; ?>