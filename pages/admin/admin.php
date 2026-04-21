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
    <?php include $project_root . "components/admin_sidebar.php"; ?>

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
    <?php if (!empty($member->photo)): ?>
        <img src="/images/members/<?= htmlspecialchars($member->photo) ?>"
             alt="<?= htmlspecialchars($member->name) ?>"
             style="width:80px;height:80px;border-radius:50%;object-fit:cover;">
    <?php else: ?>
        <div class="member-avatar"><?= strtoupper(substr($member->name, 0, 2)) ?></div>
    <?php endif; ?>
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
             STOCK
        ════════════════════════════════════════════════════════ -->
<?php elseif ($active_page === 'stock'): ?>

    <?php
        $products         = getProducts();
        $lowStockAlert    = getLowStockProducts(10);
        $categories = array_unique(array_filter(array_column($products, 'category'), fn($c) => $c !== null));
        sort($categories);
        $total_products   = count($products);
        $in_stock_count   = count(array_filter($products, fn($p) => $p->is_active && $p->stock > 10));
        $low_count        = count(array_filter($products, fn($p) => $p->is_active && $p->stock > 0 && $p->stock <= 10));
        $oos_count = count(array_filter($products, fn($p) => $p->is_active && $p->stock == 0));
        $inactive_count   = count(array_filter($products, fn($p) => !$p->is_active));
    ?>

    <section class="section-container">

        <div class="section-header">
            <h1 class="admin-section-title">Stock management</h1>
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

        <!-- Stats cards -->
        <div class="stats-grid" style="margin-bottom:20px;">
            <div class="stat-card">
                <div class="stat-label">Total products</div>
                <div class="stat-num"><?= $total_products ?></div>
            </div>
            <div class="stat-card s-valid">
                <div class="stat-label">In stock</div>
                <div class="stat-num"><?= $in_stock_count ?></div>
            </div>
            <div class="stat-card s-locked">
                <div class="stat-label">Low stock</div>
                <div class="stat-num"><?= $low_count ?></div>
            </div>
            <div class="stat-card s-invalid">
                <div class="stat-label">Out of stock</div>
                <div class="stat-num"><?= $oos_count ?></div>
            </div>
        </div>

        <!-- Page header actions -->
        <div class="page-header" style="margin-bottom:12px;">
            <div>
                <p class="sub" id="stock-record-count"><?= $total_products ?> record(s) found</p>
            </div>
            <div class="page-header-actions">
                <button class="btn-oos" id="stock-toggle-oos" type="button">
                    Out of Stock (<?= $oos_count ?>)
                </button>
                <button class="btn-oos" id="stock-toggle-inactive" type="button">
                    Deactivated (<?= $inactive_count ?>)
                </button>
                <button class="btn-view-toggle" id="stock-view-toggle" type="button">⊞ Card View</button>
                <a class="btn-primary" href="../admin/product_insert.php" style="margin-left:auto;">+ Add Product</a>
            </div>
        </div>

        <!-- Search -->
        <div class="search-wrap" style="margin-bottom:12px;">
            <input type="text" id="stock-search" placeholder="Search by ID, name, category, material…" autocomplete="off">
        </div>

        <!-- Filters -->
        <div class="filter-wrap" style="margin-bottom:16px;">
            <select id="stock-cat-filter">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                <?php endforeach; ?>
            </select>

            <select id="stock-status-filter">
                <option value="">All Status</option>
                <option value="ok">In stock (&gt;10)</option>
                <option value="warn">Low stock (1–10)</option>
                <option value="danger">Out of stock (0)</option>
            </select>

            <div class="price-range">
                <input type="number" id="stock-price-min" placeholder="Min price (RM)" min="0" step="0.01">
                <span>–</span>
                <input type="number" id="stock-price-max" placeholder="Max price (RM)" min="0" step="0.01">
            </div>

            <button id="stock-filter-reset" type="button">Reset</button>
        </div>

        <!-- ── TABLE VIEW ─────────────────────────────────────────── -->
        <div id="stock-table-view">
            <div class="tbl-wrap" style="overflow-x:auto;">
                <table id="stock-table">
                    <thead>
                    <tr>
                        <th>Photo</th>
                        <th class="sortable" data-col="1" data-type="num">ID <span class="sort-icon"></span></th>
                        <th class="sortable" data-col="2">Name <span class="sort-icon"></span></th>
                        <th class="sortable" data-col="3">Category <span class="sort-icon"></span></th>
                        <th class="sortable" data-col="4">Color <span class="sort-icon"></span></th>
                        <th class="sortable" data-col="5">Description <span class="sort-icon"></span></th>
                        <th class="sortable" data-col="6">Material <span class="sort-icon"></span></th>
                        <th class="sortable" data-col="7" data-type="num">Weight (g) <span class="sort-icon"></span></th>
                        <th class="sortable" data-col="8" data-type="num">Height (cm) <span class="sort-icon"></span></th>
                        <th class="sortable" data-col="9" data-type="num">Base Diameter (cm) <span class="sort-icon"></span></th>
                        <th class="sortable" data-col="10" data-type="num">Price (RM) <span class="sort-icon"></span></th>
                        <th class="sortable" data-col="11" data-type="num">Stock <span class="sort-icon"></span></th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody id="stock-active-tbody">
                        <?php foreach ($products as $p):
                            $stockKey = $p->stock == 0 ? 'danger' : ($p->stock <= 10 ? 'warn' : 'ok');
                            $stockLabel  = $p->stock == 0 ? 'Out of stock' : ($p->stock <= 10 ? 'Low stock' : 'In stock');
                            $badgeCls    = $p->stock == 0 ? 'badge-invalid' : ($p->stock <= 10 ? 'badge-locked' : 'badge-valid');
                            $isInactive  = !$p->is_active;
                        ?>
                        <tr class="<?= $isInactive ? 'row-disabled' : '' ?>"
                            data-group="<?= $isInactive ? 'inactive' : 'active' ?>"
                            data-id="<?= $p->id ?>"
                            data-name="<?= htmlspecialchars(strtolower($p->name ?? '')) ?>"
                            data-cat="<?= htmlspecialchars(strtolower($p->category ?? '')) ?>"
                            data-material="<?= htmlspecialchars(strtolower($p->material ?? '')) ?>"
                            data-status="<?= $stockKey ?>"
                            data-price="<?= $p->price ?? 0 ?>">
                            <td>
                                <?php if (!empty($p->photo)): ?>
                                    <img class="product-thumb"
                                        src="/images/<?= htmlspecialchars($p->photo) ?>"
                                        alt="<?= htmlspecialchars($p->name) ?>">
                                <?php else: ?>
                                    <span class="no-photo">No Photo</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $p->id ?></td>
                            <td><?= htmlspecialchars($p->name ?? '-') ?></td>
                            <td><?= htmlspecialchars($p->category_id ?? '-') ?></td>
                            <td><?= htmlspecialchars($p->color_name ?? '-') ?></td>
                            <td class="desc-cell" title="<?= htmlspecialchars($p->description ?? '') ?>">
                                <?= htmlspecialchars($p->description ?? '-') ?>
                            </td>
                            <td><?= htmlspecialchars($p->material ?? '-') ?></td>
                            <td><?= htmlspecialchars($p->weight_g ?? '-') ?></td>
                            <td><?= htmlspecialchars($p->height_cm ?? '-') ?></td>
                            <td><?= htmlspecialchars($p->base_diameter_cm ?? '-') ?></td>
                            <td><?= number_format($p->price ?? 0, 2) ?></td>
                            <td style="font-weight:500;"><?= $p->stock ?></td>
                            <td><span class="badge <?= $badgeCls ?>"><?= $stockLabel ?></span></td>
                            <td>
                                <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;">
                                    <?php if ($isInactive): ?>
                                        <!-- Inactive: Edit + Restore only -->
                                        <a class="act-btn" href="product_update.php?id=<?= $p->id ?>">Edit</a>
                                        <form method="GET" action="../../database/product_restore.php">
                                            <input type="hidden" name="id" value="<?= $p->id ?>">
                                            <button type="submit" class="act-btn unlock"
                                                    onclick="return confirm('Restore this product?')">Restore</button>
                                        </form>
                                    <?php else: ?>
                                        <!-- Active (including OOS): Update stock + Edit + Delete -->
                                        <form method="POST" action="?action=update_stock"
                                            style="display:flex;gap:6px;align-items:center;">
                                            <input type="hidden" name="id" value="<?= $p->id ?>">
                                            <input type="number" name="stock" value="<?= $p->stock ?>" min="0"
                                                style="width:70px;padding:4px 8px;
                                                        border:1px solid #e5e7eb;border-radius:8px;font-size:0.85rem;">
                                            <button type="submit" class="btn-primary"
                                                    style="padding:4px 12px;font-size:0.8rem;">Update</button>
                                        </form>
                                        <a class="act-btn" href="product_update.php?id=<?= $p->id ?>">Edit</a>
                                        <a class="act-btn del" href="../../database/product_delete.php?id=<?= $p->id ?>"
                                           onclick="return confirm('Sure to delete?')">Delete</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrap" id="stock-table-pagination-wrap">
                <span id="stock-table-pagination-info"></span>
                <div id="stock-table-pagination"></div>
            </div>
        </div>

        <!-- ── CARD VIEW ──────────────────────────────────────────── -->
        <div id="stock-card-view" hidden>
            <div class="stock-grid" id="stock-card-grid">
                <?php foreach ($products as $p):
                    $stockStatus = $p->stock == 0 ? 'invalid' : ($p->stock <= 10 ? 'locked' : 'valid');
                    $stockLabel  = $p->stock == 0 ? 'Out of stock' : ($p->stock <= 10 ? 'Low stock' : 'In stock');
                    $stockClass  = $p->stock == 0 ? 'stock-danger' : ($p->stock <= 10 ? 'stock-warn' : 'stock-ok');
                    $stockKey    = $p->stock == 0 ? 'danger' : ($p->stock <= 10 ? 'warn' : 'ok');
                    $isInactive  = !$p->is_active;
                ?>
                <div class="stock-card <?= $isInactive ? 'card-disabled' : '' ?>"
                    data-group="<?= $isInactive ? 'inactive' : 'active' ?>"
                    data-id="<?= $p->id ?>"
                    data-name="<?= htmlspecialchars(strtolower($p->name ?? '')) ?>"
                    data-cat="<?= htmlspecialchars(strtolower($p->category ?? '')) ?>"
                    data-material="<?= htmlspecialchars(strtolower($p->material ?? '')) ?>"
                    data-status="<?= $stockKey ?>"
                    data-price="<?= $p->price ?? 0 ?>">

                    <div class="stock-img-wrapper <?= $stockClass ?>">
                        <?php if (!empty($p->photo)): ?>
                            <img src="/images/<?= htmlspecialchars($p->photo) ?>"
                                alt="<?= htmlspecialchars($p->name) ?>"
                                style="width:100%;height:100%;object-fit:cover;">
                        <?php else: ?>
                            <div class="stock-qty-big"><?= $p->stock ?></div>
                            <div class="stock-qty-label">units</div>
                        <?php endif; ?>
                    </div>

                    <div class="stock-info">
                        <h5 class="stock-name">
                            <span style="color:var(--text-muted);font-weight:500;font-size:0.78rem;">#<?= $p->id ?></span>
                            <?= htmlspecialchars($p->name ?? '-') ?>
                        </h5>
                        <p class="stock-cat">
                            <?= htmlspecialchars($p->category ?? '-') ?>
                            <?php if (!empty($p->color_name)): ?>
                                · <span style="color:#6b7280;"><?= htmlspecialchars($p->color_name) ?></span>
                            <?php endif; ?>
                        </p>
                        <div class="stock-price">RM <?= number_format($p->price ?? 0, 2) ?></div>
                        <div style="font-size:0.82rem;font-weight:600;color:var(--text-dark);margin-bottom:6px;">
                            Stock: <?= $p->stock ?> units
                        </div>
                        <span class="badge badge-<?= $stockStatus ?> stock-badge"><?= $stockLabel ?></span>
                    </div>

                    <div class="stock-hover-overlay">
                        <div class="shov-name"><?= htmlspecialchars($p->name ?? '-') ?></div>

                        <div class="shov-meta">
                            <div class="shov-row"><span>Category</span><span><?= htmlspecialchars($p->category ?? '-') ?></span></div>
                            <?php if (!empty($p->color_name)): ?>
                            <div class="shov-row"><span>Color</span><span><?= htmlspecialchars($p->color_name) ?></span></div>
                            <?php endif; ?>
                            <div class="shov-row"><span>Material</span><span><?= htmlspecialchars($p->material ?? '-') ?></span></div>
                            <?php if (!empty($p->weight_g)): ?>
                            <div class="shov-row"><span>Weight</span><span><?= htmlspecialchars($p->weight_g) ?> g</span></div>
                            <?php endif; ?>
                            <?php if (!empty($p->height_cm)): ?>
                            <div class="shov-row"><span>Height</span><span><?= htmlspecialchars($p->height_cm) ?> cm</span></div>
                            <?php endif; ?>
                            <?php if (!empty($p->base_diameter_cm)): ?>
                            <div class="shov-row"><span>Base Diameter</span><span><?= htmlspecialchars($p->base_diameter_cm) ?> cm</span></div>
                            <?php endif; ?>
                            <div class="shov-row"><span>Price</span><span>RM <?= number_format($p->price ?? 0, 2) ?></span></div>
                            <div class="shov-row"><span>Stock</span><span><?= $p->stock ?> units</span></div>
                        </div>

                        <?php if (!$isInactive): ?>
                        <form method="POST" action="?action=update_stock" class="shov-form">
                            <input type="hidden" name="id" value="<?= $p->id ?>">
                            <input type="number" name="stock" value="<?= $p->stock ?>" min="0">
                            <button type="submit" class="btn-primary" style="padding:5px 12px;font-size:0.8rem;">Update</button>
                        </form>
                        <?php endif; ?>

                        <div style="display:flex;gap:6px;margin-top:6px;">
                            <?php if ($isInactive): ?>
                                <a class="act-btn" style="flex:1;text-align:center;"
                                   href="product_update.php?id=<?= $p->id ?>">Edit</a>
                                <form method="GET" action="../../database/product_restore.php" style="flex:1;">
                                    <input type="hidden" name="id" value="<?= $p->id ?>">
                                    <button type="submit" class="act-btn unlock" style="width:100%;"
                                            onclick="return confirm('Restore this product?')">Restore</button>
                                </form>
                            <?php else: ?>
                                <a class="act-btn" style="flex:1;text-align:center;"
                                   href="product_update.php?id=<?= $p->id ?>">Edit</a>
                                <a class="act-btn del" style="flex:1;text-align:center;"
                                   href="../../database/product_delete.php?id=<?= $p->id ?>"
                                   onclick="return confirm('Sure to delete?')">Delete</a>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
                <?php endforeach; ?>
            </div>

            <div class="pagination-wrap" id="stock-card-pagination-wrap">
                <span id="stock-card-pagination-info"></span>
                <div id="stock-card-pagination"></div>
            </div>
        </div>

    </section>

    <script>
        
    (function () {
        const ROWS_PER_PAGE  = 10;
        const CARDS_PER_PAGE = 12;

        let currentView  = localStorage.getItem('stockView') || 'table';
        let showOos = false;
        let showInactive = false;
        let sortCol      = -1;
        let sortAsc      = true;
        let tablePage    = 1;
        let cardPage     = 1;

        const tableView   = document.getElementById('stock-table-view');
        const cardView    = document.getElementById('stock-card-view');
        const cardGrid    = document.getElementById('stock-card-grid');
        const tbody       = document.getElementById('stock-active-tbody');
        const allRows     = Array.from(tbody.querySelectorAll('tr'));
        const allCards    = Array.from(cardGrid.querySelectorAll('.stock-card'));
        const recordCount = document.getElementById('stock-record-count');


        if (new URLSearchParams(window.location.search).get('show') === 'inactive') {
        showInactive = true;
        document.getElementById('stock-toggle-inactive').classList.add('active');}

        document.querySelectorAll('[data-status]').forEach(el => {
            console.log(el.dataset.id, el.dataset.group, el.dataset.status);
        });

        function setView(v) {
            currentView = v;
            tableView.hidden = v !== 'table';
            cardView.hidden  = v !== 'card';
            document.getElementById('stock-view-toggle').textContent =
                v === 'table' ? '⊞ Card View' : '☰ Table View';
            localStorage.setItem('stockView', v);
            applyAll();
        }

        document.getElementById('stock-view-toggle').addEventListener('click', () =>
            setView(currentView === 'table' ? 'card' : 'table'));

            document.getElementById('stock-toggle-oos').addEventListener('click', function () {
                showOos = !showOos;
                if (showOos) {
                    showInactive = false;
                    document.getElementById('stock-toggle-inactive').classList.remove('active');
                }
                this.classList.toggle('active', showOos);
                tablePage = cardPage = 1;
                applyAll();
            });

            document.getElementById('stock-toggle-inactive').addEventListener('click', function () {
                showInactive = !showInactive;
                if (showInactive) {
                    showOos = false;
                    document.getElementById('stock-toggle-oos').classList.remove('active');
                }
                this.classList.toggle('active', showInactive);
                tablePage = cardPage = 1;
                applyAll();
            });

        const searchEl   = document.getElementById('stock-search');
        const catFilter  = document.getElementById('stock-cat-filter');
        const statFilter = document.getElementById('stock-status-filter');
        const priceMin   = document.getElementById('stock-price-min');
        const priceMax   = document.getElementById('stock-price-max');

        [searchEl, catFilter, statFilter, priceMin, priceMax].forEach(el =>
            el.addEventListener('input', () => { tablePage = cardPage = 1; applyAll(); }));

        document.getElementById('stock-filter-reset').addEventListener('click', () => {
            searchEl.value = catFilter.value = statFilter.value = priceMin.value = priceMax.value = '';
            tablePage = cardPage = 1;
            applyAll();
        });

        function matchesFilters(el) {
            const group = el.dataset.group; 
            const stock = el.dataset.status; 

            if (showInactive && group !== 'inactive') return false;
            if (!showInactive && group === 'inactive') return false;
            if (showOos && stock !== 'danger') return false;

            const q     = searchEl.value.toLowerCase().trim();
            const cat   = catFilter.value.toLowerCase();
            const stat  = statFilter.value;
            const min   = parseFloat(priceMin.value);
            const max   = parseFloat(priceMax.value);
            const price = parseFloat(el.dataset.price);

            if (q && !el.dataset.name.includes(q) &&
                    !el.dataset.cat.includes(q) &&
                    !(el.dataset.material || '').includes(q) &&
                    !(el.dataset.id || '').includes(q)) return false;
            if (cat  && el.dataset.cat !== cat)  return false;
            if (stat && stock !== stat)           return false;
            if (!isNaN(min) && price < min)       return false;
            if (!isNaN(max) && price > max)       return false;
            return true;
        }

        document.querySelectorAll('#stock-table thead th.sortable').forEach(th => {
            th.style.cursor = 'pointer';
            th.addEventListener('click', () => {
                const col = parseInt(th.dataset.col);
                if (sortCol === col) { sortAsc = !sortAsc; } else { sortCol = col; sortAsc = true; }
                document.querySelectorAll('#stock-table thead .sort-icon')
                    .forEach(s => s.textContent = '');
                th.querySelector('.sort-icon').textContent = sortAsc ? ' ↑' : ' ↓';
                tablePage = 1;
                applyAll();
            });
        });


        function getSortValue(row, col) {
            const cells = row.querySelectorAll('td');
            const cell  = cells[col];
            if (!cell) return '';
            const isNum = document.querySelector(`#stock-table thead th[data-col="${col}"]`)
                                ?.dataset.type === 'num';
            return isNum ? parseFloat(cell.textContent) || 0 : cell.textContent.trim().toLowerCase();
        }

        function buildPagination(containerId, infoId, total, perPage, currentPage, onPage) {
            const totalPages = Math.max(1, Math.ceil(total / perPage));
            const safeP      = Math.min(currentPage, totalPages);
            const info       = document.getElementById(infoId);
            const pag        = document.getElementById(containerId);

            const from = total === 0 ? 0 : (safeP - 1) * perPage + 1;
            const to   = Math.min(safeP * perPage, total);
            info.textContent = total === 0
                ? 'No records found'
                : `Showing ${from}–${to} of ${total} record(s)`;

            pag.innerHTML = '';
            if (totalPages <= 1) return safeP;

            const mkBtn = (label, page, disabled, active) => {
                const b = document.createElement('button');
                b.textContent = label;
                b.className   = 'page-btn' + (active ? ' active' : '');
                b.disabled    = disabled;
                b.type        = 'button';
                b.addEventListener('click', () => onPage(page));
                pag.appendChild(b);
            };

            mkBtn('«', 1,         safeP === 1,          false);
            mkBtn('‹', safeP - 1, safeP === 1,          false);

            let start = Math.max(1, safeP - 2);
            let end   = Math.min(totalPages, safeP + 2);
            if (safeP <= 3)              end   = Math.min(5, totalPages);
            if (safeP >= totalPages - 2) start = Math.max(1, totalPages - 4);

            for (let i = start; i <= end; i++) mkBtn(i, i, false, i === safeP);

            mkBtn('›', safeP + 1, safeP === totalPages, false);
            mkBtn('»', totalPages, safeP === totalPages, false);

            return safeP;
        }

        function applyAll() {
            let visibleRows = allRows.filter(matchesFilters);

            if (sortCol !== -1) {
                visibleRows.sort((a, b) => {
                    const av  = getSortValue(a, sortCol);
                    const bv  = getSortValue(b, sortCol);
                    const cmp = typeof av === 'number' ? av - bv : av.localeCompare(bv);
                    return sortAsc ? cmp : -cmp;
                });
            }

            tablePage = buildPagination(
                'stock-table-pagination', 'stock-table-pagination-info',
                visibleRows.length, ROWS_PER_PAGE, tablePage,
                (p) => { tablePage = p; applyAll(); }
            );

            const tStart = (tablePage - 1) * ROWS_PER_PAGE;
            const tEnd   = tStart + ROWS_PER_PAGE;

            allRows.forEach(r => { r.hidden = true; tbody.appendChild(r); });
            visibleRows.forEach((r, i) => { r.hidden = !(i >= tStart && i < tEnd); });

            const visibleCards = allCards.filter(matchesFilters);

            cardPage = buildPagination(
                'stock-card-pagination', 'stock-card-pagination-info',
                visibleCards.length, CARDS_PER_PAGE, cardPage,
                (p) => { cardPage = p; applyAll(); }
            );

            const cStart = (cardPage - 1) * CARDS_PER_PAGE;
            const cEnd   = cStart + CARDS_PER_PAGE;

            allCards.forEach(c => { c.hidden = true; });
            visibleCards.forEach((c, i) => { c.hidden = !(i >= cStart && i < cEnd); });

            const total = currentView === 'table' ? visibleRows.length : visibleCards.length;
            recordCount.textContent = total + ' record(s) found';
        }

        setView(currentView);
    })();
    </script>

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
                            <span class="profile-val"><?= htmlspecialchars($admin->is_superadmin ? 'Super Admin' : 'Regular Admin') ?></span>
                        </div>
                        <div class="profile-row">
                            <span class="profile-lbl">Status</span>
                            <span class="profile-val"><span class="badge badge-valid">Active</span></span>
                        </div>
                    </div>

                    <div style="display:flex; justify-content:center; margin-top:24px;">
                        <a href="/pages/admin/change_password.php" class="btn-primary" style="padding: 10px 24px; text-decoration: none;">
                            Change Password
                        </a>
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

<?php require $project_root . "components/admin_footer.php"; ?>
<script src="/js/admin.js"></script>
</body>
</html>