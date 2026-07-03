<?php
require_once 'config.php';
require_once 'auth.php';
requireLogin();

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->query("DELETE FROM products WHERE id = $id");
    header('Location: index.php');
    exit;
}

$product = $conn->query("
    SELECT p.*, c.name AS category, s.name AS supplier
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN suppliers s ON p.supplier_id = s.id
    WHERE p.id = $id
")->fetch_assoc();

if (!$product) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Delete Product - Tindahan Store System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container" style="max-width:500px;">
        <h1>Delete Product</h1>
        <p>Are you sure you want to delete this product? This cannot be undone.</p>

        <?php if (!empty($product['image_url'])): ?>
            <div class="img-container" style="max-width:150px; margin-bottom:15px;">
                <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="max-width:100%;">
            </div>
        <?php endif; ?>

        <table>
            <tr><th>Name</th><td><?= htmlspecialchars($product['name']) ?></td></tr>
            <tr><th>Category</th><td><?= htmlspecialchars($product['category'] ?? '—') ?></td></tr>
            <tr><th>Supplier</th><td><?= htmlspecialchars($product['supplier'] ?? '—') ?></td></tr>
            <tr><th>Price</th><td>&#8369;<?= number_format($product['price'], 2) ?></td></tr>
            <tr><th>Stock</th><td><?= $product['stock'] ?></td></tr>
        </table>

        <form method="POST">
            <button type="submit" class="btn-danger">Yes, Delete</button>
            <a href="index.php" class="cancel">Cancel</a>
        </form>
    </div>
</body>
</html>