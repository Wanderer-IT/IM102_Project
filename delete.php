<?php
require_once 'config.php';
require_once 'auth.php';

requireAdmin();


$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->query("DELETE FROM products WHERE products_ID = $id");
    header('Location: index.php');
    exit;
}

$result = $conn->query("SELECT product_Name, product_Description, product_Price, product_Stocks FROM products WHERE products_ID = $id");
$product = $result->fetch_assoc();

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
        <p class="name"><?= htmlspecialchars($product['product_Name']) ?></p>
        <p class="details"><?= $product['product_Description'] ?> — Price <?= $product['product_Price'] ?>  — Stocks <?= $product['product_Stocks'] ?></p>
        <p class="warning">This action cannot be undone.</p>
        
        <form method="POST" style="display: inline;">
            <button type="submit" class="btn-delete">Yes, Delete</button>
        </form>
        <a href="index.php" class="btn-cancel">Cancel</a>
    </div>
</body>
</html>