<?php
require_once "../config.php";

$sql = "SELECT u.firstname, u.lastname, f.next_feed, f.fed_at
        FROM bee_feeding_schedule f
        JOIN users u ON f.fed_by_user_id = u.user_id
        ORDER BY f.fed_at DESC";

$res = mysqli_query($link, $sql);
?>


<a href="admin-dashboard.php" class="btn btn-secondary">
    ← Back to Dashboard
</a>

<table class="table">
  <thead>
    <tr>
      <th>User</th>
      <th>Last Fed Time</th>
      <th>Next Feed</th>
    </tr>
  </thead>
  <tbody>
    <?php while($row = mysqli_fetch_assoc($res)): ?>
      <tr>
        <td><?= htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) ?></td>
        <td><?= htmlspecialchars($row['fed_at']) ?></td>
        <td><?= htmlspecialchars($row['next_feed']) ?></td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>
