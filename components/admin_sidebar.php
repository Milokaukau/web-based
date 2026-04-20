<?php
// Ensure $is_superadmin is set
$is_superadmin = (isset($_SESSION['is_superadmin']) && $_SESSION['is_superadmin'] == 1);

// Helper to determine active link
$current_uri = $_SERVER['REQUEST_URI'];
function is_active($paths, $current_uri) {
    if (!is_array($paths)) {
        $paths = [$paths];
    }
    foreach ($paths as $path) {
        if (strpos($current_uri, $path) !== false) {
            return 'active';
        }
    }
    return '';
}
?>
<!-- SIDEBAR -->
<div class="sidebar">
    <div class="sidebar-section">Main</div>
    
    <a class="nav-link <?= is_active('page=members', $current_uri) ?>" href="/pages/admin/admin.php?page=members">
        <span class="nav-icon">&#128101;</span> Members
    </a>
    
    <!-- Match BOTH order listing and order details to keep the Orders tab active -->
    <a class="nav-link <?= is_active(['order_listing.php', 'order_details_admin.php'], $current_uri) ?>" href="/pages/admin/order_listing.php">
        <span class="nav-icon">&#128230;</span> Orders
    </a>
    
    <a class="nav-link <?= is_active('page=stock', $current_uri) ?>" href="/pages/admin/admin.php?page=stock">
        <span class="nav-icon">&#128202;</span> Stock
    </a>

    <div class="sidebar-section">Management</div>
    
    <!-- Only show Admins link to super admins -->
    <?php if ($is_superadmin): ?>
    <a class="nav-link <?= is_active(['admin_list.php', 'admin_edit.php', 'admin_add.php'], $current_uri) ?>" href="/pages/admin/admin_list.php">
        <span class="nav-icon">&#128110;</span> Admins
    </a>
    <?php endif; ?>
    
    <a class="nav-link <?= is_active(['category_list.php', 'category_items.php', 'category_add.php'], $current_uri) ?>" href="/pages/admin/category_list.php">
        <span class="nav-icon">&#128193;</span> Categories
    </a>

    <div class="sidebar-section">Analytics</div>
    <a class="nav-link <?= is_active('page=charts', $current_uri) ?>" href="/pages/admin/admin.php?page=charts">
        <span class="nav-icon">&#128202;</span> Data Charts
    </a>
    
    <div class="sidebar-section">Account</div>
    <a class="nav-link <?= is_active(['page=profile', 'change_password.php'], $current_uri) ?>" href="/pages/admin/admin.php?page=profile">
        <span class="nav-icon">&#9881;</span> Admin Profile
    </a>
</div>