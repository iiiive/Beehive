<?php
require_once "../config.php";
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit;
}

$sql = "SELECT u.firstname, u.lastname, f.next_feed, f.fed_at
        FROM bee_feeding_schedule f
        JOIN users u ON f.fed_by_user_id = u.user_id
        ORDER BY f.fed_at DESC LIMIT 10";

$res = mysqli_query($link, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Feeding History</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
  font-family: Raleway, sans-serif;
}

body {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  padding: 80px 15px 30px;
  position: relative;
}

body::before {
  content: "";
  position: absolute;
  inset: 0;
  background: url("https://beeswiki.com/wp-content/uploads/2023/03/Are-there-stingless-bees-1024x683.png")
    no-repeat center/cover;
  filter: brightness(25%);
  z-index: -1;
}

.container {
  width: 100%;
  max-width: 900px;
  background: #fff7c3ff;
  border-radius: 20px;
  box-shadow: 0 0 24px #ceae1fff;
  padding: 30px;
}

h2 {
  text-align: center;
  color: #47300cff;
  font-size: 32px;
  margin-bottom: 25px;
}

/* ===== TABLE (DESKTOP) ===== */
.table {
  width: 100%;
  border-collapse: collapse;
  background: rgba(255,255,255,0.7);
  border-radius: 15px;
  overflow: hidden;
}

.table th,
.table td {
  padding: 14px;
  font-size: 16px;
  text-align: left;
}

.table thead {
  background: #e7d25bff;
  color: #47300cff;
}

.table tbody tr:nth-child(even) {
  background: rgba(255,255,255,0.8);
}

.table tbody tr:hover {
  background: #fff2a6;
}

.table td {
  color: #47300cff;
  font-weight: 600;
}

/* ===== BACK BUTTON ===== */
.back-btn {
  position: fixed;
  top: 15px;
  left: 15px;
  padding: 8px 16px;
  font-weight: bold;
  background: #e7d25bff;
  color: #333;
  border-radius: 20px;
  text-decoration: none;
  box-shadow: 4px 4px 10px rgba(0,0,0,0.3);
  z-index: 1000;
}

/* =========================
   📱 MOBILE CARD VIEW
   ========================= */
@media (max-width: 600px) {

  h2 {
    font-size: 22px;
  }

  .table,
  .table thead,
  .table tbody,
  .table th,
  .table tr {
    display: block;
    width: 100%;
  }

  .table thead {
    display: none;
  }

  .table tr {
    background: rgba(255,255,255,0.9);
    margin-bottom: 15px;
    border-radius: 12px;
    padding: 12px;
  }

  .table td {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    font-size: 14px;
    border-bottom: 1px solid rgba(0,0,0,0.1);
  }

  .table td:last-child {
    border-bottom: none;
  }

  .table td::before {
    content: attr(data-label);
    font-weight: bold;
    color: #47300cff;
  }
}
</style>
</head>

<body>

<a href="admin-dashboard.php" class="back-btn">← Back</a>

<div class="container">
  <h2>Feeding History</h2>

  <table class="table">
    <thead>
      <tr>
        <th>User</th>
        <th>Last Fed Time</th>
        <th>Next Feed</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($res && mysqli_num_rows($res) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($res)): ?>
          <tr>
            <td data-label="User"><?= htmlspecialchars($row['firstname'].' '.$row['lastname']) ?></td>
            <td data-label="Last Fed"><?= htmlspecialchars($row['fed_at'] ?: '—') ?></td>
            <td data-label="Next Feed"><?= htmlspecialchars($row['next_feed'] ?: '—') ?></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td data-label="Info">No feeding records found</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

</body>
</html>
