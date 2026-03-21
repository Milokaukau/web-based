<?php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require_once $project_root . "config.php";
require_once $project_root . "logic/auth_helper.php";

if (!isAdmin()) {
    header("Location: /pages/admin/login.php");
    exit;
}

// Handle actions (lock/unlock/delete/add) before any HTML output
require_once $project_root . "logic/admin_actions.php";

// Fetch all members from tb_member
$pdo = db();

$search   = trim($_GET['search'] ?? '');
$status   = $_GET['status'] ?? '';
$page_num = max(1, (int)($_GET['page'] ?? 1));
$per_page = 10;
$offset   = ($page_num - 1) * $per_page;

// Build query using ? positional params to avoid duplicate named param issue
$where       = [];
$params      = [];

if ($search !== '') {
    $where[]  = "(name LIKE ? OR email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($status === 'locked') {
    $where[] = "(locked_until IS NOT NULL AND locked_until > NOW())";
} elseif ($status === 'active') {
    $where[] = "(locked_until IS NULL OR locked_until <= NOW())";
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Count query
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM tb_member $whereSQL");
$countStmt->execute($params);
$total       = (int)$countStmt->fetchColumn();
$total_pages = max(1, ceil($total / $per_page));

// Paginated results
$pageParams   = array_merge($params, [$per_page, $offset]);
$stmt = $pdo->prepare("SELECT * FROM tb_member $whereSQL ORDER BY id ASC LIMIT ? OFFSET ?");
$stmt->execute($pageParams);
$members = $stmt->fetchAll(PDO::FETCH_OBJ);

// Stats — derived from locked_until since there's no status column
$stats = $pdo->query("SELECT
    COUNT(*) AS total,
    SUM(locked_until IS NULL OR locked_until <= NOW()) AS active_count,
    SUM(locked_until IS NOT NULL AND locked_until > NOW()) AS locked_count
FROM tb_member")->fetch(PDO::FETCH_OBJ);

$_title = 'NOAIR Admin';
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
        <a class="nav-link active" href="?page=members"><span class="nav-icon">&#128101;</span> Members</a>
        <a class="nav-link" href="?page=orders"><span class="nav-icon">&#128230;</span> Orders</a>
        <a class="nav-link" href="?page=stock"><span class="nav-icon">&#128230;</span> Stock</a>
        <div class="sidebar-section">Account</div>
        <a class="nav-link" href="?page=profile"><span class="nav-icon">&#9881;</span> Admin Profile</a>
    </div>

    <!-- CONTENT -->
    <div class="content">

        <?php if (!empty($_SESSION['flash'])): ?>
            <div class="alert alert-<?= $_SESSION['flash']['type'] ?>">
                <?= htmlspecialchars($_SESSION['flash']['message']) ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <?php
        $active_page = $_GET['page'] ?? 'members';
        ?>

        <!-- ====== MEMBERS PAGE ====== -->
        <?php if ($active_page === 'members'): ?>
        <div class="page active" id="page-members">
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
                    <?php
                        $highAttempts = $pdo->query("SELECT COUNT(*) FROM tb_member WHERE login_attempts >= 3")->fetchColumn();
                    ?>
                    <div class="stat-num"><?= $highAttempts ?></div>
                </div>
            </div>

            <!-- Toolbar -->
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
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Gender</th>
                            <th>Login Attempts</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($members as $m):
                            $isLocked = !empty($m->locked_until) && strtotime($m->locked_until) > time();
                            $statusLabel = $isLocked ? 'Locked' : 'Active';
                            $statusClass = $isLocked ? 'badge-locked' : 'badge-valid';
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($m->id) ?></td>
                            <td><?= htmlspecialchars($m->name ?? '-') ?></td>
                            <td><?= htmlspecialchars($m->email ?? '-') ?></td>
                            <td><?= htmlspecialchars($m->phone ?? '-') ?></td>
                            <td><?= htmlspecialchars($m->gender ?? '-') ?></td>
                            <td>
                                <span style="color:<?= ($m->login_attempts >= 3) ? 'var(--danger)' : 'inherit' ?>">
                                    <?= (int)($m->login_attempts ?? 0) ?>
                                </span>
                            </td>
                            <td><span class="badge <?= $statusClass ?>"><?= $statusLabel ?></span></td>
                            <td>
                                <a href="?page=member_detail&id=<?= $m->id ?>" class="act-btn">View</a>
                                <?php if ($isLocked): ?>
                                    <a href="?action=unlock&id=<?= $m->id ?>&page=members&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>"
                                       class="act-btn unlock"
                                       onclick="return confirm('Unlock this member?')">Unlock</a>
                                <?php else: ?>
                                    <a href="?action=lock&id=<?= $m->id ?>&page=members&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>"
                                       class="act-btn lock"
                                       onclick="return confirm('Lock this member?')">Lock</a>
                                <?php endif; ?>
                                <a href="?action=delete&id=<?= $m->id ?>&page=members"
                                   class="act-btn del"
                                   onclick="return confirm('Delete this member? This cannot be undone.')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=members&search=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>&page_num=<?= $i ?>"
                       class="pag-btn <?= $i === $page_num ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
                <span class="pag-info">Showing <?= count($members) ?> of <?= $total ?> members</span>
            </div>
            <?php endif; ?>
        </div>

        <!-- ====== MEMBER DETAIL PAGE ====== -->
        <?php elseif ($active_page === 'member_detail' && isset($_GET['id'])): ?>
        <?php
            $mid = (int)$_GET['id'];
            $member = $pdo->prepare("SELECT * FROM tb_member WHERE id = ?");
            $member->execute([$mid]);
            $member = $member->fetch(PDO::FETCH_OBJ);
        ?>
        <div class="page active">
            <div class="page-header">
                <h1>Member Detail</h1>
                <p><a href="?page=members" style="color:var(--accent);text-decoration:none">&#8592; Back to Members</a></p>
            </div>
            <?php if ($member):
                $isLocked = !empty($member->locked_until) && strtotime($member->locked_until) > time();
            ?>
            <div class="profile-wrap">
                <div class="profile-card">
                    <div class="profile-avt"><?= strtoupper(substr($member->name ?? 'M', 0, 2)) ?></div>
                    <div class="profile-row"><span class="profile-lbl">ID</span><span class="profile-val"><?= $member->id ?></span></div>
                    <div class="profile-row"><span class="profile-lbl">Name</span><span class="profile-val"><?= htmlspecialchars($member->name ?? '-') ?></span></div>
                    <div class="profile-row"><span class="profile-lbl">Email</span><span class="profile-val"><?= htmlspecialchars($member->email ?? '-') ?></span></div>
                    <div class="profile-row"><span class="profile-lbl">Phone</span><span class="profile-val"><?= htmlspecialchars($member->phone ?? '-') ?></span></div>
                    <div class="profile-row"><span class="profile-lbl">Gender</span><span class="profile-val"><?= htmlspecialchars($member->gender ?? '-') ?></span></div>
                    <div class="profile-row"><span class="profile-lbl">Status</span>
                        <span class="profile-val">
                            <span class="badge <?= $isLocked ? 'badge-locked' : 'badge-valid' ?>">
                                <?= $isLocked ? 'Locked' : 'Active' ?>
                            </span>
                        </span>
                    </div>
                    <div class="profile-row"><span class="profile-lbl">Login Attempts</span><span class="profile-val"><?= $member->login_attempts ?? 0 ?></span></div>
                    <div class="profile-row"><span class="profile-lbl">Locked Until</span><span class="profile-val"><?= $member->locked_until ?? 'N/A' ?></span></div>
                </div>
                <div style="display:flex;gap:8px">
                    <?php if ($isLocked): ?>
                        <a href="?action=unlock&id=<?= $member->id ?>&page=members" class="btn-outline" onclick="return confirm('Unlock this member?')">Unlock Account</a>
                    <?php else: ?>
                        <a href="?action=lock&id=<?= $member->id ?>&page=members" class="btn-outline" onclick="return confirm('Lock this member?')">Lock Account</a>
                    <?php endif; ?>
                    <a href="?action=delete&id=<?= $member->id ?>&page=members" class="btn-primary" onclick="return confirm('Delete? Cannot undo.')" style="background:var(--danger)">Delete</a>
                </div>
            </div>
            <?php else: ?>
                <div class="empty-state">Member not found.</div>
            <?php endif; ?>
        </div>

        <!-- ====== ORDERS PAGE ====== -->
        <?php elseif ($active_page === 'orders'): ?>
        <?php
            $orders = $pdo->query("SELECT o.*, m.username FROM tb_order o LEFT JOIN tb_member m ON o.member_id = m.id ORDER BY o.id DESC LIMIT 50")->fetchAll(PDO::FETCH_OBJ);
        ?>
        <div class="page active">
            <div class="page-header"><h1>Order Listing</h1><p>All customer orders</p></div>
            <div class="table-wrap">
                <?php if (empty($orders)): ?>
                    <div class="empty-state">No orders found.</div>
                <?php else: ?>
                <table>
                    <thead><tr><th>Order ID</th><th>Member</th><th>Total (RM)</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                        <?php foreach ($orders as $o): ?>
                        <tr>
                            <td>#<?= $o->id ?></td>
                            <td><?= htmlspecialchars($o->username ?? 'Guest') ?></td>
                            <td><?= number_format($o->total_amount ?? 0, 2) ?></td>
                            <td><span class="badge badge-<?= $o->status === 'completed' ? 'valid' : ($o->status === 'cancelled' ? 'invalid' : 'locked') ?>"><?= ucfirst($o->status ?? '-') ?></span></td>
                            <td><?= htmlspecialchars($o->created_at ?? '-') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- ====== STOCK PAGE ====== -->
        <?php elseif ($active_page === 'stock'): ?>
        <?php
            $products = $pdo->query("SELECT * FROM tb_product ORDER BY stock ASC")->fetchAll(PDO::FETCH_OBJ);
        ?>
        <div class="page active">
            <div class="page-header"><h1>Stock Management</h1><p>Monitor product inventory levels</p></div>
            <div class="table-wrap">
                <?php if (empty($products)): ?>
                    <div class="empty-state">No products found.</div>
                <?php else: ?>
                <table>
                    <thead><tr><th>Product</th><th>Category</th><th>Price (RM)</th><th>Stock</th><th>Status</th></tr></thead>
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

        <!-- ====== PROFILE PAGE ====== -->
        <?php elseif ($active_page === 'profile'): ?>
        <?php
            $admin = $pdo->prepare("SELECT * FROM tb_admin WHERE id = ?");
            $admin->execute([$_SESSION['admin_id'] ?? 1]);
            $admin = $admin->fetch(PDO::FETCH_OBJ);
        ?>
        <div class="page active">
            <div class="page-header"><h1>Admin Profile</h1><p>Your administrator account details</p></div>
            <div class="profile-wrap">
                <div class="profile-card">
                    <div class="profile-avt"><?= strtoupper(substr($admin->name ?? "AD", 0, 2)) ?></div>
                    <div class="profile-row"><span class="profile-lbl">Name</span><span class="profile-val"><?= htmlspecialchars($admin->name ?? '-') ?></span></div>
                    <div class="profile-row"><span class="profile-lbl">Email</span><span class="profile-val"><?= htmlspecialchars($admin->email ?? '-') ?></span></div>
                    <div class="profile-row"><span class="profile-lbl">Role</span><span class="profile-val">Super Admin</span></div>
                    <div class="profile-row"><span class="profile-lbl">Status</span><span class="profile-val"><span class="badge badge-valid">Active</span></span></div>
                </div>
                <button class="btn-primary" onclick="openModal('modal-pw')">Change Password</button>
            </div>
        </div>
        <?php endif; ?>

    </div><!-- /content -->
</div><!-- /layout -->

<div class="footer">NOAIR Admin Panel &mdash; &copy; <?= date('Y') ?> NOAIR. All rights reserved.</div>

<!-- CHANGE PASSWORD MODAL -->
<div class="modal-overlay" id="modal-pw">
    <div class="modal-box">
        <div class="modal-title">Change Password</div>
        <form method="POST" action="?action=change_pw&page=profile">
            <div class="form-row"><label>New Password</label><input type="password" name="new_password" required></div>
            <div class="form-row"><label>Confirm Password</label><input type="password" name="confirm_password" required></div>
            <div class="modal-actions">
                <button type="button" class="btn-outline" onclick="closeModal('modal-pw')">Cancel</button>
                <button type="submit" class="btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.modal-overlay').forEach(o => {
    o.addEventListener('click', e => { if(e.target === o) o.classList.remove('open'); });
});
function updateClock() {
    const n = new Date();
    document.getElementById('clock').textContent =
        n.toLocaleDateString('en-MY',{day:'2-digit',month:'short',year:'numeric'}) + '  ' +
        n.toLocaleTimeString('en-MY',{hour:'2-digit',minute:'2-digit'});
}
updateClock(); setInterval(updateClock, 1000);

// Highlight active sidebar link
const p = new URLSearchParams(location.search).get('page') || 'members';
document.querySelectorAll('.nav-link').forEach(l => {
    l.classList.remove('active');
    if(l.href.includes('page='+p)) l.classList.add('active');
});
</script>
</body>
</html>