<?php
require_once "../config.php";
date_default_timezone_set('Asia/Manila');
mysqli_query($link, "SET time_zone = '+08:00'");

$sql = "SELECT u.username, f.last_fed, f.next_feed, f.timer_state,
               f.remaining_seconds
        FROM bee_feeding_schedule f
        JOIN users u ON f.fed_by_user_id = u.user_id
        ORDER BY f.id DESC
        LIMIT 1";

$result = mysqli_query($link, $sql);
$data = mysqli_fetch_assoc($result) ?: [];

if (!empty($data)) {
  if (strtolower($data['timer_state']) === 'paused') {
    $data['display_seconds'] = (int)$data['remaining_seconds'];
  } else {
    // running/stopped: calculate from next_feed
    $data['display_seconds'] = isset($data['next_feed'])
      ? max(0, (int)strtotime($data['next_feed']) - time())
      : 0;
  }
}

header('Content-Type: application/json');
echo json_encode($data);
