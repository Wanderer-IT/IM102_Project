<?php
require_once 'config.php';
require_once 'auth.php';

requireLogin();

$summary_sql = "
    SELECT
        COUNT(products_ID)              AS total_products,
        SUM(product_Stock)              AS total_stock,
        SUM(product_Stock * product_Price) AS total_value
    FROM products";
$summary = $conn->query($summary_sql)->fetch_assoc();

$category_sql = "
    SELECT
        c.category_Name                                    AS category_name,
        COUNT(p.products_ID)                               AS products_count,
        IFNULL(SUM(p.product_Stock), 0)                    AS total_stock,
        IFNULL(SUM(p.product_Price * p.product_Stock), 0)  AS total_value,
        IFNULL(AVG(p.product_Price), 0)                    AS avg_price
    FROM category c
    LEFT JOIN products p ON c.category_ID = p.category_ID
    GROUP BY c.category_ID, c.category_Name
    ORDER BY total_value DESC";
$category_report = $conn->query($category_sql);

$supplier_sql = "
    SELECT
        s.supplier_Name                 AS supplier_name,
        COUNT(p.products_ID)            AS products_count,
        IFNULL(SUM(p.product_Stock), 0) AS total_stock
    FROM suppliers s
    LEFT JOIN products p ON s.suppliers_ID = p.suppliers_ID
    GROUP BY s.suppliers_ID, s.supplier_Name
    ORDER BY total_stock DESC";
$supplier_report = $conn->query($supplier_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Report</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar">
        <a href="index.php" class="navbar-brand">
        </a>
        <div class="navbar-links">
            <a href="index.php" class="nav-link active">Products</a>
            <a href="report.php" class="nav-link">View Report</a>
            <?php if (isAdmin()): ?>
            <a href="add.php" class="nav-link btn">+ Add Product</a>
            <a href="users.php" class="nav-link">Manage Staff</a>
            <?php endif; ?>
            <span class="user-badge">
                <span class="user-icon">&#128100;</span>
                <?= htmlspecialchars(getUsername()) ?>
                <span class="role-tag role-<?= htmlspecialchars(getRole()) ?>"><?= htmlspecialchars(ucfirst(getRole())) ?></span>
            </span>
            <a href="logout.php" class="nav-link">Logout</a>
        </div>
    </nav>

<div class="container">

    <h2 class="section-heading">Overall Summary</h2>

    <div class="stats">
        <div class="stat-card">
            <h3>Total Distinct Products</h3>
            <p><?= number_format($summary['total_products'] ?? 0) ?></p>
        </div>
        <div class="stat-card">
            <h3>Total Accumulated Units</h3>
            <p><?= number_format($summary['total_stock'] ?? 0) ?></p>
        </div>
        <div class="stat-card">
            <h3>Gross Inventory Value</h3>
            <p>₱<?= number_format($summary['total_value'] ?? 0, 2) ?></p>
        </div>
    </div>

    <!-- ── Category breakdown ── -->
    <div class="table-section">
        <h2 class="section-heading">Per-Category Breakdown</h2>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Category</th>
                        <th class="number-col">Products</th>
                        <th class="number-col">Total Stock</th>
                        <th class="number-col">Avg Price</th>
                        <th class="number-col">Total Value</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($category_report && $category_report->num_rows > 0): ?>
                        <?php while ($cat = $category_report->fetch_assoc()): ?>
                            <tr>
                                <td><span class="category-name"><?= htmlspecialchars($cat['category_name']) ?></span></td>
                                <td class="number-col"><?= number_format($cat['products_count']) ?></td>
                                <td class="number-col"><?= number_format($cat['total_stock']) ?> units</td>
                                <td class="number-col">₱<?= number_format($cat['avg_price'], 2) ?></td>
                                <td class="number-col">₱<?= number_format($cat['total_value'], 2) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="no-data">No categories configured.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ── Supplier breakdown ── -->
    <div class="table-section">
        <h2 class="section-heading">Per-Supplier Breakdown</h2>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Supplier</th>
                        <th class="number-col">Products Supplied</th>
                        <th class="number-col">Total Stock</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($supplier_report && $supplier_report->num_rows > 0): ?>
                        <?php while ($sup = $supplier_report->fetch_assoc()): ?>
                            <tr>
                                <td><span class="supplier-name"><?= htmlspecialchars($sup['supplier_name']) ?></span></td>
                                <td class="number-col"><?= number_format($sup['products_count']) ?></td>
                                <td class="number-col"><?= number_format($sup['total_stock']) ?> units</td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="3" class="no-data">No suppliers configured.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</body>
</html>