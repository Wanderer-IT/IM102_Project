<?php
require_once 'config.php';
require_once 'auth.php';

// If already logged in, redirect away from the login page
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$justRegistered = isset($_GET['registered']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $result = $conn->query("SELECT * FROM users WHERE username = '$username'");
    $user = $result->fetch_assoc();

    // Same error message whether the user doesn't exist or the password is wrong
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Tindahan Store System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container" style="max-width:420px;">
        <h1>Tindahan Login</h1>

        <?php if ($justRegistered): ?>
            <p class="success">Account created! You may now log in.</p>
        <?php endif; ?>

        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Username</label>
            <input type="text" name="username" required placeholder="Enter your username">

            <label>Password</label>
            <input type="password" name="password" required placeholder="Enter your password">

            <button type="submit">Login</button>
        </form>

        <p style="margin-top:15px; color:#888; font-size:0.85em;">Need an account? Ask your admin to add you as staff.</p>
    </div>
</body>
</html>