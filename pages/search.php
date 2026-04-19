<?php
$project_root = $_SERVER['DOCUMENT_ROOT'] . "/";
require $project_root . "config.php";
require $project_root . "logic/search.php";

$_title = "Search";
require $project_root . "components/header.php";
?>

<link rel="stylesheet" href="../css/search.css">

<div class="search-results-wrapper">

    <?php if ($search_term === ''): ?>
        <div class="search-empty">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <p>Use the search bar above to find a product.</p>
            <a href="shop.php" class="primary-btn-link">Browse All Products</a>
        </div>

    <?php elseif ($total_found === 0): ?>
        <div class="search-empty">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                <line x1="8" y1="11" x2="14" y2="11"/>
            </svg>
            <p>No products found for "<strong><?= htmlspecialchars($search_term) ?></strong>".</p>
            <a href="home.php" class="primary-btn-link">Back to Home</a>
        </div>

    <?php else: ?>
        <div class="search-results-header">
            <h1 class="search-results-title">
                Results for "<span><?= htmlspecialchars($search_term) ?></span>"
            </h1>
            <p class="result-meta"><?= $total_found ?> product<?= $total_found !== 1 ? 's' : '' ?> found</p>
        </div>

        <div class="search-product-grid">
            <?php foreach ($search_results as $item): ?>
                <a href="product.php?id=<?= $item->id ?>" class="search-product-card">

                    <?php if (!empty($item->photo)): ?>
                        <img src="/images/<?= htmlspecialchars($item->photo) ?>"
                             alt="<?= htmlspecialchars($item->name) ?>"
                             class="card-img">
                    <?php else: ?>
                        <div class="card-img-placeholder">🧴</div>
                    <?php endif; ?>

                    <div class="card-body">
                        <div class="card-name">
                            <?= htmlspecialchars($item->name) ?>
                            <?php if ($item->stock <= 0): ?>
                                <span class="tag-sold-out">SOLD OUT</span>
                            <?php endif; ?>
                        </div>
                        <div class="card-cat">NOAIR Series</div>
                        <div class="card-price">RM <?= number_format($item->price, 2) ?></div>
                    </div>

                </a>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>

</div>

<?php require $project_root . "components/footer.php"; ?>
