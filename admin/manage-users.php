<?php
require_once "../config.php";
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("location: admin-login.php");
    exit;
}

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

if (isset($_GET['permanent_delete_id'])) {
    $user_id = intval($_GET['permanent_delete_id']);
    $sql = "DELETE FROM users WHERE user_id = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: manage-users.php?permadeleted=1");
    exit;
}

$sql = "SELECT user_id, firstname, lastname, username, email, status, created_at 
        FROM users ORDER BY created_at DESC";
$result = mysqli_query($link, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Users</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css?family=Raleway:400,700" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Raleway', sans-serif;
}
body {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  padding-top: 80px;
  position: relative;
  color: white;
}
body::before {
  content: "";
  position: absolute;
  inset: 0;
  background: url('../frontend/images/profile_addusers.jpeg') no-repeat center center/cover;
  filter: brightness(25%);
  z-index: -1;
}
.container {
  width: 90%;
  max-width: 1100px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 20px;
  backdrop-filter: blur(15px);
  border: 1px solid rgba(255,255,255,0.2);
  box-shadow: 0px 0px 24px #ceae1fff;
  padding: 30px;
  animation: fadeIn 1s ease-in-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-30px); }
  to { opacity: 1; transform: translateY(0); }
}
h2 {
  text-align: center;
  color: #e7d25bff;
  font-size: 28px;
  margin-bottom: 25px;
}
.table {
  width: 100%;
  border-collapse: collapse;
  text-align: center;
}
.table th, .table td {
  padding: 12px;
  border-bottom: 1px solid rgba(255,255,255,0.2);
}
.table th {
  background: rgba(255,255,255,0.15);
  color: #e7d25bff;
}
.table tr:hover {
  background: rgba(255,255,255,0.1);
  transition: 0.3s;
}

/* 🔹 Customizable Buttons */
.btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  justify-content: center;
  padding: 8px 14px;
  border-radius: 10px;
  border: none;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.3s ease;
  margin: 2px;
  min-width: 100px;
  text-decoration: none;
  color: white;
}
.btn i {
  font-size: 1rem;
}

/* 🔸 Custom Colors — you can freely edit these */
.btn-add {
  background: #47300cff; /* gold */
}
.btn-add:hover {
  background: #d4aa2a;
  transform: scale(1.05);
}

.btn-deactivate {
  background: #74512D; /* orange */
}
.btn-deactivate:hover {
  background: #cf6e1a;
  transform: scale(1.05);
}

.btn-reactivate {
  background: #27ae60; /* green */
}
.btn-reactivate:hover {
  background: #219150;
  transform: scale(1.05);
}

.btn-delete {
  background: #532d28ff; /* red */
}
.btn-delete:hover {
  background: #c0392b;
  transform: scale(1.05);
}

/* Back Button */
.back-btn {
  position: absolute;
  top: 20px;
  left: 20px;
  padding: 10px 20px;
  font-size: 1rem;
  font-weight: bold;
  color: #333;
  background: #e7d25bff;
  border-radius: 20px;
  text-decoration: none;
  box-shadow: 4px 4px 10px rgba(0,0,0,0.3);
  transition: all 0.3s ease;
}
.back-btn:hover {
  background: #cdbd49;
  transform: scale(1.05);
}

/* Badges */
.badge {
  padding: 6px 10px;
  border-radius: 8px;
  font-size: 0.85rem;
  font-weight: bold;
}
.bg-success { background: #4CAF50; color: white; }
.bg-secondary { background: #aaa; color: black; }

.alert {
  text-align: center;
  font-weight: bold;
  margin-bottom: 20px;
  border-radius: 10px;
}
.alert-warning { color: #e7d25bff; }
.alert-success { color: lightgreen; }
.alert-danger { color: #ff7b7b; }

.table-responsive {
  overflow-x: auto;
}
/* ===== MOBILE CARD VIEW (NO HORIZONTAL SCROLL) ===== */
@media (max-width: 768px) {

  .table-responsive {
    overflow-x: hidden;
  }

  table,
  thead,
  tbody,
  th,
  td,
  tr {
    display: block;
    width: 100%;
  }

  thead {
    display: none; /* hide table header */
  }

  tbody tr {
    background: rgba(255,255,255,0.12);
    border-radius: 15px;
    padding: 15px;
    margin-bottom: 15px;
    box-shadow: 0 6px 15px rgba(0,0,0,0.25);
  }

  tbody td {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border: none;
    text-align: left;
    font-size: 0.9rem;
  }

  tbody td::before {
    content: attr(data-label);
    font-weight: bold;
    color: #e7d25bff;
    margin-right: 10px;
    flex: 1;
  }

  tbody td > * {
    flex: 2;
    text-align: right;
  }

  /* Action buttons stack nicely */
  tbody td:last-child {
    flex-direction: column;
    gap: 8px;
  }

  .btn {
    width: 100%;
    min-width: unset;
  }
}

}
</style>
</head>
<body>

<a href="admin-dashboard.php" class="back-btn"><i class="bi bi-arrow-left"></i> Back</a>

<div class="container">
  <h2>Manage Users</h2>

  <?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert-warning">User account has been deactivated.</div>
  <?php elseif (isset($_GET['activated'])): ?>
    <div class="alert alert-success">User account has been reactivated.</div>
  <?php elseif (isset($_GET['permadeleted'])): ?>
    <div class="alert alert-danger">User account permanently deleted.</div>
  <?php endif; ?>

  <div class="text-center mb-3">
    <a href="add_users.php" class="btn btn-add"><i class="bi bi-person-plus-fill"></i> Add Users</a>
  </div>

  <div class="table-responsive">
    <table class="table">
      <thead>
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
    <td data-label="ID"><?= $row['user_id'] ?></td>

    <td data-label="Full Name">
      <?= htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) ?>
    </td>

    <td data-label="Username"><?= htmlspecialchars($row['username']) ?></td>

    <td data-label="Email"><?= htmlspecialchars($row['email']) ?></td>

    <td data-label="Status">
      <?php if ($row['status'] == 'active'): ?>
        <span class="badge bg-success">Active</span>
      <?php else: ?>
        <span class="badge bg-secondary">Inactive</span>
      <?php endif; ?>
    </td>

    <td data-label="Created At"><?= $row['created_at'] ?></td>

    <td data-label="Actions">
      <?php if ($row['status'] == 'active'): ?>
        <a href="?delete_id=<?= $row['user_id'] ?>"
           class="btn btn-deactivate"
           onclick="return confirm('Deactivate this user?');">
          <i class="bi bi-person-dash-fill"></i> Deactivate
        </a>
      <?php else: ?>
        <a href="?activate_id=<?= $row['user_id'] ?>"
           class="btn btn-reactivate">
          <i class="bi bi-person-check-fill"></i> Reactivate
        </a>
      <?php endif; ?>

      <a href="?permanent_delete_id=<?= $row['user_id'] ?>"
         class="btn btn-delete"
         onclick="return confirm('⚠️ Permanently delete this user?');">
        <i class="bi bi-trash3-fill"></i> Delete
      </a>
    </td>
  </tr>
<?php endwhile; ?>
</tbody>

    </table>
  </div>
</div>
</body>
</html>
