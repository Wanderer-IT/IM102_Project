<?php
require_once 'config.php';
require_once 'auth.php';
requireLogin();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name'] ?? '');
    $description = $conn->real_escape_string($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $image_url = !empty($_POST['image_url']) ? "'" . $conn->real_escape_string(trim($_POST['image_url'])) . "'" : 'NULL';
    $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : 'NULL';
    $supplier_id = !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : 'NULL';
    $added_by = (int)$_SESSION['user_id'];

    if (empty($_POST['name']) || $price < 0 || $stock < 0) {
        $message = '<p class="error">Name is required, and price/stock cannot be negative.</p>';
    } else {
        $sql = "INSERT INTO products (name, description, price, stock, image_url, category_id, supplier_id, added_by)
                VALUES ('$name', '$description', $price, $stock, $image_url, $category_id, $supplier_id, $added_by)";

        if ($conn->query($sql)) {
            header('Location: index.php');
            exit;
        } else {
            $message = '<p class="error">Error: ' . $conn->error . '</p>';
        }
    }
}

$categories = $conn->query("SELECT id, name FROM categories ORDER BY name");
$suppliers = $conn->query("SELECT id, name FROM suppliers ORDER BY name");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Product - Tindahan Store System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container" style="max-width:550px;">
        <h1>Add New Product</h1>

        <?= $message ?>

        <form method="POST">
            <label>Product Name</label>
            <input type="text" name="name" required placeholder="e.g. Piattos Cheese 40g" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">

            <label>Description</label>
            <textarea name="description" rows="3" placeholder="Optional short description"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>

            <label>Price (&#8369;)</label>
            <input type="number" name="price" step="0.01" min="0" required placeholder="0.00" value="<?= htmlspecialchars($_POST['price'] ?? '') ?>">

            <label>Stock Quantity</label>
            <input type="number" name="stock" min="0" required placeholder="0" value="<?= htmlspecialchars($_POST['stock'] ?? '') ?>">

            <label>Image URL</label>
            <input type="url" name="image_url" placeholder="https://example.com/image.jpg" value="<?= htmlspecialchars($_POST['image_url'] ?? '') ?>">

            <label>Category</label>
            <select name="category_id">
                <option value="">-- Select Category --</option>
                <?php while ($cat = $categories->fetch_assoc()): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endwhile; ?>
            </select>

            <label>Supplier</label>
            <select name="supplier_id">
                <option value="">-- Select Supplier --</option>
                <?php while ($sup = $suppliers->fetch_assoc()): ?>
                    <option value="<?= $sup['id'] ?>"><?= htmlspecialchars($sup['name']) ?></option>
                <?php endwhile; ?>
            </select>

            <button type="submit">Add Product</button>
            <a href="index.php" class="cancel">Cancel</a>
        </form>
    </div>
</body>
</html>