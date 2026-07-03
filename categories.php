<?php
require_once 'config.php';
require_once 'auth.php';
requireAdmin();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $name = $conn->real_escape_string($_POST['name'] ?? '');
    $description = $conn->real_escape_string($_POST['description'] ?? '');

    if (empty($_POST['name'])) {
        $message = '<p class="error">Category name is required.</p>';
    } else {
        $sql = "INSERT INTO categories (name, description) VALUES ('$name', '$description')";
        if ($conn->query($sql)) {
            header('Location: categories.php');
            exit;
        } else {
            $message = '<p class="error">Error: ' . $conn->error . '</p>';
        }
    }
}

// Actual deletion only happens on a confirmed POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete_id'])) {
    $id = (int)$_POST['confirm_delete_id'];
    $conn->query("DELETE FROM categories WHERE id = $id");
    header('Location: categories.php');
    exit;
}

$pendingDelete = null;
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $pendingDelete = $conn->query("SELECT id, name FROM categories WHERE id = $delId")->fetch_assoc();
}

$categories = $conn->query("
    SELECT c.*, COUNT(p.id) AS product_count
    FROM categories c
    LEFT JOIN products p ON c.id = p.category_id
    GROUP BY c.id
    ORDER BY c.name
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Categories - Tindahan Store System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h1>Categories</h1>

        <?= $message ?>

        <?php if ($pendingDelete): ?>
            <div class="credits-card" style="border-left:5px solid #d9534f; margin-bottom:20px;">
                <p>Delete category "<strong><?= htmlspecialchars($pendingDelete['name']) ?></strong>"? Products in it will become uncategorized. This cannot be undone.</p>
                <form method="POST">
                    <input type="hidden" name="confirm_delete_id" value="<?= $pendingDelete['id'] ?>">
                    <button type="submit" class="btn-danger">Yes, Delete</button>
                    <a href="categories.php" class="cancel">Cancel</a>
                </form>
            </div>
        <?php endif; ?>

        <form method="POST" class="filter-bar">
            <input type="text" name="name" placeholder="New category name" required>
            <input type="text" name="description" placeholder="Description (optional)">
            <button type="submit">+ Add Category</button>
        </form>

        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Products</th>
                <th>Actions</th>
            </tr>
            <?php while ($c = $categories->fetch_assoc()): ?>
                <tr>
                    <td><?= $c['id'] ?></td>
                    <td><?= htmlspecialchars($c['name']) ?></td>
                    <td><?= htmlspecialchars($c['description']) ?></td>
                    <td><?= $c['product_count'] ?></td>
                    <td class="actions">
                        <a href="categories.php?delete=<?= $c['id'] ?>">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>