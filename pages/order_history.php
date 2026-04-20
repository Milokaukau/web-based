<?php
$project_root = $_SERVER['DOCUMENT_ROOT']."/";
require $project_root."config.php";
require $project_root."logic/order_history.php";

$_title = 'My Orders';
include $project_root.'components/header.php';
// echo "<pre>";
// var_dump($orders);
// echo "</pre>";
?>

<div class="container history-container">
    <h1 class="history-title">My Orders</h1>
    <p class="orders-subtitle">Total Order(s) Placed: <?= count($orders) ?></p>

    <?php if (empty($orders)): ?>
        <p style="text-align: center; color: var(--text-muted); margin-top: 50px;">You haven't placed any orders yet.</p>
    <?php else: ?>

        <?php foreach ($orders as $order): ?>
            <div class="order-card">
                
                <div class="order-header">
                    <div class="order-header-info">
                        <div class="order-info">
                            <span>Order Placed</span>
                            <strong><?= date('M d, Y', strtotime($order->created_at)) ?></strong>
                        </div>
                        <div class="order-info">
                            <span>Total</span>
                            <strong>$<?= number_format($order->amount, 2) ?></strong>
                        </div>
                        <div class="order-info">
                            <span>Order #</span>
                            <strong><?= htmlspecialchars($order->order_id) ?></strong>
                        </div>
                    </div>
                    <div class="order-header-status" style="display: flex; gap: 8px;">
                        <span class="status-badge status-<?= htmlspecialchars($order->order_status) ?>">
                            Status: <?= ucwords(str_replace('_', ' ', htmlspecialchars($order->order_status))) ?>
                        </span>
                    </div>
                </div>

                <?php foreach ($order->items as $item): ?>
                    <div class="order-body">
                        
                        <div class="item-thumbnail">
                            <img src="/images/<?= htmlspecialchars($item->photo) ?>" alt="Product Image Placeholder">
                        </div>

                        <div class="item-center">
                            <div class="item-details">
                                <h3><?= htmlspecialchars($item->product_name) ?></h3>
                                <div class="item-meta">
                                    Color: <?= htmlspecialchars($item->color_name) ?> | Qty: <?= htmlspecialchars($item->quantity) ?>
                                </div>
                            </div>
                        </div>

                        <div class="item-actions" style="margin-left: auto; display: flex; align-items: center; font-weight: 600; color: var(--text-dark, #333); font-size: 1.1rem;">
                            $<?= number_format((float)$item->purchase_price * (int)$item->quantity, 2) ?>
                        </div>

                    </div>
                <?php endforeach; ?>

                <div class="order-footer" style="padding: 15px 20px; border-top: 1px solid var(--border-ultra-light, #eee); display: flex; justify-content: flex-end; border-radius: 0 0 8px 8px;">
                    <a href="order_details.php?id=<?= $order->order_id ?>" class="btn btn-primary" style="padding: 8px 16px; width: max-content;">
                        View order details
                    </a>
                </div>

            </div>
        <?php endforeach; ?>

    <?php endif; ?>

</div>

<?php include $project_root.'components/footer.php'; ?>