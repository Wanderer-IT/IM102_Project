<?php
require_once 'config.php';


$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $Name        = $conn->real_escape_string(trim($_POST['userName'] ?? ''));
    $Email       = $conn->real_escape_string(trim($_POST['email'] ?? ''));
    $Password    = $_POST['password'] ?? '';
    $ConfirmPass = $_POST['confirm_password'] ?? '';
    $role        = (int)($_POST['role'] ?? 0);

    if (empty($Name) || empty($Email) || empty($Password) || empty($ConfirmPass) || $role === 0) {
        $message = '<p style="color:red;">All fields are required.</p>';

    } elseif (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
        $message = '<p style="color:red;">Invalid email format.</p>';

    } elseif (strlen($Password) < 6) {
        $message = '<p style="color:red;">Password must be at least 6 characters long.</p>';

    } elseif ($Password !== $ConfirmPass) {
        $message = '<p style="color:red;">Passwords do not match.</p>';

    } else {
        $check = $conn->query("SELECT user_ID FROM users WHERE userName = '$Name' OR email = '$Email'");

        if ($check && $check->num_rows > 0) {
            $message = '<p style="color:red;">Username or Email already exists.</p>';
        } else {
            $password_hash = password_hash($Password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (userName, email, password_hash, role)
                    VALUES ('$Name', '$Email', '$password_hash', $role)";

            if ($conn->query($sql)) {
                $message = '<p style="color:green;">User registered successfully!</p>';
            } else {
                $message = '<p style="color:red;">Error: ' . $conn->error . '</p>';
            }
        }
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

    <h1>Create New User</h1>

    <?= $message ?>

    <form method="POST">

        <label>Username</label>
        <input type="text" name="userName" required placeholder="ENTER YOUR USERNAME"
            value="<?= htmlspecialchars($_POST['userName'] ?? '') ?>">

        <label>Email</label>
        <input type="email" name="email" required placeholder="ENTER YOUR EMAIL"
            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

        <label>Password</label>
        <input type="password" name="password" required>

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required>

        <label>Role</label>
        <select name="role" required>
            <option value="0">Select Role</option>
            <option value="1" <?= (($_POST['role'] ?? 0) == 1) ? 'selected' : '' ?>>Admin</option>
            <option value="2" <?= (($_POST['role'] ?? 0) == 2) ? 'selected' : '' ?>>Staff</option>
        </select>

        <br><br>

        <button type="submit">Create New User</button>
        <a href="login.php" class="login">Login</a>

    </form>

</div>

</body>
</html>