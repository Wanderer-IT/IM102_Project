<?php
require_once 'config.php';
require_once 'auth.php';

requireAdmin();

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM products WHERE products_ID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_assoc();
$stmt->close();

if (!$products) {
    die("Product not found.");
}

$categories = $conn->query("SELECT category_ID, category_Name FROM category");
$suppliers  = $conn->query("SELECT suppliers_ID, supplier_Name FROM suppliers");

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['products_name'] ?? '');
    $description = trim($_POST['products_Description'] ?? '');
    $Price = (double)($_POST['product_Price'] ?? 0);
    $Stock = (int)($_POST['product_Stock'] ?? 0);
    $Category = (int)($_POST['category_ID'] ?? 0);
    $Suppliers = (int)($_POST['suppliers_ID'] ?? 0);
    
    if ($name === '' || $description === '' || $Price <= 0 || $Stock < 0 || $Category <= 0 || $Suppliers <= 0) {
        $message = '<p style="color:red;">All fields are required.</p>';
    } else {
        $stmt = $conn->prepare(
            "UPDATE products SET
                products_name = ?,
                products_Description = ?,
                product_Price = ?,
                product_Stock = ?,
                category_ID = ?,
                suppliers_ID = ?
             WHERE products_ID = ?"
        );
        $stmt->bind_param("ssdiiii", $name, $description, $Price, $Stock, $Category, $Suppliers, $id);

        if ($stmt->execute()) {
            header('Location: index.php');
            exit;
        } else {
            $message = '<p style="color:red;">Error: ' . $stmt->error . '</p>';
        }
        $stmt->close();
    }
}


?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Edit Product #<?= $products['products_ID'] ?></h1>
        
        <?= $message ?>
        
        <form method="POST">
            <label>Full Name</label>
            <input type="text" name="products_name" value="<?= htmlspecialchars($products['products_name']) ?>" required>
            
            <label>Description</label>
            <input type="text" name="products_Description" value="<?= htmlspecialchars($products['products_Description']) ?>" required>
            
            <label>Price</label>
            <input type="number" name="product_Price" value="<?= $products['product_Price'] ?>" step="0.01" required min = "0" >
            
            <label>Stock</label>
            <input type="number" name="product_Stock" value="<?= $products['product_Stock'] ?>" min="0" required>
            
            <label>Category
                <select name="category_ID" required>
                <?php while ($cat = $categories->fetch_assoc()): ?>
                    <option value="<?= $cat['category_ID'] ?>" <?= $products['category_ID'] == $cat['category_ID'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['category_Name']) ?>
                    </option>
                <?php endwhile; ?>
                </select>
            </label>
            
            <label>Suppliers
                <select name="suppliers_ID" required>
                <option value="">-- Select Supplier --</option>
                <?php while ($sup = $suppliers->fetch_assoc()): ?>
                    <option value="<?= $sup['suppliers_ID'] ?>" <?= $products['suppliers_ID'] == $sup['suppliers_ID'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($sup['supplier_Name']) ?>
                    </option>
                <?php endwhile; ?>
                </select>
            </label>

            <button type="submit">Update Product</button>
            <a href="index.php" class="cancel">Cancel</a>
        </form>
    </div>
</body>
</html>