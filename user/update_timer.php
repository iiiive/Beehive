<?php
session_start();
require_once "../config.php";

$state = $_POST['state'] ?? null;
$now   = time();

if (!in_array($state, ['running','paused','stopped'])) {
  echo json_encode(["success"=>false]);
  exit;
}

// Get latest row
$q = mysqli_query($link, "
  SELECT id, next_feed, remaining_seconds, timer_state
  FROM bee_feeding_schedule
  ORDER BY id DESC
  LIMIT 1
");
$row = mysqli_fetch_assoc($q);

$id = $row['id'];

if ($state === 'paused') {
  // 🔒 Freeze remaining time
  $remaining = strtotime($row['next_feed']) - $now;
  if ($remaining < 0) $remaining = 0;

  mysqli_query($link, "
    UPDATE bee_feeding_schedule
    SET timer_state='paused',
        remaining_seconds=$remaining
    WHERE id=$id
  ");
}

elseif ($state === 'running') {
  // ▶ Resume from remaining time
  if ($row['remaining_seconds'] !== null) {
    $newNextFeed = date('Y-m-d H:i:s', $now + $row['remaining_seconds']);

    mysqli_query($link, "
      UPDATE bee_feeding_schedule
      SET timer_state='running',
          next_feed='$newNextFeed',
          remaining_seconds=NULL
      WHERE id=$id
    ");
  } else {
    // normal resume
    mysqli_query($link, "
      UPDATE bee_feeding_schedule
      SET timer_state='running'
      WHERE id=$id
    ");
  }
}

elseif ($state === 'stopped') {
  mysqli_query($link, "
    UPDATE bee_feeding_schedule
    SET timer_state='stopped',
        remaining_seconds=NULL
    WHERE id=$id
  ");
}

echo json_encode(["success"=>true,"state"=>$state]);
