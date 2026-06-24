<?php
require_once 'config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $Name        = trim($_POST['userName'] ?? '');
    $Email       = trim($_POST['email'] ?? '');
    $Password    = $_POST['password'] ?? '';
    $ConfirmPass = $_POST['confirm_password'] ?? '';
    $role        = 'staff'; // always staff on self-registration

    if (empty($Name) || empty($Email) || empty($Password) || empty($ConfirmPass)) {
        $message = '<p style="color:red;">All fields are required.</p>';

    } elseif (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
        $message = '<p style="color:red;">Invalid email format.</p>';

    } elseif (strlen($Password) < 6) {
        $message = '<p style="color:red;">Password must be at least 6 characters long.</p>';

    } elseif ($Password !== $ConfirmPass) {
        $message = '<p style="color:red;">Passwords do not match.</p>';

    } else {
        $checkStmt = $conn->prepare("SELECT user_ID FROM users WHERE userName = ? OR email = ?");
        $checkStmt->bind_param("ss", $Name, $Email);
        $checkStmt->execute();
        $check = $checkStmt->get_result();

        if ($check && $check->num_rows > 0) {
            $message = '<p style="color:red;">Username or Email already exists.</p>';
        } else {
            $password_hash = password_hash($Password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (userName, email, password_hash, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $Name, $Email, $password_hash, $role);

            if ($stmt->execute()) {
                $message = '<p style="color:green;">Account created! <a href="login.php">Sign in</a></p>';
            } else {
                $message = '<p style="color:red;">Error: ' . $stmt->error . '</p>';
            }
            $stmt->close();
        }
        $checkStmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create New User</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <h1>Create New Account</h1>
    <p style="color:#666; margin-bottom:1rem;">New accounts are registered as <strong>Staff</strong>.</p>

    <?= $message ?>

    <form method="POST">

        <label>Username</label>
        <input type="text" name="userName" required placeholder="Enter your username"
            value="<?= htmlspecialchars($_POST['userName'] ?? '') ?>">

        <label>Email</label>
        <input type="email" name="email" required placeholder="Enter your email"
            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

        <label>Password</label>
        <input type="password" name="password" required placeholder="Minimum 6 characters">

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required placeholder="Re-enter password">

        <br>

        <button type="submit">Register</button>
        <a href="login.php" class="login">Back to Login</a>

    </form>

</div>

</body>
</html>