<?php require_once 'auth.php'; ?>
<nav class="navbar">
    <a href="index.php">Products</a>
    <a href="add.php">+ Add Product</a>
    <a href="report.php">Reports</a>
    <?php if (isAdmin()): ?>
        <a href="categories.php">Categories</a>
        <a href="suppliers.php">Suppliers</a>
        <a href="users.php">Manage Staff</a>
    <?php endif; ?>
    <span class="navbar-user">
        <?= htmlspecialchars(getUsername()) ?>
        <span class="badge <?= isAdmin() ? 'badge-admin' : 'badge-staff' ?>"><?= htmlspecialchars($_SESSION['role']) ?></span>
        <a href="logout.php">Logout</a>
    </span>
</nav>