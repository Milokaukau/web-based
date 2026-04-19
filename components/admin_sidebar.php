<?php
// Ensure $is_superadmin is set
$is_superadmin = (isset($_SESSION['is_superadmin']) && $_SESSION['is_superadmin'] == 1);

// Helper to determine active link
$current_uri = $_SERVER['REQUEST_URI'];
function is_active($path, $current_uri) {
    return (strpos($current_uri, $path) !== false) ? 'active' : '';
}
?>
<!-- SIDEBAR -->
<div class="sidebar">
    <div class="sidebar-section">Main</div>
    <a class="nav-link <?= is_active('admin.php?page=members', $current_uri) ?>" href="/pages/admin/admin.php?page=members">
        <span class="nav-icon">&#128101;</span> Members
    </a>
    <a class="nav-link <?= is_active('order_listing.php', $current_uri) ?>" href="/pages/admin/order_listing.php">
        <span class="nav-icon">&#128230;</span> Orders
    </a>
    <a class="nav-link <?= is_active('admin.php?page=stock', $current_uri) ?>" href="/pages/admin/admin.php?page=stock">
        <span class="nav-icon">&#128202;</span> Stock
    </a>

    <div class="sidebar-section">Management</div>
    
    <!-- Only show Admins link to super admins -->
    <?php if ($is_superadmin): ?>
    <a class="nav-link <?= is_active('admin_list.php', $current_uri) ?>" href="/pages/admin/admin_list.php">
        <span class="nav-icon">&#128110;</span> Admins
    </a>
    <?php endif; ?>
    
    <a class="nav-link <?= is_active('category_list.php', $current_uri) ?>" href="/pages/admin/category_list.php">
        <span class="nav-icon">&#128193;</span> Categories
    </a>

    <div class="sidebar-section">Analytics</div>
    <a class="nav-link <?= is_active('admin.php?page=charts', $current_uri) ?>" href="/pages/admin/admin.php?page=charts">
        <span class="nav-icon">&#128202;</span> Data Charts
    </a>
    
    <div class="sidebar-section">Account</div>
    <a class="nav-link <?= is_active('admin.php?page=profile', $current_uri) ?>" href="/pages/admin/admin.php?page=profile">
        <span class="nav-icon">&#9881;</span> Admin Profile
    </a>
</div>