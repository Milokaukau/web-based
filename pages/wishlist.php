<?php
$project_root = $_SERVER['DOCUMENT_ROOT']."/";
require $project_root."config.php";

if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

if (isset($_GET['action'])) {
    $id = $_GET['id'] ?? null;
    if ($id && is_numeric($id)) {
        if ($_GET['action'] == 'add') {
            if (!in_array($id, $_SESSION['wishlist'])) {
                $_SESSION['wishlist'][] = $id;
            }
            header("Location: wishlist.php");
            exit;
        } elseif ($_GET['action'] == 'remove') {
            if (($key = array_search($id, $_SESSION['wishlist'])) !== false) {
                unset($_SESSION['wishlist'][$key]);
            }
            header("Location: wishlist.php");
            exit;
        }
    }
}

$_title = "Wishlist";
require $project_root."components/header.php";
require_once $project_root . "database/product.php";

$wishlistProducts = [];
if (!empty($_SESSION['wishlist'])) {
    foreach ($_SESSION['wishlist'] as $wid) {
        $product = getProductById($wid);
        if ($product) {
            $wishlistProducts[] = $product;
        }
    }
}
?>

<link rel="stylesheet" href="../css/style.css">

<main class="wishlist-container">
    <div class="wishlist-header">
        <h1 class="main-title">MY WISHLIST <span class="w-count">(<?= count($wishlistProducts) ?> items)</span></h1>
    </div>

    <?php if (empty($wishlistProducts)): ?>
        <div class="w-empty-state">
            <p>You haven't saved any items to your wishlist yet. Start shopping and add your favorite items to your wishlist.</p>
        </div>
    <?php else: ?>
        <div class="wishlist-grid">
            <?php foreach($wishlistProducts as $p): ?>
                <div class="w-card">
                    <a href="product.php?id=<?= $p->id ?>" class="w-card-img-box">
                        <img src="<?= $p->photo ? '../' . $p->photo : '../assets/placeholder.png' ?>" alt="<?= htmlspecialchars($p->name) ?>">
                    </a>
                    
                    <div class="w-card-content">
                        <div class="w-name"><?= htmlspecialchars($p->name) ?></div>
                        <div class="w-price">RM<?= number_format($p->price, 2) ?></div>
                        
                        <div class="w-actions">
                            <a href="product.php?id=<?= $p->id ?>" class="w-btn-view">VIEW ITEM</a>
                            <a href="wishlist.php?action=remove&id=<?= $p->id ?>" class="w-btn-remove">REMOVE</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php require $project_root."components/footer.php"; ?>
