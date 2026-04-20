<?php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require_once $project_root . "config.php";
require_once $project_root . "logic/auth_helper.php";
require_once $project_root . "database/order.php";

if (!isAdmin()) {
    header("Location: /pages/admin/login.php");
    exit;
}

$order_id = $_GET['id'] ?? null;
$order = getOrderById($order_id, null); 
$items = getOrderItems($order_id);

if (!$order) {
    header("Location: order_listing.php");
    exit;
}

$order_status_labels = [
    'pending_payment' => 'Pending Payment', 'confirmed' => 'Confirmed',
    'in_delivery' => 'In Delivery', 'delivered' => 'Delivered',
    'completed' => 'Completed', 'cancelled' => 'Cancelled',
    'pending_refund' => 'Pending Refund', 'refunded' => 'Refunded',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>NOAIR — Order #<?= htmlspecialchars($order->order_id) ?></title>
    <link rel="stylesheet" href="/css/admin.css">
    <link rel="stylesheet" href="/css/components/order_listing.css">
</head>
<body>
<div class="topbar">
    <div class="topbar-brand">NOAIR</div>
    <div class="topbar-right">
        <span class="topbar-clock" id="clock"></span>
        <div class="topbar-user">
            <div class="avatar-sm">AD</div>
            <span class="topbar-name"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></span>
        </div>
        <a href="/pages/logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<div class="layout">
    <?php include $project_root . "components/admin_sidebar.php"; ?>

    <div class="content">
        <section class="section-container">
            <div class="section-header">
                <a href="order_listing.php" class="btn-outline" style="margin-bottom: 20px; font-size: 0.8rem; display: inline-block;">← Back to Orders</a>
                <h1 class="admin-section-title">Order #<?= htmlspecialchars($order->order_id) ?></h1>
                <div class="line"></div>
            </div>

            <!-- Order Overview & Live Status Toggle -->
            <div style="display: flex; gap: 24px; margin-bottom: 24px;">
                
                <!-- Card 1: Overview + Manage Status -->
                <!-- Note the added max-width: none to override admin.css generic behavior -->
                <div class="feature-card" style="flex: 1; max-width: none; text-align: left; padding: 24px; background: #fff; border-radius: 12px; border: 1px solid var(--border-card); box-shadow: 0 4px 16px rgba(0,0,0,0.04);">
                    <h3 style="margin-bottom: 16px; color: var(--text-dark); border-bottom: 1px solid var(--border-card); padding-bottom: 8px;">Order Overview</h3>
                    
                    <div style="margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between;">
                        <strong>Date:</strong> 
                        <span><?= date('M d, Y h:i A', strtotime($order->created_at)) ?></span>
                    </div>
                    
                    <div style="margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between;">
                        <strong>Payment Method:</strong> 
                        <span><?= !empty($order->payment_method) ? htmlspecialchars(ucwords(str_replace('_', ' ', $order->payment_method))) : '-' ?></span>
                    </div>
                    
                    <div style="margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between;">
                        <strong>Payment Status:</strong> 
                        <span class="badge <?= $order->payment_status === 'success' ? 'badge-valid' : 'badge-invalid' ?>"><?= htmlspecialchars(strtoupper($order->payment_status ?? 'UNKNOWN')) ?></span>
                    </div>
                    
                    <div style="margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between;">
                        <strong>Total Amount:</strong> 
                        <span style="font-weight: 700; color: var(--coral); font-size: 1.1rem;">RM<?= number_format($order->amount, 2) ?></span>
                    </div>

                    <!-- Modification action grouped in overview (On the same line) -->
                    <div style="margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--border-card); display: flex; align-items: center; justify-content: space-between;">
                        <strong>Modify Status:</strong>
                        <select class="filter-sel status-dropdown" data-order-id="<?= htmlspecialchars($order->order_id) ?>" style="width: auto; min-width: 140px; margin: 0; font-size: 0.9rem; padding: 6px 10px;">
                            <?php foreach ($order_status_labels as $val => $label): ?>
                                <option value="<?= $val ?>" <?= ($order->order_status === $val) ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Card 2: Customer Information -->
                <!-- Note the added max-width: none -->
                <div class="feature-card" style="flex: 1; max-width: none; text-align: left; padding: 24px; background: #fff; border-radius: 12px; border: 1px solid var(--border-card); box-shadow: 0 4px 16px rgba(0,0,0,0.04);">
                    <h3 style="margin-bottom: 16px; color: var(--text-dark); border-bottom: 1px solid var(--border-card); padding-bottom: 8px;">Customer Information</h3>
                    
                    <div style="margin-bottom: 16px;">
                        <span style="color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">MEMBER ID</span>
                        <div style="font-weight: 600; font-size: 1.1rem;">#<?= htmlspecialchars($order->member_id) ?></div>
                    </div>
                    
                    <div style="margin-bottom: 16px;">
                        <span style="color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">FULL NAME</span>
                        <div style="font-weight: 500; font-size: 1.05rem;"><?= htmlspecialchars($order->member_name ?? 'Unknown User') ?></div>
                    </div>
                    
                    <div style="margin-bottom: 8px;">
                        <span style="color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">EMAIL ADDRESS</span>
                        <div><a href="mailto:<?= htmlspecialchars($order->member_email ?? '') ?>" style="color: var(--text-dark); text-decoration: none;"><?= htmlspecialchars($order->member_email ?? 'N/A') ?></a></div>
                    </div>
                </div>
            </div>

            <!-- Product Items List -->
            <!-- Calculate total quantities across all items for context -->
            <?php 
            $total_products = 0;
            foreach ($items as $item) $total_products += $item->quantity; 
            ?>
            <h2 class="admin-section-title" style="font-size: 1.3rem; margin-bottom: 16px;">
                Items in this Order <span style="font-size: 0.9rem; font-weight: 500; color: var(--text-muted);">(Total: <?= $total_products ?> items)</span>
            </h2>
            
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 10%;">Image</th>
                            <th style="width: 35%;">Product Details</th>
                            <th style="width: 15%; text-align: center;">Price</th>
                            <th style="width: 15%; text-align: center;">Quantity</th>
                            <th style="width: 20%; text-align: right;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $row_idx = 1; foreach ($items as $item): ?>
                        <tr>
                            <td style="color: var(--text-muted); font-weight: 600;"><?= $row_idx++ ?></td>
                            <td>
                                <img src="/images/<?= htmlspecialchars($item->photo) ?>" alt="<?= htmlspecialchars($item->product_name) ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-card);">
                            </td>
                            <td>
                                <div style="font-weight: 600; color: var(--text-dark);"><?= htmlspecialchars($item->product_name) ?></div>
                                <?php if (!empty($item->color_name)): ?>
                                    <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px;">Color: <?= htmlspecialchars($item->color_name) ?></div>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">RM<?= number_format((float)$item->purchase_price, 2) ?></td>
                            <td style="text-align: center; font-weight: 500;"><?= htmlspecialchars($item->quantity) ?></td>
                            <td style="text-align: right; font-weight: 600; color: var(--coral);">
                                RM<?= number_format(((float)$item->purchase_price * (int)$item->quantity), 2) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
        </section>
    </div>
</div>

<?php include $project_root . "components/admin_footer.php"; ?>
<script src="/js/admin.js"></script>
<script>
document.querySelectorAll('.status-dropdown').forEach(function(dropdown) {
    dropdown.addEventListener('change', function() {
        const orderId = this.dataset.orderId;
        const newStatus = this.value;
        const originalShadow = this.style.boxShadow;
        
        this.style.boxShadow = "0 0 0 3px rgba(243, 158, 158, 1)"; 

        const formData = new FormData();
        formData.append('order_id', orderId);
        formData.append('status', newStatus);

        fetch('/pages/admin/order_listing.php', {
            method: 'POST',
            body: formData
        }).then(response => {
            setTimeout(() => { this.style.boxShadow = originalShadow; }, 500);
        }).catch(err => {
            alert('Failed to update order status.');
            this.style.boxShadow = originalShadow;
        });
    });
});
</script>
</body>
</html>