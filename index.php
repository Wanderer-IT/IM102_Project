<?php
require_once 'config.php';
require_once 'auth.php';
requireLogin();

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

$sql = "SELECT p.*, c.name AS category_name, s.name AS supplier_name, u.username AS added_by_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN suppliers s ON p.supplier_id = s.id
        LEFT JOIN users u ON p.added_by = u.id
        WHERE 1=1";

if (!empty($search)) {
    $safe_search = $conn->real_escape_string($search);
    $sql .= " AND (p.name LIKE '%$safe_search%' OR p.description LIKE '%$safe_search%')";
}

if (!empty($category)) {
    $safe_category = $conn->real_escape_string($category);
    $sql .= " AND c.name = '$safe_category'";
}

$sql .= " ORDER BY p.id ASC";

$products = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);

// Group products into shelves by category, "Uncategorized" pushed to the end.
$shelves = [];
foreach ($products as $row) {
    $shelfName = $row['category_name'] ?? 'Uncategorized';
    $shelves[$shelfName][] = $row;
}
uksort($shelves, function ($a, $b) {
    if ($a === 'Uncategorized') return 1;
    if ($b === 'Uncategorized') return -1;
    return strcasecmp($a, $b);
});

$categories = $conn->query("SELECT DISTINCT name FROM categories ORDER BY name");

$statsSql = "SELECT COUNT(*) AS total, COALESCE(SUM(p.stock),0) AS total_stock,
             COALESCE(SUM(p.price * p.stock),0) AS total_value,
             COALESCE(SUM(CASE WHEN p.stock < 20 THEN 1 ELSE 0 END),0) AS low_stock
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE 1=1";
if (!empty($search)) {
    $statsSql .= " AND (p.name LIKE '%$safe_search%' OR p.description LIKE '%$safe_search%')";
}
if (!empty($category)) {
    $statsSql .= " AND c.name = '$safe_category'";
}
$stats = $conn->query($statsSql)->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Products - Tindahan Store System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="shelf-page">
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h1>Products</h1>

        <p><a href="add.php" class="btn">+ Add Product</a></p>
        <p><a href="add_sale.php" class="btn">+ Add Sale</a></p>

        <form method="GET" class="filter-bar">
            <input type="text" name="search" placeholder="Search by name or description" value="<?= htmlspecialchars($search) ?>">
            <select name="category">
                <option value="">All Categories</option>
                <?php while ($c = $categories->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($c['name']) ?>" <?= $category === $c['name'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                <?php endwhile; ?>
            </select>
            <button type="submit">Filter</button>
            <a href="index.php" class="cancel">Reset</a>
        </form>

        <div class="cards">
            <div class="card">
                <div class="value"><?= (int)$stats['total'] ?></div>
                <div class="label">Total Products</div>
            </div>
            <div class="card">
                <div class="value"><?= (int)$stats['total_stock'] ?></div>
                <div class="label">Total Stock</div>
            </div>
            <div class="card">
                <div class="value">&#8369;<?= number_format($stats['total_value'], 2) ?></div>
                <div class="label">Inventory Value</div>
            </div>
            <div class="card">
                <div class="value"><?= (int)$stats['low_stock'] ?></div>
                <div class="label">Low Stock Items</div>
            </div>
        </div>

        <?php if (empty($shelves)): ?>
            <p class="shelf-empty">No products match yet. <a href="add.php">Add your first product</a> to start a shelf.</p>
        <?php else: ?>
            <?php foreach ($shelves as $shelfName => $items): ?>
                <div class="shelf-section">
                    <div class="shelf-header">
                        <h2><?= htmlspecialchars($shelfName) ?></h2>
                        <?php if ($shelfName !== 'Uncategorized'): ?>
                            <a class="shelf-arrow" href="index.php?category=<?= urlencode($shelfName) ?>">View all &rarr;</a>
                        <?php endif; ?>
                    </div>

                    <div class="shelf-row">
                        <?php foreach ($items as $row): ?>
                            <div class="shelf-card">
                                <div class="shelf-thumb-wrap">
                                    <?php if (!empty($row['image_url'])): ?>
                                        <img src="<?= htmlspecialchars($row['image_url']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                                    <?php else: ?>
                                        <div class="shelf-thumb-placeholder">&#128722;</div>
                                    <?php endif; ?>

                                    <?php if ((int)$row['stock'] === 0): ?>
                                        <span class="shelf-badge">Out of Stock</span>
                                    <?php elseif ($row['stock'] < 20): ?>
                                        <span class="shelf-badge">Low Stock</span>
                                    <?php endif; ?>

                                    <span class="shelf-price">&#8369;<?= number_format($row['price'], 2) ?></span>
                                </div>

                                <div class="shelf-body">
                                    <div class="shelf-title"><?= htmlspecialchars($row['name']) ?></div>

                                    <div class="shelf-meta">
                                        <span>&#128230; <strong class="<?= $row['stock'] < 20 ? 'low' : '' ?>"><?= $row['stock'] ?></strong> in stock</span>
                                    </div>
                                    <div class="shelf-sub">Supplier: <?= htmlspecialchars($row['supplier_name'] ?? '—') ?></div>
                                    <div class="shelf-sub">Added by: <?= htmlspecialchars($row['added_by_name'] ?? '—') ?></div>

                                    <div class="shelf-actions">
                                        <a href="edit.php?id=<?= $row['id'] ?>">Edit</a>
                                        <a href="delete.php?id=<?= $row['id'] ?>" class="shelf-delete">Delete</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <p class="count">Total: <?= $stats['total'] ?> product(s)</p>
    </div>
</body>
</html>