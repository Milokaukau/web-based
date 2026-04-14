
<?php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";

require_once $project_root . "config.php";
require_once $project_root . "logic/auth_helper.php";
require_once $project_root . "database/admin.php";
require_once $project_root . "database/admin_action.php";


if (!isAdmin()) {
    header("Location: /pages/admin/login.php");
    exit;
}

// Inputs
$search   = trim($_GET['search'] ?? '');
$status   = $_GET['status'] ?? '';
$page_num = max(1, (int)($_GET['page_num'] ?? 1));
$per_page = 10;

// Data
list($members, $total) = getMembersData($search, $status, $page_num, $per_page);
$total_pages = max(1, ceil($total / $per_page));

$stats = getStats();
$highAttempts = getHighAttempts();

$_title = 'NOAIR Admin';

// Active page
$active_page = $_GET['page'] ?? 'members';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>NOAIR — Admin Panel</title>
<link rel="stylesheet" href="/css/admin.css">
</head>
<body>

<!-- TOPBAR -->
<div class="topbar">
    <div class="topbar-brand">NO<span>AI</span>R</div>
    <div class="topbar-right">
        <span class="topbar-clock" id="clock"></span>
        <div class="topbar-user">
            <div class="avatar-sm">AD</div>
            <span class="topbar-name"><?= htmlspecialchars($_SESSION["admin_name"] ?? "Admin") ?></span>
        </div>
        <a href="/pages/logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<!-- LAYOUT -->
<div class="layout">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="sidebar-section">Main</div>
        <a class="nav-link <?= $active_page === 'members' ? 'active' : '' ?>" href="?page=members">👥 Members</a>
        <a class="nav-link <?= $active_page === 'orders' ? 'active' : '' ?>" href="?page=orders">📦 Orders</a>
        <a class="nav-link <?= $active_page === 'stock' ? 'active' : '' ?>" href="?page=stock">📦 Stock</a>
        <div class="sidebar-section">Account</div>
        <a class="nav-link <?= $active_page === 'profile' ? 'active' : '' ?>" href="?page=profile">⚙ Admin Profile</a>
    </div>

    <!-- CONTENT -->
    <div class="content">

        <?php if (!empty($_SESSION['flash'])): ?>
            <div class="alert alert-<?= $_SESSION['flash']['type'] ?>">
                <?= htmlspecialchars($_SESSION['flash']['message']) ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <!-- ===== MEMBERS ===== -->
        <?php if ($active_page === 'members'): ?>

        <div class="page active">
            <div class="page-header">
                <h1>Member Maintenance</h1>
                <p>Manage all registered NOAIR members</p>
            </div>

            <!-- Stats -->
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-label">Total Members</div>
                    <div class="stat-num"><?= $stats->total ?? 0 ?></div>
                </div>
                <div class="stat-card s-valid">
                    <div class="stat-label">Active</div>
                    <div class="stat-num"><?= $stats->active_count ?? 0 ?></div>
                </div>
                <div class="stat-card s-locked">
                    <div class="stat-label">Locked</div>
                    <div class="stat-num"><?= $stats->locked_count ?? 0 ?></div>
                </div>
                <div class="stat-card s-invalid">
                    <div class="stat-label">High Attempts (3+)</div>
                    <div class="stat-num"><?= $highAttempts ?></div>
                </div>
            </div>

            <!-- Search -->
            <form method="GET" action="">
                <input type="hidden" name="page" value="members">
                <div class="toolbar">
                    <div class="search-wrap">
                        <span class="search-icon">&#128269;</span>
                        <input type="text" name="search" placeholder="Search name or email..."
                               value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <select name="status" class="filter-sel" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="locked" <?= $status === 'locked' ? 'selected' : '' ?>>Locked</option>
                    </select>
                    <button type="submit" class="btn-primary">Search</button>
                    <?php if ($search || $status): ?>
                        <a href="?page=members" class="btn-outline">Clear</a>
                    <?php endif; ?>
                </div>
            </form>

            <!-- Table -->
            <div class="table-wrap">
                <?php if (empty($members)): ?>
                    <div class="empty-state">No members found.</div>
                <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th><th>Name</th><th>Email</th>
                            <th>Phone</th><th>Gender</th>
                            <th>Attempts</th><th>Status</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($members as $m):
                            $isLocked = !empty($m->locked_until) && strtotime($m->locked_until) > time();
                        ?>
                        <tr>
                            <td><?= $m->id ?></td>
                            <td><?= htmlspecialchars($m->name) ?></td>
                            <td><?= htmlspecialchars($m->email) ?></td>
                            <td><?= htmlspecialchars($m->phone) ?></td>
                            <td><?= htmlspecialchars($m->gender) ?></td>
                            <td><?= $m->login_attempts ?></td>
                            <td>
                                <span class="badge <?= $isLocked ? 'badge-locked' : 'badge-valid' ?>">
                                    <?= $isLocked ? 'Locked' : 'Active' ?>
                                </span>
                            </td>
                            <td>
    <a href="?page=member_detail&id=<?= $m->id ?>" class="act-btn">View</a>

    <?php if ($isLocked): ?>
        <a href="?action=unlock&id=<?= $m->id ?>&page=members"
           class="act-btn unlock"
           onclick="return confirm('Unlock this member?')">Unlock</a>
    <?php else: ?>
        <a href="?action=lock&id=<?= $m->id ?>&page=members"
           class="act-btn lock"
           onclick="return confirm('Lock this member?')">Lock</a>
    <?php endif; ?>

    <a href="?action=delete&id=<?= $m->id ?>&page=members"
       class="act-btn del"
       onclick="return confirm('Delete this member?')">Delete</a>
</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>

        </div>

        <!-- ===== MEMBER DETAIL ===== -->
        <?php elseif ($active_page === 'member_detail' && isset($_GET['id'])): ?>

        <?php $member = getMemberById((int)$_GET['id']); ?>

        <div class="page active">
            <h1>Member Detail</h1>

            <?php if ($member): ?>
                <div class="profile-wrap">
    <div class="profile-card">
        
        <div class="profile-avt">
            <?= strtoupper(substr($member->name ?? "MB", 0, 2)) ?>
        </div>

        <div class="profile-row">
            <span class="profile-lbl">Name</span>
            <span class="profile-val"><?= htmlspecialchars($member->name ?? '-') ?></span>
        </div>

        <div class="profile-row">
            <span class="profile-lbl">Email</span>
            <span class="profile-val"><?= htmlspecialchars($member->email ?? '-') ?></span>
        </div>

        <div class="profile-row">
            <span class="profile-lbl">Phone</span>
            <span class="profile-val"><?= htmlspecialchars($member->phone ?? '-') ?></span>
        </div>

        <div class="profile-row">
            <span class="profile-lbl">Attempts</span>
            <span class="profile-val"><?= $member->login_attempts ?? 0 ?></span>
        </div>

    </div>
</div>
            <?php else: ?>
                <p>Member not found.</p>
            <?php endif; ?>

        </div>

        <!-- ===== ORDERS ===== -->
        <?php elseif ($active_page === 'orders'): ?>
<?php $orders = getOrders(); ?>

<div class="page active">
    <div class="page-header">
        <h1>Order Listing</h1>
        <p>All customer orders</p>
    </div>

    <div class="table-wrap">
        <?php if (empty($orders)): ?>
            <div class="empty-state">No orders found.</div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Member</th>
                    <th>Total (RM)</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                <tr>
                    <td>#<?= $o->id ?></td>
                    <td><?= htmlspecialchars($o->username ?? 'Guest') ?></td>
                    <td><?= number_format($o->total_amount ?? 0, 2) ?></td>
                    <td>
                        <span class="badge badge-<?= 
                            $o->status === 'completed' ? 'valid' : 
                            ($o->status === 'cancelled' ? 'invalid' : 'locked') ?>">
                            <?= ucfirst($o->status ?? '-') ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($o->created_at ?? '-') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

        <!-- ===== STOCK ===== -->
        <?php elseif ($active_page === 'stock'): ?>
<?php $products = getProducts(); ?>

<div class="page active">
    <div class="page-header">
        <h1>Stock Management</h1>
        <p>Monitor product inventory levels</p>
    </div>

    <div class="table-wrap">
        <?php if (empty($products)): ?>
            <div class="empty-state">No products found.</div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price (RM)</th>
                    <th>Stock</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p):
                    $stockStatus = $p->stock == 0 ? 'invalid' : ($p->stock < 10 ? 'locked' : 'valid');
                    $stockLabel  = $p->stock == 0 ? 'Out of Stock' : ($p->stock < 10 ? 'Low Stock' : 'In Stock');
                ?>
                <tr>
                    <td><?= htmlspecialchars($p->name ?? '-') ?></td>
                    <td><?= htmlspecialchars($p->category ?? '-') ?></td>
                    <td><?= number_format($p->price ?? 0, 2) ?></td>
                    <td><?= $p->stock ?></td>
                    <td><span class="badge badge-<?= $stockStatus ?>"><?= $stockLabel ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

        <!-- ===== PROFILE ===== -->
        <?php elseif ($active_page === 'profile'): ?>

        <?php $admin = getAdmin($_SESSION['admin_id'] ?? 1); ?>

        <div class="profile-wrap">
    <div class="profile-card">
        <div class="profile-avt"><?= strtoupper(substr($admin->name ?? "AD", 0, 2)) ?></div>

        <div class="profile-row">
            <span class="profile-lbl">Name</span>
            <span class="profile-val"><?= htmlspecialchars($admin->name ?? '-') ?></span>
        </div>

        <div class="profile-row">
            <span class="profile-lbl">Email</span>
            <span class="profile-val"><?= htmlspecialchars($admin->email ?? '-') ?></span>
        </div>

        <div class="profile-row">
            <span class="profile-lbl">Role</span>
            <span class="profile-val">Super Admin</span>
        </div>

        <div class="profile-row">
            <span class="profile-lbl">Status</span>
            <span class="profile-val">
                <span class="badge badge-valid">Active</span>
            </span>
        </div>
    </div>
</div>

        <?php endif; ?>

    </div>
</div>

<?php 
// Ensure footer is placed at the most bottom
require $project_root."components/footer.php"; 
?>
<script src="/js/admin.js"></script>
</body>
</html>
