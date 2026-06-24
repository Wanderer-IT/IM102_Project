<?php
require_once 'config.php';
require_once 'auth.php';

requireLogin();

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

$sql = "
    SELECT
        p.products_ID,
        p.products_Name,
        p.products_Description,
        p.product_Price,
        p.product_Stock,
        c.category_Name,
        s.supplier_Name,
        p.created_At
    FROM products p
    INNER JOIN category c  ON p.category_ID  = c.category_ID
    INNER JOIN suppliers s ON p.suppliers_ID = s.suppliers_ID
    WHERE 1=1";

$params = [];
$types  = '';

if (!empty($search)) {
    $sql .= " AND (p.products_name LIKE ? OR p.products_Description LIKE ?)";
    $like = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
    $types   .= 'ss';
}

if (!empty($category)) {
    $sql .= " AND c.category_Name = ?";
    $params[] = $category;
    $types   .= 's';
}

$sql .= " ORDER BY p.products_ID ASC";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$categories = $conn->query("SELECT DISTINCT category_Name FROM category ORDER BY category_Name");

$stats_sql = "
    SELECT
        COUNT(*)                                                   AS total,
        SUM(p.product_Stock)                                       AS total_stock,
        SUM(p.product_Price * p.product_Stock)                     AS total_value,
        SUM(CASE WHEN p.product_Stock < 20 THEN 1 ELSE 0 END)     AS low_stock
    FROM products p
    JOIN category  c ON p.category_ID  = c.category_ID
    JOIN suppliers s ON p.suppliers_ID = s.suppliers_ID
    WHERE 1=1";

$stats_params = [];
$stats_types  = '';

if (!empty($search)) {
    $stats_sql .= " AND (p.products_name LIKE ? OR p.products_Description LIKE ?)";
    $like = '%' . $search . '%';
    $stats_params[] = $like;
    $stats_params[] = $like;
    $stats_types   .= 'ss';
}

if (!empty($category)) {
    $stats_sql .= " AND c.category_Name = ?";
    $stats_params[] = $category;
    $stats_types   .= 's';
}

$stats_stmt = $conn->prepare($stats_sql);
if ($stats_params) {
    $stats_stmt->bind_param($stats_types, ...$stats_params);
}
$stats_stmt->execute();
$stats = $stats_stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
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

        <h1>Products</h1>

        <div class="stats">
            <div class="stat-card">
                <h3>Total Products</h3>
                <p><?= $stats['total'] ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Stock</h3>
                <p><?= $stats['total_stock'] ?? 0 ?></p>
            </div>
            <div class="stat-card">
                <h3>Inventory Value</h3>
                <p>$<?= number_format($stats['total_value'] ?? 0, 2) ?></p>
            </div>
            <div class="stat-card">
                <h3>Low Stock Items</h3>
                <p><?= $stats['low_stock'] ?? 0 ?></p>
            </div>
        </div>

        <div class="toolbar">
            <form method="GET" class="filters">
                <input type="text" name="search" placeholder="Search name or description…"
                    value="<?= htmlspecialchars($search) ?>">

                <select name="category">
                    <option value="">All Categories</option>
                    <?php while ($c = $categories->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($c['category_Name']) ?>" <?= $c['category_Name'] == $category ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['category_Name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <button type="submit">Filter</button>
                <a href="index.php">Reset</a>
            </form>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Category</th>
                        <th>Supplier</th>
                        <th>Date Added</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['products_Name']) ?></td>
                            <td><?= htmlspecialchars($row['products_Description']) ?></td>
                            <td class="price">$<?= number_format($row['product_Price'], 2) ?></td>
                            <td class="stock <?= $row['product_Stock'] < 20 ? 'low-stock' : '' ?>">
                                <?= $row['product_Stock'] ?>
                            </td>
                            <td><span class="badge"><?= htmlspecialchars($row['category_Name']) ?></span></td>
                            <td><?= htmlspecialchars($row['supplier_Name']) ?></td>
                            <td class="date"><?= htmlspecialchars($row['created_At']) ?></td>
                            <td>
                                <div class="td-actions">
                                    <?php if (isAdmin()): ?>
                                    <a href="edit.php?id=<?= $row['products_ID'] ?>" class="link-edit">Edit</a>
                                    <a href="delete.php?id=<?= $row['products_ID'] ?>" class="link-delete">Delete</a>
                                    <?php else: ?>
                                    <span class="no-access">—</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </div>
</body>

</html>