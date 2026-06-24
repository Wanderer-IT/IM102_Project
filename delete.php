<?php
require_once 'config.php';
require_once 'auth.php';

requireAdmin();


$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("DELETE FROM products WHERE products_ID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header('Location: index.php');
    exit;
}

$stmt = $conn->prepare("SELECT products_name, products_Description, product_Price, product_Stock FROM products WHERE products_ID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
    die("Product not found.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Delete Product</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Delete Product</h1>
        
        <p>Are you sure you want to delete:</p>
        <p class="name"><?= htmlspecialchars($product['products_name']) ?></p>
        <p class="details"><?= htmlspecialchars($product['products_Description']) ?> — Price <?= $product['product_Price'] ?>  — Stocks <?= $product['product_Stock'] ?></p>
        <p class="warning">This action cannot be undone.</p>
        
        <form method="POST" style="display: inline;">
            <button type="submit" class="btn-delete">Yes, Delete</button>
        </form>
        <a href="index.php" class="btn-cancel">Cancel</a>
    </div>
</body>
</html>