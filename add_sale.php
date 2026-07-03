<?php
require_once 'config.php';
require_once 'auth.php';
requireLogin();

$message = '';
$search = '';
$selected_id = 0;
$quantity_val = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search = trim($_POST['search'] ?? '');
    $selected_id = (int)($_POST['product_id'] ?? 0);
    $quantity_val = $_POST['quantity'] ?? '';
    $action = $_POST['action'] ?? '';

    if ($action === 'record') {
        $quantity = (int)$quantity_val;
        $served_by = (int)$_SESSION['user_id'];

        if ($selected_id <= 0 || $quantity <= 0) {
            $message = '<p class="error">Please select a product and enter a valid quantity.</p>';
        } else {
            $product = $conn->query("SELECT * FROM products WHERE id = $selected_id")->fetch_assoc();

            if (!$product) {
                $message = '<p class="error">Selected product no longer exists.</p>';
            } elseif ($quantity > $product['stock']) {
                $message = '<p class="error">Not enough stock. Only ' . $product['stock'] . ' left.</p>';
            } else {
                $total_price = $product['price'] * $quantity;
                $newStock = $product['stock'] - $quantity;

                $sql = "INSERT INTO sales (product_id, quantity, total_price, served_by)
                        VALUES ($selected_id, $quantity, $total_price, $served_by)";

                if ($conn->query($sql)) {
                    $conn->query("UPDATE products SET stock = $newStock WHERE id = $selected_id");
                    header('Location: report.php');
                    exit;
                } else {
                    $message = '<p class="error">Error: ' . $conn->error . '</p>';
                }
            }
        }
    }
}

$matches = [];
if ($search !== '') {
    $safe_search = $conn->real_escape_string($search);
    $matches = $conn->query("
        SELECT id, name, price, stock, image_url
        FROM products
        WHERE stock > 0 AND name LIKE '%$safe_search%'
        ORDER BY name
    ")->fetch_all(MYSQLI_ASSOC);
}

$selectedProduct = null;
if ($selected_id > 0) {
    $selectedProduct = $conn->query("SELECT id, name, price, stock FROM products WHERE id = $selected_id")->fetch_assoc();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Record Sale - Tindahan Store System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container" style="max-width:500px;">
        <h1>Record a Sale</h1>

        <?= $message ?>

        <form method="POST">
            <label>Search Product</label>
            <div class="search-row">
                <input type="text" name="search" placeholder="e.g. Coca-Cola" value="<?= htmlspecialchars($search) ?>">
                <button type="submit" name="action" value="search" formnovalidate>Search</button>
            </div>

            <?php if ($search !== ''): ?>
                <?php if (count($matches) > 0): ?>
                    <label>Select Product</label>
                    <div class="product-results">
                        <?php foreach ($matches as $p): ?>
                            <label class="product-option">
                                <input type="radio" name="product_id" value="<?= $p['id'] ?>" <?= $selected_id === (int)$p['id'] ? 'checked' : '' ?> required>
                                <?php if (!empty($p['image_url'])): ?>
                                    <img class="product-thumb" src="<?= htmlspecialchars($p['image_url']) ?>" alt="">
                                <?php else: ?>
                                    <span class="product-thumb"></span>
                                <?php endif; ?>
                                <span><?= htmlspecialchars($p['name']) ?> (&#8369;<?= number_format($p['price'], 2) ?>, <?= $p['stock'] ?> in stock)</span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="count">No in-stock products match "<?= htmlspecialchars($search) ?>".</p>
                <?php endif; ?>
            <?php else: ?>
                <p class="count">Type a product name above and click Search to find a product.</p>
            <?php endif; ?>

            <label>Quantity</label>
            <input type="number" name="quantity" min="1" required placeholder="1" value="<?= htmlspecialchars($quantity_val) ?>">

            <?php if ($selectedProduct): ?>
                <p class="count">
                    Selected: <?= htmlspecialchars($selectedProduct['name']) ?>
                    | Price: &#8369;<?= number_format($selectedProduct['price'], 2) ?>
                    | Stock available: <?= $selectedProduct['stock'] ?>
                </p>
            <?php endif; ?>

            <button type="submit" name="action" value="record">Record Sale</button>
            <a href="report.php" class="cancel">Cancel</a>
        </form>
    </div>
</body>
</html>