<?php
require_once 'config.php';
require_once 'auth.php';
requireAdmin();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $name = $conn->real_escape_string($_POST['name'] ?? '');
    $contact_person = $conn->real_escape_string($_POST['contact_person'] ?? '');
    $phone = $conn->real_escape_string($_POST['phone'] ?? '');
    $email = $conn->real_escape_string($_POST['email'] ?? '');

    if (empty($_POST['name'])) {
        $message = '<p class="error">Supplier name is required.</p>';
    } else {
        $sql = "INSERT INTO suppliers (name, contact_person, phone, email) VALUES ('$name', '$contact_person', '$phone', '$email')";
        if ($conn->query($sql)) {
            header('Location: suppliers.php');
            exit;
        } else {
            $message = '<p class="error">Error: ' . $conn->error . '</p>';
        }
    }
}

// Actual deletion only happens on a confirmed POST
// (products linked to it get supplier_id = NULL, ON DELETE SET NULL).
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete_id'])) {
    $id = (int)$_POST['confirm_delete_id'];
    $conn->query("DELETE FROM suppliers WHERE id = $id");
    header('Location: suppliers.php');
    exit;
}

// A GET with ?delete=id only shows a confirmation prompt - nothing is deleted yet.
$pendingDelete = null;
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $pendingDelete = $conn->query("SELECT id, name FROM suppliers WHERE id = $delId")->fetch_assoc();
}

$suppliers = $conn->query("
    SELECT s.*, COUNT(p.id) AS product_count
    FROM suppliers s
    LEFT JOIN products p ON s.id = p.supplier_id
    GROUP BY s.id
    ORDER BY s.id ASC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Suppliers - Tindahan Store System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h1>Suppliers</h1>

        <?= $message ?>

        <?php if ($pendingDelete): ?>
            <div class="credits-card" style="border-left:5px solid #d9534f; margin-bottom:20px;">
                <p>Delete supplier "<strong><?= htmlspecialchars($pendingDelete['name']) ?></strong>"? Linked products will lose their supplier link. This cannot be undone.</p>
                <form method="POST">
                    <input type="hidden" name="confirm_delete_id" value="<?= $pendingDelete['id'] ?>">
                    <button type="submit" class="btn-danger">Yes, Delete</button>
                    <a href="suppliers.php" class="cancel">Cancel</a>
                </form>
            </div>
        <?php endif; ?>

        <form method="POST" class="filter-bar">
            <input type="text" name="name" placeholder="Supplier name" required>
            <input type="text" name="contact_person" placeholder="Contact person">
            <input type="text" name="phone" placeholder="Phone">
            <input type="text" name="email" placeholder="Email">
            <button type="submit">+ Add Supplier</button>
        </form>

        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Contact Person</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Products</th>
                <th>Actions</th>
            </tr>
            <?php while ($s = $suppliers->fetch_assoc()): ?>
                <tr>
                    <td><?= $s['id'] ?></td>
                    <td><?= htmlspecialchars($s['name']) ?></td>
                    <td><?= htmlspecialchars($s['contact_person'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($s['phone'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($s['email'] ?? '—') ?></td>
                    <td><?= $s['product_count'] ?></td>
                    <td class="actions">
                        <a href="suppliers.php?delete=<?= $s['id'] ?>">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <p class="count">Total: <?= $suppliers->num_rows ?> supplier(s)</p>
    </div>
</body>
</html>