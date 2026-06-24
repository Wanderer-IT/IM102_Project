<?php
require_once 'config.php';
require_once 'auth.php';

requireAdmin();

$message = '';

// ── DELETE ──────────────────────────────────────────────────────────────────
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $del_id = (int)($_GET['id'] ?? 0);

    // prevent admin from deleting themselves
    if ($del_id === (int)$_SESSION['user_id']) {
        $message = '<p style="color:red;">You cannot delete your own account.</p>';
    } elseif ($del_id > 0) {
        $stmt = $conn->prepare("DELETE FROM users WHERE user_ID = ? AND role = 'staff'");
        $stmt->bind_param("i", $del_id);
        $stmt->execute();
        $stmt->close();
        header('Location: users.php?deleted=1');
        exit;
    }
}

if (isset($_GET['deleted'])) {
    $message = '<p style="color:green;">Staff account deleted.</p>';
}

// ── EDIT (inline form) ──────────────────────────────────────────────────────
$edit_user = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit') {
    $edit_id = (int)($_GET['id'] ?? 0);
    $stmt = $conn->prepare("SELECT user_ID, userName, email, role FROM users WHERE user_ID = ? AND role = 'staff'");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $edit_user = $res ? $res->fetch_assoc() : null;
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $uid      = (int)($_POST['user_ID'] ?? 0);
    $uname    = trim($_POST['userName'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $new_pass = $_POST['new_password'] ?? '';

    if ($uname === '' || $email === '') {
        $message = '<p style="color:red;">Username and email are required.</p>';
    } elseif ($new_pass !== '' && strlen($new_pass) < 6) {
        $message = '<p style="color:red;">Password must be at least 6 characters.</p>';
    } else {
        // check duplicate username/email (exclude self)
        $dupStmt = $conn->prepare("SELECT user_ID FROM users WHERE (userName = ? OR email = ?) AND user_ID != ?");
        $dupStmt->bind_param("ssi", $uname, $email, $uid);
        $dupStmt->execute();
        $dup = $dupStmt->get_result();

        if ($dup && $dup->num_rows > 0) {
            $message = '<p style="color:red;">Username or email already taken.</p>';
        } else {
            if ($new_pass !== '') {
                $hash = password_hash($new_pass, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET userName = ?, email = ?, password_hash = ? WHERE user_ID = ? AND role = 'staff'");
                $stmt->bind_param("sssi", $uname, $email, $hash, $uid);
            } else {
                $stmt = $conn->prepare("UPDATE users SET userName = ?, email = ? WHERE user_ID = ? AND role = 'staff'");
                $stmt->bind_param("ssi", $uname, $email, $uid);
            }

            if ($stmt->execute()) {
                header('Location: users.php?updated=1');
                exit;
            } else {
                $message = '<p style="color:red;">Error: ' . $stmt->error . '</p>';
            }
            $stmt->close();
        }
        $dupStmt->close();
    }
}

if (isset($_GET['updated'])) {
    $message = '<p style="color:green;">Staff account updated.</p>';
}

// ── FETCH ALL STAFF ──────────────────────────────────────────────────────────
$staff = $conn->query("SELECT user_ID, userName, email, role, created_At FROM users WHERE role = 'staff' ORDER BY created_At DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff</title>
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

    <h1>Manage Staff</h1>

    <?= $message ?>

    <?php if ($edit_user): ?>
    <!-- ── Edit form ── -->
    <div class="card" style="margin-bottom:1.5rem; padding:1.25rem; border:1px solid #e2e8f0; border-radius:8px;">
        <h2 style="margin-bottom:1rem;">Edit Staff: <?= htmlspecialchars($edit_user['userName']) ?></h2>
        <form method="POST">
            <input type="hidden" name="user_ID" value="<?= $edit_user['user_ID'] ?>">
            <input type="hidden" name="update_user" value="1">

            <label>Username</label>
            <input type="text" name="userName" value="<?= htmlspecialchars($edit_user['userName']) ?>" required>

            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($edit_user['email']) ?>" required>

            <label>New Password <small style="color:#888;">(leave blank to keep current)</small></label>
            <input type="password" name="new_password" placeholder="Enter new password">

            <button type="submit">Save Changes</button>
            <a href="users.php" class="cancel">Cancel</a>
        </form>
    </div>
    <?php endif; ?>

    <!-- ── Staff table ── -->
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Date Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($staff && $staff->num_rows > 0): ?>
                    <?php while ($u = $staff->fetch_assoc()): ?>
                        <tr>
                            <td><?= $u['user_ID'] ?></td>
                            <td><?= htmlspecialchars($u['userName']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><span class="badge"><?= ucfirst($u['role']) ?></span></td>
                            <td class="date"><?= htmlspecialchars($u['created_At'] ?? '—') ?></td>
                            <td>
                                <div class="td-actions">
                                    <a href="users.php?action=edit&id=<?= $u['user_ID'] ?>" class="link-edit">Edit</a>
                                    <a href="users.php?action=delete&id=<?= $u['user_ID'] ?>"
                                       class="link-delete"
                                       onclick="return confirm('Delete <?= htmlspecialchars($u['userName']) ?>? This cannot be undone.')">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="no-data">No staff accounts found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>