<?php
require_once 'config.php';
require_once 'auth.php';

requireAdmin();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $Name        = $_POST['product_Name']        ?? '';
    $Description = $_POST['product_Description'] ?? '';
    $Price       = (double)($_POST['product_Price']  ?? 0);
    $Stock       = (int)($_POST['product_Stock']     ?? 0);
    $Category    = (int)($_POST['category_ID']       ?? 0);
    $Suppliers   = (int)($_POST['suppliers_ID']       ?? 0);

    if ($Name === '' || $Description === '' || $Price <= 0 || $Stock < 0 || $Category <= 0 || $Suppliers <= 0) {

        $message = '<p style="color:red;">All fields are required.</p>';

    } else {

        $stmt = $conn->prepare(
            "INSERT INTO products (products_name, products_Description, product_Price, product_Stock, category_ID, suppliers_ID)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("ssdiii", $Name, $Description, $Price, $Stock, $Category, $Suppliers);

        if ($stmt->execute()) {
            header('Location: index.php');
            exit;
        } else {
            $message = '<p style="color:red;">Error: ' . $stmt->error . '</p>';
        }

        $stmt->close();
    }
}

$categories = $conn->query("SELECT category_ID, category_Name FROM category ORDER BY category_Name");
$suppliers  = $conn->query("SELECT suppliers_ID, supplier_Name FROM suppliers ORDER BY supplier_Name");
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <h1>Add New Product</h1>

    <?= $message ?>

    <form method="POST">

        <label>Product Name</label>
        <input
            type="text"
            name="product_Name"
            required
            placeholder="Product name"
            value="<?= htmlspecialchars($_POST['product_Name'] ?? '') ?>">

        <label>Product Description</label>
        <input
            type="text"
            name="product_Description"
            required
            placeholder="Description"
            value="<?= htmlspecialchars($_POST['product_Description'] ?? '') ?>">

        <label>Price</label>
        <input
            type="number"
            name="product_Price"
            step="0.01"
            min="0"
            required
            placeholder="Price"
            value="<?= htmlspecialchars($_POST['product_Price'] ?? '') ?>">

        <label>Stock</label>
        <input
            type="number"
            name="product_Stock"
            min="0"
            required
            placeholder="Stock"
            value="<?= htmlspecialchars($_POST['product_Stock'] ?? '') ?>">

        <label>Category</label>
        <select name="category_ID" required>
            <option value="">-- Select Category --</option>
            <?php while ($cat = $categories->fetch_assoc()): ?>
                <option value="<?= $cat['category_ID'] ?>"
                    <?= (($_POST['category_ID'] ?? '') == $cat['category_ID']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['category_Name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Supplier</label>
        <select name="suppliers_ID" required>
            <option value="">-- Select Supplier --</option>
            <?php while ($sup = $suppliers->fetch_assoc()): ?>
                <option value="<?= $sup['suppliers_ID'] ?>"
                    <?= (($_POST['suppliers_ID'] ?? '') == $sup['suppliers_ID']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($sup['supplier_Name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit">Add Product</button>
        <a href="index.php" class="cancel">Cancel</a>

    </form>

</div>
</body>
</html>