<?php
require_once 'config.php';
require_once 'auth.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password']      ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please fill in all fields.';
    } else {
        $stmt = $conn->prepare("SELECT user_ID, userName, password_hash, role FROM users WHERE userName = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user   = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id']  = $user['user_ID'];
            $_SESSION['username'] = $user['userName'];
            $_SESSION['role']     = $user['role'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>#</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="wrapper">

    <div class="brand">
        <div class="brand-icon">
        </div>
        <h1>Inventory Management System</h1>
    </div>

    <div class="card">

        <?php if ($error !== ''): ?>
        <div class="error-banner">
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="login.php" autocomplete="on">

            <div class="field">
                <label for="username">Username</label>
                <div class="input-wrap">
                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Enter your username"
                        value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                        autocomplete="username"
                        autofocus
                        class="<?= $error ? 'has-error' : '' ?>"
                    >
                </div>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        autocomplete="current-password"
                        class="<?= $error ? 'has-error' : '' ?>"
                    >
                    <button type="button" class="toggle-pw" onclick="togglePw()" title="Show/hide password">
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-login">Sign In</button>

        </form>
    </div>
</div>
</body>
</html>