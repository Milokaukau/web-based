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
// Notice we pass null for member_id so it fetches any order cross-member
$order = getOrderById($order_id, null); 
$items = getOrderItems($order_id);

if (!$order) {
    header("Location: order_listing.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>NOAIR — Order Details</title>
    <link rel="stylesheet" href="/css/admin.css">
</head>
<body>
<div class="topbar">
    <div class="topbar-brand">NOAIR</div>
</div>

<div class="layout">
    <!-- Include your sidebar HTML directly here like in the other pages -->

    <div class="content">
        <section class="section-container">
            <div class="section-header">
                <a href="order_listing.php" class="btn-outline" style="margin-bottom: 20px; font-size: 0.8rem;">← Back to Orders</a>
                <h1 class="admin-section-title">Order #<?= htmlspecialchars($order->order_id) ?></h1>
                <div class="line"></div>
            </div>

            <!-- Start building your Order Items & Summary using your div cards here -->
            <div class="feature-card-wrap" style="justify-content: flex-start;">
                <div class="feature-card" style="width: 100%; max-width: 800px; text-align: left;">
                    <h3 style="margin-bottom: 12px;">Customer: <?= htmlspecialchars($order->member_id) /* Or joining member name */ ?></h3>
                    <p>Status: <?= htmlspecialchars($order->order_status) ?></p>
                    <p>Total: $<?= number_format($order->amount, 2) ?></p>
                </div>
            </div>
            
        </section>
    </div>
</div>
</body>
</html>