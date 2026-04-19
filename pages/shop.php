<?php
$project_root = $_SERVER['DOCUMENT_ROOT']."/";
require $project_root."config.php";
require_once $project_root."database/product.php";

// Get category ID from URL, default to 1 if not set
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 1;

// Fetch products
$products = getProductsByCategory($category_id);

$_title = "Shop"; 
require $project_root."components/header.php";
?>

<link rel="stylesheet" href="../css/style.css">

<main class="shop-container">
    <header class="shop-header">
        <h1>Explore Our Collection</h1>
        <p>Filtered by: <strong><?= count($products) > 0 ? $products[0]->category_name : 'Category' ?></strong></p>
    </header>

    <div class="product-grid">
        <?php if (count($products) > 0): ?>
            <?php foreach ($products as $item): ?>
                <div class="product-card">
                    <a href="product.php?id=<?= $item->id ?>">
                        <div class="product-image">
                            <img src="<?= $item->photo ? '/uploads/' . $item->photo : '/assets/placeholder.png' ?>" alt="<?= htmlspecialchars($item->name) ?>">
                        </div>
                        <div class="product-info">
                            <h3><?= htmlspecialchars($item->name) ?></h3>
                            <p class="product-meta">NOAIR | <?= htmlspecialchars($item->category_name) ?></p>
                            <span class="price">RM<?= number_format($item->price, 2) ?></span>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No products found in this category.</p>
        <?php endif; ?>
    </div>
</main>

<?php require $project_root."components/footer.php"; ?>
