<?php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require_once $project_root . "config.php";
require_once $project_root . "logic/auth_helper.php";
require_once $project_root . "logic/admin/order_listing.php";

if (!isAdmin()) {
    header("Location: /pages/admin/login.php");
    exit;
}

// Re-usable order status labels for the exact values in your database
$order_status_labels = [
    'pending_payment' => 'Pending Payment',
    'confirmed'       => 'Confirmed',
    'in_delivery'     => 'In Delivery',
    'delivered'       => 'Delivered',
    'completed'       => 'Completed',
    'cancelled'       => 'Cancelled',
    'pending_refund'  => 'Pending Refund',
    'refunded'        => 'Refunded',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOAIR — Order Maintenance</title>
    <link rel="stylesheet" href="/css/admin.css">
    <link rel="stylesheet" href="/css/components/order_listing.css">
</head>
<body>

<!-- TOPBAR -->
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

    <!-- CONTENT -->
    <div class="content">

        <section class="section-container">
            <div class="section-header" style="display: flex; justify-content: space-between; align-items: flex-end;">
                <div>
                    <h1 class="admin-section-title">Order Maintenance</h1>
                    <p class="admin-section-sub">View and update customer order statuses</p>
                    <div class="line"></div>
                </div>
            </div>

            <!-- Toolbar / Filter -->
            <div class="toolbar order-listing-toolbar">
                <form method="GET" class="filter-form">
                    
                    <!-- 1. Searchable Custom Member Dropdown with ID -->
                    <div id="custom-member-select" class="custom-member-select">
                        <div class="custom-trigger">
                            <span id="selected-member">All Members</span>
                            <span class="arrow">▼</span>
                        </div>
                        <div class="custom-options">
                            <div class="custom-search-container">
                                <input type="text" id="member-search" class="custom-search-input" placeholder="Search ID or name...">
                            </div>
                            <div class="opt-item" data-val="">All Members</div>
                            <?php foreach ($members as $member): ?>
                                <div class="opt-item" data-val="<?= $member->id ?>">
                                    <span class="opt-item-id">#<?= htmlspecialchars($member->id) ?></span>
                                    <?= htmlspecialchars($member->name) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="member_id" id="hidden-member-val" value="<?= htmlspecialchars($_GET['member_id'] ?? '') ?>">
                    </div>

                    <!-- 2. Order Status Filter -->
                    <select name="order_status" class="filter-sel" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <?php foreach ($order_status_labels as $val => $label): ?>
                            <option value="<?= $val ?>" <?= (isset($_GET['order_status']) && $_GET['order_status'] === $val) ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>

                    <!-- 3. Payment Method Filter -->
                    <select name="payment_method" class="filter-sel" onchange="this.form.submit()">
                        <option value="">All Payments</option>
                        <option value="e_wallet" <?= (isset($_GET['payment_method']) && $_GET['payment_method'] === 'e_wallet') ? 'selected' : '' ?>>E-Wallet</option>
                        <option value="online_banking" <?= (isset($_GET['payment_method']) && $_GET['payment_method'] === 'online_banking') ? 'selected' : '' ?>>Online Banking</option>
                        <option value="card" <?= (isset($_GET['payment_method']) && $_GET['payment_method'] === 'card') ? 'selected' : '' ?>>Card</option>
                    </select>

                    <!-- 4. Date Range Filter -->
                    <div class="date-filter-group">
                        <input type="date" name="date_from" class="filter-sel date-input" value="<?= htmlspecialchars($_GET['date_from'] ?? '') ?>" onchange="this.form.submit()">
                        <span class="date-separator">to</span>
                        <input type="date" name="date_to" class="filter-sel date-input" value="<?= htmlspecialchars($_GET['date_to'] ?? '') ?>" onchange="this.form.submit()">
                    </div>

                    <?php if (!empty($_GET['member_id']) || !empty($_GET['order_status']) || !empty($_GET['payment_method']) || !empty($_GET['date_from']) || !empty($_GET['date_to'])): ?>
                        <a href="order_listing.php" class="btn-outline btn-clear-filters">Clear Filters</a>
                    <?php endif; ?>
                </form>
            </div>
            <!-- Table -->
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 10%;">Order ID</th>
                            <th style="width: 15%;">Order Date</th>
                            <th style="width: 20%;">Member</th>
                            <th style="width: 15%;">Payment Method</th>
                            <th style="width: 15%;">Amount</th>
                            <th style="width: 15%;">Order Status</th>
                            <th style="width: 10%; text-align: right;">Action</th> 
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($arr)): ?>
                            <?php foreach ($arr as $data): ?>  
                                <tr>
                                    <td style="font-weight: 700; color: var(--text-dark);">
                                        #<?= htmlspecialchars($data->order_id) ?>
                                    </td>
                                    <td>
                                        <?= date('M d, Y', strtotime($data->created_at)) ?>
                                    </td>
                                    <td style="font-weight: 500;">
                                        <?= htmlspecialchars($data->member_name) ?>
                                    </td>
                                    <td>
                                        <span style="color: var(--text-muted);">
                                            <?= !empty($data->payment_method) ? htmlspecialchars(ucwords(str_replace('_', ' ', $data->payment_method))) : '-' ?>
                                        </span>
                                    </td>
                                    <td style="font-weight: 600; color: var(--coral);">
                                        RM<?= number_format((float)$data->amount, 2) ?>
                                    </td>
                                    
                                    <td>
                                        <select class="filter-sel status-dropdown" data-order-id="<?= htmlspecialchars($data->order_id) ?>">
                                            <?php foreach ($order_status_labels as $val => $label): ?>
                                                <option value="<?= $val ?>" <?= ($data->order_status === $val) ? 'selected' : '' ?>>
                                                    <?= $label ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>

                                    <td style="text-align: right;">
                                        <a href="order_details_admin.php?id=<?= $data->order_id ?>" class="btn-outline" style="padding: 6px 16px; font-size: 0.75rem;">
                                            View
                                        </a>
                                    </td>
                                </tr>   
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7"><div class="empty-state">No orders found for this selection.</div></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </section>
    </div>
</div>

<?php include $project_root . "components/admin_footer.php"; ?>
<script src="/js/admin.js"></script>
<script src="/js/order_listing.js"></script>
</body>
</html>