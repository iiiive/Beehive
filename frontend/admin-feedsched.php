<?php
require_once "../config.php";

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
  align-items: center;
  justify-content: center;
  position: relative;
  flex-direction: column;
  padding: 40px 0;
}
body::before {
  content: "";
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background-image: url("https://beeswiki.com/wp-content/uploads/2023/03/Are-there-stingless-bees-1024x683.png");
  background-size: cover;
  background-position: center;
  filter: brightness(25%);
  z-index: -1;
}
.container {
  width: 85%;
  max-width: 900px;
  background: #fff7c3ff;
  border-radius: 20px;
  box-shadow: 0px 0px 24px #ceae1fff;
  padding: 30px 40px;
  animation: fadeIn 1s ease-in-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-30px); }
  to { opacity: 1; transform: translateY(0); }
}
h2 {
  text-align: center;
  color: #47300cff;
  font-size: 35px;
  margin-bottom: 25px;
}
.table {
  width: 100%;
  border-collapse: collapse;
  background: rgba(255, 255, 255, 0.6);
  border-radius: 15px;
  overflow: hidden;
}
.table th, .table td {
  padding: 15px 18px;
  text-align: center;
  font-size: 18px;
}
.table thead {
  background-color: #e7d25bff;
  color: #47300cff;
}
.table tbody tr:nth-child(even) {
  background: rgba(255, 255, 255, 0.7);
}
.table tbody tr:hover {
  background: #fff2a6;
  transition: 0.3s ease;
}
.table td {
  color: #47300cff;
  font-weight: 600;
}
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
  transition: background 0.3s ease, transform 0.2s ease;
  z-index: 1000;
}
.back-btn:hover {
  background: #cdbd49;
  color: #000;
  transform: scale(1.05);
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
        <?php while($row = mysqli_fetch_assoc($res)): ?>
          <tr>
            <td><?= htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) ?></td>
            <td><?= htmlspecialchars($row['fed_at'] ?: '—') ?></td>
            <td><?= htmlspecialchars($row['next_feed'] ?: '—') ?></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="3" style="color:#888; font-weight:bold;">No feeding records found</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

</body>
</html>
