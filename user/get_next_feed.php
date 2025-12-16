<?php
session_start();
require_once "../config.php";
header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? 0;
if (!$user_id) {
  echo json_encode(["has_schedule" => false]);
  exit;
}

$sql = "SELECT interval_minutes, last_fed, next_feed, timer_state, remaining_seconds
        FROM bee_feeding_schedule
        WHERE user_id = $user_id
        LIMIT 1";
$res = mysqli_query($link, $sql);

if (!$res || mysqli_num_rows($res) === 0) {
  echo json_encode(["has_schedule" => false]);
  exit;
}

$row = mysqli_fetch_assoc($res);

$timer_state = strtolower(trim($row['timer_state'] ?? 'running'));
$remaining   = (int)($row['remaining_seconds'] ?? 0);
$next        = $row['next_feed']; // ✅ DO NOT null this

// Optional: compute "hungry" server-side ONLY if running and time passed
if ($timer_state === "running") {
  $nextTs = strtotime($next);
  if ($nextTs !== false && $nextTs <= time()) {
    $timer_state = "hungry";
  }
}

echo json_encode([
  "has_schedule"       => true,
  "interval_minutes"   => (int)($row['interval_minutes'] ?? 0),
  "last_fed"           => $row['last_fed'] ?: null,
  "next_feed"          => $next,                 // ✅ always string
  "timer_state"        => $timer_state,
  "remaining_seconds"  => $remaining
]);

mysqli_close($link);
