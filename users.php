<?php
require_once 'config.php';
require_once 'auth.php';
requireAdmin();

$message = '';

if (isset($_GET['created'])) {
    $message = '<p class="success">Staff account created.</p>';
}

// Actual removal only happens on a confirmed POST (and never for your own account).
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_remove_id'])) {
    $id = (int)$_POST['confirm_remove_id'];

    if ($id === (int)$_SESSION['user_id']) {
        $message = '<p class="error">You cannot remove your own account while logged in.</p>';
    } else {
        $conn->query("DELETE FROM users WHERE id = $id");
        header('Location: users.php');
        exit;
    }
}

// A GET with ?remove=id only shows a confirmation prompt - nothing is removed yet.
$pendingRemove = null;
if (isset($_GET['remove'])) {
    $remId = (int)$_GET['remove'];
    if ($remId === (int)$_SESSION['user_id']) {
        $message = '<p class="error">You cannot remove your own account while logged in.</p>';
    } else {
        $pendingRemove = $conn->query("SELECT id, username FROM users WHERE id = $remId")->fetch_assoc();
    }
}

$sql = "SELECT u.*, COUNT(p.id) AS product_count
        FROM users u
        LEFT JOIN products p ON u.id = p.added_by
        GROUP BY u.id
        ORDER BY u.created_at ASC";

$users = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Staff - Tindahan Store System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h1>Manage Staff</h1>

        <?= $message ?>

        <?php if ($pendingRemove): ?>
            <div class="credits-card" style="border-left:5px solid #d9534f; margin-bottom:20px;">
                <p>Remove account "<strong><?= htmlspecialchars($pendingRemove['username']) ?></strong>"? This cannot be undone.</p>
                <form method="POST">
                    <input type="hidden" name="confirm_remove_id" value="<?= $pendingRemove['id'] ?>">
                    <button type="submit" class="btn-danger">Yes, Remove</button>
                    <a href="users.php" class="cancel">Cancel</a>
                </form>
            </div>
        <?php endif; ?>

        <p><a href="register.php" class="btn">+ Add Staff</a></p>

        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Products Added</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
            <?php while ($u = $users->fetch_assoc()): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['username']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><span class="badge <?= $u['role'] === 'admin' ? 'badge-admin' : 'badge-staff' ?>"><?= htmlspecialchars($u['role']) ?></span></td>
                    <td><?= $u['product_count'] ?></td>
                    <td><?= $u['created_at'] ?></td>
                    <td class="actions">
                        <?php if ((int)$u['id'] !== (int)$_SESSION['user_id']): ?>
                            <a href="users.php?remove=<?= $u['id'] ?>">Remove</a>
                        <?php else: ?>
                            <span style="color:#aaa;">(you)</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <p class="count">Total: <?= $users->num_rows ?> user(s)</p>
    </div>
</body>
</html>