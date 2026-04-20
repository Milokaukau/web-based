<?php $active = $active ?? ''; ?>
<nav class="sidebar">
    <div class="nav-section">Catalogue</div>
    <a class="nav-item <?= $active === 'product' ? 'active' : '' ?>" href="../pages/product_maintenance.php">
        <span class="nav-icon">&#128230;</span> Product
    </a>
 
    <div class="nav-section">Sales</div>
    <a class="nav-item <?= $active === 'order' ? 'active' : '' ?>" href="../pages/order_listing.php">
        <span class="nav-icon">&#128203;</span> Order
    </a>
    <a class="nav-item <?= $active === 'member' ? 'active' : '' ?>" href="../logic/member.php">
        <span class="nav-icon">&#128101;</span> Member
    </a>
</nav>
 