<?php
/**
 * pages/admin/admin.php
 * Main admin panel router. Each page section is included below.
 */

$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";

require_once $project_root . "config.php";
require_once $project_root . "logic/auth_helper.php";


// ── Database layers (split by concern) ────────────────────────────────────
require_once $project_root . "database/admin.php";
require_once $project_root . "database/member.php";
require_once $project_root . "database/order.php";
require_once $project_root . "database/product.php";

// ── Auth guard ────────────────────────────────────────────────────────────
if (!isAdmin()) {
    header("Location: /pages/admin/login.php");
    exit;
}

// ── Action dispatcher (lock / unlock / add_member / change_pw) ───────────
$action = $_GET['action'] ?? '';

if ($action === 'lock' && isset($_GET['id'])) {
    $id           = (int) $_GET['id'];
    $locked_until = date('Y-m-d H:i:s', strtotime('+100 years'));
    $stmt = db()->prepare("UPDATE tb_member SET locked_until = ? WHERE id = ?");
    $stmt->execute([$locked_until, $id]);
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Member account has been locked.'];
    $redirect = $_GET['rpage'] ?? 'members';
    header("Location: /pages/admin/admin.php?page=$redirect");
    exit;
}

if ($action === 'unlock' && isset($_GET['id'])) {
    $id   = (int) $_GET['id'];
    $stmt = db()->prepare("UPDATE tb_member SET locked_until = NULL, login_attempts = 0 WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Member account has been unlocked.'];
    $redirect = $_GET['rpage'] ?? 'members';
    header("Location: /pages/admin/admin.php?page=$redirect");
    exit;
}

if ($action === 'add_member' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo      = db();
    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';
    $phone    = trim($_POST['phone']    ?? '');
    $gender   = $_POST['gender']        ?? '';

    if ($name && $email && $password) {
        $check = $pdo->prepare("SELECT id FROM tb_member WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Email already exists.'];
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt   = $pdo->prepare("INSERT INTO tb_member (name, email, password, phone, gender, login_attempts) VALUES (?, ?, ?, ?, ?, 0)");
            $stmt->execute([$name, $email, $hashed, $phone ?: null, $gender ?: null]);
            $_SESSION['flash'] = ['type' => 'success', 'message' => "Member '$name' added successfully."];
        }
    } else {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Name, email and password are required.'];
    }
    header("Location: /pages/admin/admin.php?page=members");
    exit;
}

if ($action === 'update_stock' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id    = (int) ($_POST['id']    ?? 0);
    $stock = (int) ($_POST['stock'] ?? 0);
    if ($id > 0 && $stock >= 0) {
        updateStock($id, $stock);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Stock updated successfully.'];
    } else {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Invalid stock value.'];
    }
    header("Location: /pages/admin/admin.php?page=stock");
    exit;
}

if ($action === 'update_order_status' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = (int) ($_POST['id']     ?? 0);
    $status = trim($_POST['status']   ?? '');
    if ($id > 0 && $status) {
        updateOrderStatus($id, $status);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Order status updated.'];
    } else {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Invalid order or status.'];
    }
    header("Location: /pages/admin/admin.php?page=orders");
    exit;
}

if ($action === 'change_pw' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $new  = $_POST['new_password']     ?? '';
    $conf = $_POST['confirm_password'] ?? '';
    if ($new && $new === $conf && strlen($new) >= 6) {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $stmt   = db()->prepare("UPDATE tb_admin SET password = ? WHERE id = ?");
        $stmt->execute([$hashed, $_SESSION['admin_id'] ?? 1]);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Password updated successfully.'];
    } else {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Passwords do not match or are too short (min 6 chars).'];
    }
    header("Location: /pages/admin/admin.php?page=profile");
    exit;
}

// ── Shared page state ─────────────────────────────────────────────────────
$active_page = $_GET['page'] ?? 'members';

// Members-page pagination / filter params (only needed on members page)
$search   = trim($_GET['search'] ?? '');
$status   = $_GET['status']      ?? '';
$page_num = max(1, (int)($_GET['page_num'] ?? 1));
$per_page = 10;

[$members, $total] = getMembersData($search, $status, $page_num, $per_page);
$total_pages  = max(1, ceil($total / $per_page));
$stats        = getMemberStats();
$highAttempts = getHighAttempts();
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

<!-- ── TOPBAR ─────────────────────────────────────────────────────────────── -->
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

<!-- ── LAYOUT ─────────────────────────────────────────────────────────────── -->
<div class="layout">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="sidebar-section">Main</div>
        <a class="nav-link <?= $active_page === 'members'      ? 'active' : '' ?>" href="?page=members">
            <span class="nav-icon">&#128101;</span> Members
        </a>
        <a class="nav-link <?= $active_page === 'orders'       ? 'active' : '' ?>" href="?page=orders">
            <span class="nav-icon">&#128230;</span> Orders
        </a>
        <a class="nav-link <?= $active_page === 'stock'        ? 'active' : '' ?>" href="?page=stock">
            <span class="nav-icon">&#128202;</span> Stock
        </a>
        <div class="sidebar-section">Analytics</div>
        <a class="nav-link <?= $active_page === 'charts'       ? 'active' : '' ?>" href="?page=charts">
            <span class="nav-icon">&#128202;</span> Data Charts
        </a>
        <div class="sidebar-section">Account</div>
        <a class="nav-link <?= $active_page === 'profile'      ? 'active' : '' ?>" href="?page=profile">
            <span class="nav-icon">&#9881;</span> Admin Profile
        </a>
    </div>

    <!-- CONTENT -->
    <div class="content">

        <!-- Flash message -->
        <?php if (!empty($_SESSION['flash'])): ?>
            <div class="alert alert-<?= $_SESSION['flash']['type'] ?>">
                <?= htmlspecialchars($_SESSION['flash']['message']) ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>


        <!-- ═══════════════════════════════════════════════════════
             MEMBERS
        ════════════════════════════════════════════════════════ -->
        <?php if ($active_page === 'members'): ?>

        <section class="section-container">

            <div class="section-header">
                <h1 class="admin-section-title">Member Maintenance</h1>
                <p class="admin-section-sub">Manage all registered NOAIR members</p>
                <div class="line"></div>
            </div>

            <!-- Stats cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Members</div>
                    <div class="stat-num"><?= $stats->total ?? 0 ?></div>
                </div>
                <div class="stat-card s-valid">
                    <div class="stat-label">Active</div>
                    <div class="stat-num"><?= $stats->active_count ?? 0 ?></div>
                </div>
                <div class="stat-card s-locked">
                    <div class="stat-label">Blocked</div>
                    <div class="stat-num"><?= $stats->locked_count ?? 0 ?></div>
                </div>
                <div class="stat-card s-invalid">
                    <div class="stat-label">High Attempts (3+)</div>
                    <div class="stat-num"><?= $highAttempts ?></div>
                </div>
            </div>

            <!-- Search toolbar -->
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
                        <option value="locked" <?= $status === 'locked' ? 'selected' : '' ?>>Blocked</option>
                    </select>
                    <button type="submit" class="btn-primary">Search</button>
                    <?php if ($search || $status): ?>
                        <a href="?page=members" class="btn-outline">Clear</a>
                    <?php endif; ?>
                </div>
            </form>

            <!-- Members table -->
            <div class="table-wrap">
                <?php if (empty($members)): ?>
                    <div class="empty-state"><p>No members found.</p></div>
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
                            <td><?= htmlspecialchars($m->phone ?? '-') ?></td>
                            <td><?= htmlspecialchars($m->gender ?? '-') ?></td>
                            <td><?= $m->login_attempts ?></td>
                            <td>
                                <span class="badge <?= $isLocked ? 'badge-locked' : 'badge-valid' ?>">
                                    <?= $isLocked ? 'Blocked' : 'Active' ?>
                                </span>
                            </td>
                            <td>
                                <a href="?page=member_detail&id=<?= $m->id ?>" class="act-btn">View</a>
                                <?php if ($isLocked): ?>
                                    <a href="?action=unlock&id=<?= $m->id ?>&rpage=members"
                                       class="act-btn unlock"
                                       onclick="return confirm('Unblock this member?')">Unblock</a>
                                <?php else: ?>
                                    <a href="?action=lock&id=<?= $m->id ?>&rpage=members"
                                       class="act-btn lock"
                                       onclick="return confirm('Block this member?')">Block</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                        <a href="?page=members&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>&page_num=<?= $p ?>"
                           class="page-btn <?= $p === $page_num ? 'active' : '' ?>"><?= $p ?></a>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>

                <?php endif; ?>
            </div>

        </section>


        <!-- ═══════════════════════════════════════════════════════
             MEMBER DETAIL
        ════════════════════════════════════════════════════════ -->
        <?php elseif ($active_page === 'member_detail'): ?>

        <?php $member = getMemberById((int)($_GET['id'] ?? 0)); ?>

        <section class="section-container">
            <div class="section-header">
                <a href="?page=members" class="btn-outline" style="margin-bottom:10px">← Back</a>
                <h1 class="admin-section-title">Member Detail</h1>
                <div class="line"></div>
            </div>

            <?php if ($member): ?>
            <?php $isLocked = !empty($member->locked_until) && strtotime($member->locked_until) > time(); ?>
            <div class="feature-card-wrap">
                <div class="feature-card">
                    <div class="member-avatar-wrap">
                        <div class="member-avatar"><?= strtoupper(substr($member->name, 0, 2)) ?></div>
                    </div>
                    <div class="member-name"><?= htmlspecialchars($member->name) ?></div>
                    <div class="member-role"><?= htmlspecialchars($member->email) ?></div>

                    <div class="profile-rows">
                        <div class="profile-row">
                            <span class="profile-lbl">Phone</span>
                            <span class="profile-val"><?= htmlspecialchars($member->phone ?? '-') ?></span>
                        </div>
                        <div class="profile-row">
                            <span class="profile-lbl">Gender</span>
                            <span class="profile-val"><?= htmlspecialchars($member->gender ?? '-') ?></span>
                        </div>
                        <div class="profile-row">
                            <span class="profile-lbl">Login Attempts</span>
                            <span class="profile-val"><?= $member->login_attempts ?></span>
                        </div>
                        <div class="profile-row">
                            <span class="profile-lbl">Status</span>
                            <span class="profile-val">
                                <span class="badge <?= $isLocked ? 'badge-locked' : 'badge-valid' ?>">
                                    <?= $isLocked ? 'Blocked' : 'Active' ?>
                                </span>
                            </span>
                        </div>
                    </div>

                    <div style="display:flex;gap:10px;justify-content:center;margin-top:16px;">
                        <?php if ($isLocked): ?>
                            <a href="?action=unlock&id=<?= $member->id ?>&rpage=members"
                               class="act-btn unlock"
                               onclick="return confirm('Unblock this member?')">Unblock Account</a>
                        <?php else: ?>
                            <a href="?action=lock&id=<?= $member->id ?>&rpage=members"
                               class="act-btn lock"
                               onclick="return confirm('Block this member?')">Block Account</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php else: ?>
                <div class="empty-state"><p>Member not found.</p></div>
            <?php endif; ?>
        </section>


        <!-- ═══════════════════════════════════════════════════════
             ORDERS
        ════════════════════════════════════════════════════════ -->
        <?php elseif ($active_page === 'orders'): ?>

        <?php
            $orders        = getOrders();
            $lowStockAlert = getLowStockProducts(10);
        ?>

        <section class="section-container">

            <div class="section-header">
                <h1 class="admin-section-title">Order Listing</h1>
                <p class="admin-section-sub">All customer orders at a glance</p>
                <div class="line"></div>
            </div>

            <!-- ── Low-Stock Alert Banner ───────────────────────────────── -->
            <?php if (!empty($lowStockAlert)): ?>
            <div class="alert alert-error" style="margin-bottom:20px;">
                ⚠️ <strong><?= count($lowStockAlert) ?> product(s)</strong> are low in stock (≤10 units):
                <?php foreach ($lowStockAlert as $lp): ?>
                    <span style="margin-left:8px;">
                        <strong><?= htmlspecialchars($lp->name) ?></strong>
                        <?php if (!empty($lp->color_name)): ?>
                            · <?= htmlspecialchars($lp->color_name) ?>
                        <?php endif; ?>
                        (<?= $lp->stock ?> left)
                    </span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- ── Orders Table ────────────────────────────────────────── -->
            <div class="table-wrap">
                <?php if (empty($orders)): ?>
                    <div class="empty-state"><p>No orders found.</p></div>
                <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Member</th>
                            <th>Total (RM)</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Update Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $o): ?>
                        <tr>
                            <td>#<?= $o->id ?></td>
                            <td><?= htmlspecialchars($o->username ?? 'Guest') ?></td>
                            <td>RM <?= number_format($o->amount ?? 0, 2) ?></td>
                            <td>
                                <span class="badge badge-<?=
                                    $o->status === 'delivered'       ? 'valid'   :
                                    ($o->status === 'cancelled'      ? 'invalid' :
                                    ($o->status === 'in_delivery'    ? 'locked'  : 'locked')) ?>">
                                    <?= ucfirst(str_replace('_', ' ', $o->status ?? '-')) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($o->created_at ?? '-') ?></td>
                            <td>
                                <form method="POST" action="?action=update_order_status" style="display:flex;gap:6px;align-items:center;">
                                    <input type="hidden" name="id" value="<?= $o->id ?>">
                                    <select name="status" class="filter-sel" style="padding:4px 8px;font-size:0.8rem;">
                                        <?php foreach (['pending_payment','confirmed','in_delivery','delivered','cancelled'] as $s): ?>
                                            <option value="<?= $s ?>" <?= $o->status === $s ? 'selected' : '' ?>>
                                                <?= ucfirst(str_replace('_', ' ', $s)) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="btn-primary" style="padding:4px 12px;font-size:0.8rem;">Save</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>

        </section>


        <!-- ═══════════════════════════════════════════════════════
             STOCK
        ════════════════════════════════════════════════════════ -->
        <?php elseif ($active_page === 'stock'): ?>

        <?php
            $products      = getProducts();
            $lowStockAlert = getLowStockProducts(10);
        ?>

        <section class="section-container">

            <div class="section-header">
                <h1 class="admin-section-title">Stock Management</h1>
                <p class="admin-section-sub">Monitor and update product inventory levels</p>
                <div class="line"></div>
            </div>

            <!-- ── Low-Stock Alert Banner ───────────────────────────────── -->
            <?php if (!empty($lowStockAlert)): ?>
            <div class="alert alert-error" style="margin-bottom:20px;">
                ⚠️ <strong><?= count($lowStockAlert) ?> product(s)</strong> are low in stock (≤10 units):
                <?php foreach ($lowStockAlert as $lp): ?>
                    <span style="margin-left:8px;">
                        <strong><?= htmlspecialchars($lp->name) ?></strong>
                        <?php if (!empty($lp->color_name)): ?>
                            · <?= htmlspecialchars($lp->color_name) ?>
                        <?php endif; ?>
                        (<?= $lp->stock ?> left)
                    </span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- ── Stock Cards ─────────────────────────────────────────── -->
            <?php if (empty($products)): ?>
                <div class="empty-state"><p>No products found.</p></div>
            <?php else: ?>
            <div class="stock-grid">
                <?php foreach ($products as $p):
                    $stockStatus = $p->stock == 0 ? 'invalid' : ($p->stock <= 10 ? 'locked' : 'valid');
                    $stockLabel  = $p->stock == 0 ? 'Out of Stock' : ($p->stock <= 10 ? 'Low Stock' : 'In Stock');
                    $stockClass  = $p->stock == 0 ? 'stock-danger' : ($p->stock <= 10 ? 'stock-warn' : 'stock-ok');
                ?>
                <div class="stock-card">
                    <div class="stock-img-wrapper <?= $stockClass ?>">
                        <div class="stock-qty-big"><?= $p->stock ?></div>
                        <div class="stock-qty-label">units</div>
                    </div>
                    <div class="stock-info">
                        <h5 class="stock-name"><?= htmlspecialchars($p->name ?? '-') ?></h5>
<p class="stock-cat">
    <?= htmlspecialchars($p->category ?? '-') ?>
    <?php if (!empty($p->color_name)): ?>
        · <span style="color:#6b7280;"><?= htmlspecialchars($p->color_name) ?></span>
    <?php endif; ?>
</p>
                        <div class="stock-price">RM <?= number_format($p->price ?? 0, 2) ?></div>
                        <span class="badge badge-<?= $stockStatus ?> stock-badge"><?= $stockLabel ?></span>

                        <!-- ── Update Stock Form ────────────────────── -->
                        <form method="POST" action="?action=update_stock" style="display:flex;gap:6px;align-items:center;margin-top:10px;">
                            <input type="hidden" name="id" value="<?= $p->id ?>">
                            <input type="number" name="stock" value="<?= $p->stock ?>" min="0"
                                   style="width:70px;padding:4px 8px;border:1px solid #e5e7eb;border-radius:8px;font-size:0.85rem;">
                            <button type="submit" class="btn-primary" style="padding:4px 12px;font-size:0.8rem;">Update</button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

        </section>


        <!-- ═══════════════════════════════════════════════════════
             ADMIN PROFILE
        ════════════════════════════════════════════════════════ -->
        <?php elseif ($active_page === 'profile'): ?>

        <?php $admin = getAdmin((int)($_SESSION['admin_id'] ?? 1)); ?>

        <section class="section-container">
            <div class="section-header">
                <h1 class="admin-section-title">Admin Profile</h1>
                <div class="line"></div>
            </div>
            <div class="feature-card-wrap">
                <div class="feature-card">
                    <div class="member-avatar-wrap">
                        <div class="member-avatar"><?= strtoupper(substr($admin->name ?? 'AD', 0, 2)) ?></div>
                    </div>
                    <div class="member-name"><?= htmlspecialchars($admin->name ?? '-') ?></div>
                    <div class="member-role">Super Admin</div>
                    <div class="profile-rows">
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
                            <span class="profile-val"><span class="badge badge-valid">Active</span></span>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- ═══════════════════════════════════════════════════════
             DATA CHARTS
        ════════════════════════════════════════════════════════ -->
        <?php elseif ($active_page === 'charts'): ?>

        <section class="section-container">
            <?php include __DIR__ . '/charts.php'; ?>
        </section>

        <?php endif; ?>

    </div><!-- /content -->
</div><!-- /layout -->

<?php require $project_root . "components/footer.php"; ?>
<script src="/js/admin.js"></script>
</body>
</html>