<?php
require_once 'config.php';
require_once 'auth.php';

requireAdmin();

$id = (int)($_GET['id'] ?? 0);
$result = $conn->query("SELECT * FROM products WHERE products_ID = $id");
$products = $result->fetch_assoc();

if (!$products) {
    die("Product not found.");
}

$categories = $conn->query("SELECT category_ID, category_Name FROM category");
$suppliers  = $conn->query("SELECT suppliers_ID, supplier_Name FROM suppliers");

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['products_name'] ?? '');
    $description = $conn->real_escape_string($_POST['products_Description'] ?? '');
    $Price = (double)($_POST['product_Price'] ?? 0);
    $Stock = (int)($_POST['product_Stock'] ?? 0);
    $Category = (int)($_POST['category_ID'] ?? 0);
    $Suppliers = (int)($_POST['suppliers_ID'] ?? 0);
    
    if (empty($name) || empty($description) || empty($Price) || empty($Stock) || empty($Category) || empty($Suppliers)) {
        $message = '<p style="color:red;">All fields are required.</p>';
    } else {
        $sql = "UPDATE products SET 
                products_name='$name', 
                products_Description='$description', 
                product_Price=$Price,
                product_Stock=$Stock, 
                category_ID=$Category, 
                suppliers_ID=$Suppliers
                WHERE products_ID=$id";
        
        if ($conn->query($sql)) {
            header('Location: index.php');
            exit;
        } else {
            $message = '<p style="color:red;">Error: ' . $conn->error . '</p>';
        }
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