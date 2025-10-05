<?php
require_once "../config.php";
session_start();

// Check if admin is logged in (you should already have an admin session)
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("location: admin-login.php");
    exit;
}

// Handle delete/deactivate request
if (isset($_GET['delete_id'])) {
    $user_id = intval($_GET['delete_id']);
    $sql = "UPDATE users SET status = 'inactive' WHERE user_id = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: manage-users.php?deleted=1");
    exit;
}

// Handle reactivate request
if (isset($_GET['activate_id'])) {
    $user_id = intval($_GET['activate_id']);
    $sql = "UPDATE users SET status = 'active' WHERE user_id = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: manage-users.php?activated=1");
    exit;
}

// Fetch all users
$sql = "SELECT user_id, firstname, lastname, username, email, status, created_at 
        FROM users ORDER BY created_at DESC";
$result = mysqli_query($link, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4 text-center">Manage Users</h2>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-warning">User account has been deactivated.</div>
    <?php elseif (isset($_GET['activated'])): ?>
        <div class="alert alert-success">User account has been reactivated.</div>
    <?php endif; ?>

    <table class="table table-bordered table-striped text-center align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $row['user_id'] ?></td>
                <td><?= htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td>
                    <?php if ($row['status'] == 'active'): ?>
                        <span class="badge bg-success">Active</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Inactive</span>
                    <?php endif; ?>
                </td>
                <td><?= $row['created_at'] ?></td>
                <td>
                    <?php if ($row['status'] == 'active'): ?>
                        <a href="?delete_id=<?= $row['user_id'] ?>" class="btn btn-danger btn-sm"
                           onclick="return confirm('Are you sure you want to deactivate this user?');">
                           Deactivate
                        </a>
                    <?php else: ?>
                        <a href="?activate_id=<?= $row['user_id'] ?>" class="btn btn-success btn-sm">
                           Reactivate
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
