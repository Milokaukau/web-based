<?php
$project_root = $_SERVER['DOCUMENT_ROOT']."/";
require_once $project_root . "config.php";
require_once $project_root . "logic/auth_helper.php";
require_once $project_root . "database/order.php";

// Force member login
requireMember();

$member_id = $_SESSION['member_id'];
$orders = getOrderListByMember($member_id);

$_title = "My Orders";
require_once $project_root . "components/header.php";
?>

<link rel="stylesheet" href="/css/style.css">

<div class="orders-container">
    <div class="orders-header">
        <h1>My Orders</h1>
        <div class="line"></div>
    </div>

    <?php if (empty($orders)): ?>
        <div class="empty-orders">
            <div class="icon">📦</div>
            <h2>No orders yet</h2>
            <p>Looks like you haven't placed any orders with us. Start your hydration journey today!</p>
            <a href="/pages/shop.php" class="btn-primary" style="display:inline-block; padding:12px 30px; text-decoration:none;">Go to Shop</a>
        </div>
    <?php else: ?>
        <?php 
        // Group by order_id because the query returns one row per product in the order
        $grouped_orders = [];
        foreach ($orders as $o) {
            if (!isset($grouped_orders[$o->order_id])) {
                $grouped_orders[$o->order_id] = [
                    'id' => $o->order_id,
                    'date' => $o->created_at,
                    'amount' => $o->amount,
                    'status' => $o->status,
                    'items' => []
                ];
            }
            $grouped_orders[$o->order_id]['items'][] = [
                'name' => $o->product_name,
                'qty' => $o->quantity,
                'price' => $o->unit_price
            ];
        }
        
        foreach ($grouped_orders as $order): 
            $status_class = 'status-' . strtolower($order['status']);
            $order_date = date('d M Y, H:i', strtotime($order['date']));
        ?>
            <div class="order-card">
                <div class="order-top">
                    <div class="order-meta-info">
                        <span class="order-id">Order #<?= htmlspecialchars($order['id']) ?></span>
                        <span class="order-date"><?= $order_date ?></span>
                    </div>
                    <span class="order-status <?= $status_class ?>"><?= ucfirst(htmlspecialchars($order['status'])) ?></span>
                </div>

                <div class="order-content">
                    <table class="invoice-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-center">Qty</th>
                                <th class="text-right">Unit Price</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order['items'] as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['name']) ?></td>
                                    <td class="text-center"><?= $item['qty'] ?></td>
                                    <td class="text-right">RM <?= number_format($item['price'], 2) ?></td>
                                    <td class="text-right">RM <?= number_format($item['price'] * $item['qty'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="order-footer">
                    <div class="total-summary">
                        <span class="total-label">Subtotal</span>
                        <span class="subtotal-amount">RM <?= number_format($order['amount'], 2) ?></span>
                    </div>
                    <div class="total-summary grand-total">
                        <span class="total-label">Grand Total</span>
                        <span class="total-amount">RM <?= number_format($order['amount'], 2) ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once $project_root . "components/footer.php"; ?>
