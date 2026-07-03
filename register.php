<?php
require_once 'config.php';
require_once 'auth.php';
requireAdmin();

$errors = [];
$username = '';
$email = '';
$role = 'staff';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = ($_POST['role'] ?? '') === 'admin' ? 'admin' : 'staff';

    // Validate empty fields
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = 'All fields are required.';
    }

    if (strlen($username) < 3) {
        $errors[] = 'Username must be at least 3 characters long.';
    }

    // Validate email format
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    // Validate password length
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long.';
    }

    // Validate password match
    if ($password !== $confirm_password) {
        $errors[] = 'Password and Confirm Password do not match.';
    }

    // Check if username or email already exists
    if (empty($errors)) {
        $safe_username = $conn->real_escape_string($username);
        $safe_email = $conn->real_escape_string($email);

        $check = $conn->query("SELECT id FROM users WHERE username = '$safe_username' OR email = '$safe_email'");

        if ($check->num_rows > 0) {
            $errors[] = 'That username or email is already registered.';
        }
    }

    // If everything passes, hash the password and insert
    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $safe_username = $conn->real_escape_string($username);
        $safe_email = $conn->real_escape_string($email);

        $sql = "INSERT INTO users (username, email, password_hash, role) VALUES ('$safe_username', '$safe_email', '$hashed', '$role')";

        if ($conn->query($sql)) {
            header('Location: users.php?created=1');
            exit;
        } else {
            $errors[] = 'Error: ' . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Staff - Tindahan Store System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container" style="max-width:500px;">
        <h1>Add New Staff Account</h1>
        <p style="color:#666;">Only admins can create new accounts. Choose a role below.</p>

        <?php if (!empty($errors)): ?>
            <ul class="error">
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="POST">
            <label>Username</label>
            <input type="text" name="username" required value="<?= htmlspecialchars($username) ?>" placeholder="e.g. jrosales">

            <label>Email</label>
            <input type="email" name="email" required value="<?= htmlspecialchars($email) ?>" placeholder="e.g. juan@email.com">

            <label>Password</label>
            <input type="password" name="password" required placeholder="At least 6 characters">

            <label>Confirm Password</label>
            <input type="password" name="confirm_password" required placeholder="Re-type password">

            <label>Role</label>
            <select name="role">
                <option value="staff" <?= $role === 'staff' ? 'selected' : '' ?>>Staff</option>
                <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>

            <button type="submit">Create Account</button>
            <a href="users.php" class="cancel">Cancel</a>
        </form>
    </div>
</body>
</html>