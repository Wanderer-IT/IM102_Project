<?php
require_once 'config.php';
require_once 'auth.php';
requireLogin();

$updateMessage = isset($_GET['updated']) ? '<p class="success">Sale updated.</p>' : '';

// ---- Sales history (search + list) ----
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$salesSql = "SELECT sa.*, p.name AS product_name, u.username AS served_by_name
             FROM sales sa
             LEFT JOIN products p ON sa.product_id = p.id
             LEFT JOIN users u ON sa.served_by = u.id
             WHERE 1=1";

if (!empty($search)) {
    $safe_search = $conn->real_escape_string($search);
    $salesSql .= " AND p.name LIKE '%$safe_search%'";
}

$salesSql .= " ORDER BY sa.sold_at DESC";

// Keep the default list short, but let a search look past that cap.
if (empty($search)) {
    $salesSql .= " LIMIT 5";
}

$salesResult = $conn->query($salesSql);

$salesStats = $conn->query("
    SELECT COUNT(*) AS total_sales, COALESCE(SUM(quantity),0) AS items_sold, COALESCE(SUM(total_price),0) AS revenue
    FROM sales
")->fetch_assoc();

// ---- Inventory overview ----
$overall = $conn->query("
    SELECT COUNT(*) AS total, COALESCE(SUM(stock),0) AS total_stock,
           COALESCE(SUM(price * stock),0) AS total_value
    FROM products
")->fetch_assoc();

// ---- Per-category / per-supplier breakdowns ----
$byCategory = $conn->query("
    SELECT c.name, COUNT(p.id) AS products,
           COALESCE(SUM(p.stock),0) AS total_stock,
           COALESCE(SUM(p.price * p.stock),0) AS total_value,
           COALESCE(AVG(p.price),0) AS avg_price
    FROM categories c
    LEFT JOIN products p ON c.id = p.category_id
    GROUP BY c.id, c.name
    ORDER BY total_value DESC
");

$bySupplier = $conn->query("
    SELECT s.name, COUNT(p.id) AS products,
           COALESCE(SUM(p.stock),0) AS total_stock
    FROM suppliers s
    LEFT JOIN products p ON s.id = p.supplier_id
    GROUP BY s.id, s.name
    ORDER BY total_stock DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sales & Reports - Tindahan Store System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h1>Sales & Reports</h1>

        <?= $updateMessage ?>

        <div class="cards">
            <div class="card">
                <div class="value"><?= (int)$overall['total'] ?></div>
                <div class="label">Total Products</div>
            </div>
            <div class="card">
                <div class="value"><?= (int)$overall['total_stock'] ?></div>
                <div class="label">Total Stock</div>
            </div>
            <div class="card">
                <div class="value">&#8369;<?= number_format($overall['total_value'], 2) ?></div>
                <div class="label">Inventory Value</div>
            </div>
            <div class="card">
                <div class="value"><?= (int)$salesStats['total_sales'] ?></div>
                <div class="label">Total Transactions</div>
            </div>
            <div class="card">
                <div class="value"><?= (int)$salesStats['items_sold'] ?></div>
                <div class="label">Items Sold</div>
            </div>
            <div class="card">
                <div class="value">&#8369;<?= number_format($salesStats['revenue'], 2) ?></div>
                <div class="label">Total Sales Revenue</div>
            </div>
        </div>

        <div class="section-divider">
            <h2>Sales History</h2>
        </div>

        <form method="GET" class="filter-bar">
            <input type="text" name="search" placeholder="Search by product name" value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
            <a href="report.php" class="cancel">Reset</a>
        </form>

        <table>
            <tr>
                <th>ID</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Served By</th>
                <th>Date Sold</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $salesResult->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['product_name'] ?? 'Deleted product') ?></td>
                    <td><?= $row['quantity'] ?></td>
                    <td>&#8369;<?= number_format($row['total_price'], 2) ?></td>
                    <td><?= htmlspecialchars($row['served_by_name'] ?? '—') ?></td>
                    <td><?= $row['sold_at'] ?></td>
                    <td class="actions">
                        <a href="edit_sale.php?id=<?= $row['id'] ?>">Edit</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <p class="count">
            <?php if (empty($search)): ?>
                Showing <?= $salesResult->num_rows ?> most recent of <?= (int)$salesStats['total_sales'] ?> transaction(s). Search above to find older sales.
            <?php else: ?>
                <?= $salesResult->num_rows ?> transaction(s) matching "<?= htmlspecialchars($search) ?>"
            <?php endif; ?>
        </p>

        <div class="section-divider">
            <h2>Per-Category Breakdown</h2>
        </div>
        <table>
            <tr>
                <th>Category</th>
                <th>Products</th>
                <th>Total Stock</th>
                <th>Avg. Price</th>
                <th>Total Value</th>
            </tr>
            <?php while ($row = $byCategory->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= $row['products'] ?></td>
                    <td><?= $row['total_stock'] ?></td>
                    <td>&#8369;<?= number_format($row['avg_price'], 2) ?></td>
                    <td>&#8369;<?= number_format($row['total_value'], 2) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>

        <div class="section-divider">
            <h2>Per-Supplier Breakdown</h2>
        </div>
        <table>
            <tr>
                <th>Supplier</th>
                <th>Products Supplied</th>
                <th>Total Stock</th>
            </tr>
            <?php while ($row = $bySupplier->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= $row['products'] ?></td>
                    <td><?= $row['total_stock'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>