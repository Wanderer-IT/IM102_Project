<?php
require_once 'config.php';
require_once 'auth.php';
requireLogin();

if (!isset($_GET['id'])) {
    header('Location: report.php');
    exit;
}

$id = (int)$_GET['id'];

$sale = $conn->query("
    SELECT sa.*, p.name AS product_name
    FROM sales sa
    LEFT JOIN products p ON sa.product_id = p.id
    WHERE sa.id = $id
")->fetch_assoc();

if (!$sale) {
    header('Location: report.php');
    exit;
}

// The product/quantity this sale originally reserved. If the product was
// since deleted, product_id is NULL and there's nothing to give back.
$oldProductId = $sale['product_id'] !== null ? (int)$sale['product_id'] : 0;
$oldQuantity = (int)$sale['quantity'];

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search = trim($_POST['search'] ?? '');
    $selected_id = (int)($_POST['product_id'] ?? 0);
    $quantity_val = $_POST['quantity'] ?? '';
    $action = $_POST['action'] ?? '';
} else {
    // First load: pre-fill the search with the current product so it's
    // already sitting there, ready to have its quantity (or itself) fixed.
    $search = $sale['product_name'] ?? '';
    $selected_id = $oldProductId;
    $quantity_val = $oldQuantity;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save') {
    $quantity = (int)$quantity_val;

    if ($selected_id <= 0 || $quantity <= 0) {
        $message = '<p class="error">Please select a product and enter a valid quantity.</p>';
    } else {
        $product = $conn->query("SELECT * FROM products WHERE id = $selected_id")->fetch_assoc();

        if (!$product) {
            $message = '<p class="error">Selected product no longer exists.</p>';
        } else {
            // Stock actually available for this correction: the product's
            // current stock, plus whatever this sale already reserved if
            // it's the same product being kept.
            $availableStock = $product['stock'] + ($selected_id === $oldProductId ? $oldQuantity : 0);

            if ($quantity > $availableStock) {
                $message = '<p class="error">Not enough stock. Only ' . $availableStock . ' available.</p>';
            } else {
                $total_price = $product['price'] * $quantity;

                if ($oldProductId > 0) {
                    $conn->query("UPDATE products SET stock = stock + $oldQuantity WHERE id = $oldProductId");
                }
                $conn->query("UPDATE products SET stock = stock - $quantity WHERE id = $selected_id");

                $sql = "UPDATE sales SET product_id = $selected_id, quantity = $quantity, total_price = $total_price WHERE id = $id";

                if ($conn->query($sql)) {
                    header('Location: report.php?updated=1');
                    exit;
                } else {
                    // Roll the stock changes back out since the sale record itself didn't save.
                    if ($oldProductId > 0) {
                        $conn->query("UPDATE products SET stock = stock - $oldQuantity WHERE id = $oldProductId");
                    }
                    $conn->query("UPDATE products SET stock = stock + $quantity WHERE id = $selected_id");
                    $message = '<p class="error">Error: ' . $conn->error . '</p>';
                }
            }
        }
    }
}

$matches = [];
if ($search !== '') {
    $safe_search = $conn->real_escape_string($search);
    // A product still shows up here even at 0 stock, as long as this sale's
    // own reserved quantity would cover it once given back.
    $matches = $conn->query("
        SELECT id, name, price, stock, image_url,
               stock + IF(id = $oldProductId, $oldQuantity, 0) AS available_stock
        FROM products
        WHERE name LIKE '%$safe_search%'
        HAVING available_stock > 0
        ORDER BY name
    ")->fetch_all(MYSQLI_ASSOC);
}

$selectedProduct = null;
if ($selected_id > 0) {
    $selectedProduct = $conn->query("
        SELECT id, name, price, stock,
               stock + IF(id = $oldProductId, $oldQuantity, 0) AS available_stock
        FROM products
        WHERE id = $selected_id
    ")->fetch_assoc();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Sale - Tindahan Store System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container" style="max-width:500px;">
        <h1>Edit Sale #<?= $id ?></h1>
        <p class="count">Originally: <?= htmlspecialchars($sale['product_name'] ?? 'Deleted product') ?>, qty <?= $oldQuantity ?>, sold <?= $sale['sold_at'] ?>.</p>

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
                                <span><?= htmlspecialchars($p['name']) ?> (&#8369;<?= number_format($p['price'], 2) ?>, <?= $p['available_stock'] ?> available)</span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="count">No products match "<?= htmlspecialchars($search) ?>".</p>
                <?php endif; ?>
            <?php endif; ?>

            <label>Quantity</label>
            <input type="number" name="quantity" min="1" required value="<?= htmlspecialchars($quantity_val) ?>">

            <?php if ($selectedProduct): ?>
                <p class="count">
                    Selected: <?= htmlspecialchars($selectedProduct['name']) ?>
                    | Price: &#8369;<?= number_format($selectedProduct['price'], 2) ?>
                    | Stock available: <?= $selectedProduct['available_stock'] ?>
                </p>
            <?php endif; ?>

            <button type="submit" name="action" value="save">Save Correction</button>
            <a href="report.php" class="cancel">Cancel</a>
        </form>
    </div>
</body>
</html>