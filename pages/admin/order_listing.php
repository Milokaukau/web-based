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
    <title>NOAIR — Order Management</title>
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
    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="sidebar-section">Main</div>
        <a class="nav-link" href="/pages/admin/admin.php?page=members"><span class="nav-icon">&#128101;</span> Members</a>
        <!-- Set this link as active -->
        <a class="nav-link active" href="/pages/admin/order_listing.php"><span class="nav-icon">&#128230;</span> Orders</a>
        <a class="nav-link" href="/pages/admin/admin.php?page=stock"><span class="nav-icon">&#128202;</span> Stock</a>

        <div class="sidebar-section">Management</div>
        <a class="nav-link" href="/pages/admin/admin_list.php"><span class="nav-icon">&#128110;</span> Admins</a>
        <a class="nav-link" href="/pages/admin/category_list.php"><span class="nav-icon">&#128193;</span> Categories</a>

        <div class="sidebar-section">Analytics</div>
        <a class="nav-link" href="/pages/admin/admin.php?page=charts"><span class="nav-icon">&#128202;</span> Data Charts</a>
        <div class="sidebar-section">Account</div>
        <a class="nav-link" href="/pages/admin/admin.php?page=profile"><span class="nav-icon">&#9881;</span> Admin Profile</a>
    </div>

    <!-- CONTENT -->
    <div class="content">

        <section class="section-container">
            <div class="section-header" style="display: flex; justify-content: space-between; align-items: flex-end;">
                <div>
                    <h1 class="admin-section-title">Order Management</h1>
                    <p class="admin-section-sub">View and update customer order statuses</p>
                    <div class="line"></div>
                </div>
            </div>

            <!-- Toolbar / Filter -->
            <div class="toolbar" style="margin-bottom: 24px;">
                <form method="GET" style="display: flex; gap: 10px; width: 100%; align-items: center;">
                    <select name="member_id" class="filter-sel member-filter" onchange="this.form.submit()">                        <option value="">All Members...</option>
                        <?php foreach ($members as $member): ?>
                            <option value="<?= $member->id ?>" <?= (isset($_GET['member_id']) && $_GET['member_id'] == $member->id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($member->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <noscript><button type="submit" class="btn-primary" style="padding: 8px 16px;">Filter</button></noscript>

                    <?php if (isset($_GET['member_id'])): ?>
                        <a href="order_listing.php" class="btn-outline" style="padding: 6px 16px; font-size: 0.8rem;">Clear Filter</a>
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
                                        $<?= number_format((float)$data->amount, 2) ?>
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

<?php include $project_root . "components/footer.php"; ?>
<script src="/js/admin.js"></script>
<script>
document.querySelectorAll('.status-dropdown').forEach(function(dropdown) {
    dropdown.addEventListener('change', function() {
        const orderId = this.dataset.orderId;
        const newStatus = this.value;
        const originalShadow = this.style.boxShadow;
        
        // 1. Immediately apply the pink radius visual effect using your coral theme
        this.style.boxShadow = "0 0 0 3px rgba(243, 158, 158, 1)"; 

        // 2. Submit the data using Fetch API behind the scenes (No page reload)
        const formData = new FormData();
        formData.append('order_id', orderId);
        formData.append('status', newStatus);

        fetch('order_listing.php', {
            method: 'POST',
            body: formData
        }).then(response => {
            setTimeout(() => {
                this.style.boxShadow = originalShadow;
            }, 500);
        }).catch(err => {
            alert('Failed to update order status.');
            this.style.boxShadow = originalShadow;
        });
    });
});
</script>
</body>
</html>