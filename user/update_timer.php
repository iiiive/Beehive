<?php
session_start();
require_once "../config.php";

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? 0;
if (!$user_id) {
  echo json_encode(["ok"=>false, "error"=>"Not logged in"]);
  exit;
}

$state = strtolower(trim($_POST['state'] ?? ''));
$remaining = isset($_POST['remaining_seconds']) ? (int)$_POST['remaining_seconds'] : null;

$allowed = ["running","paused","stopped","hungry"];
if (!in_array($state, $allowed, true)) {
  echo json_encode(["ok"=>false, "error"=>"Invalid state"]);
  exit;
}

// Ensure row exists
$check = mysqli_query($link, "SELECT user_id, next_feed, remaining_seconds FROM bee_feeding_schedule WHERE user_id=$user_id LIMIT 1");
if (!$check || mysqli_num_rows($check) === 0) {
  echo json_encode(["ok"=>false, "error"=>"No feeding schedule row for this user"]);
  exit;
}

$row = mysqli_fetch_assoc($check);
$next_feed = $row['next_feed'];

if ($state === "paused") {
    $remaining = max(0, (int)($_POST['remaining_seconds'] ?? 0));

    mysqli_query($link, "
        UPDATE bee_feeding_schedule
        SET timer_state='paused',
            remaining_seconds=$remaining,
            paused_at=NOW()
        WHERE user_id=$user_id
    ");
    echo json_encode(["ok"=>true]);
    exit;
}


if ($state === "running") {
    $remaining = max(0, (int)($_POST['remaining_seconds'] ?? 0));

    if ($remaining > 0) {
        mysqli_query($link, "
            UPDATE bee_feeding_schedule
            SET timer_state='running',
                next_feed = DATE_ADD(NOW(), INTERVAL $remaining SECOND),
                remaining_seconds=0,
                paused_at=NULL
            WHERE user_id=$user_id
        ");
    } else {
        // Don't overwrite next_feed if remaining is 0
        mysqli_query($link, "
            UPDATE bee_feeding_schedule
            SET timer_state='running',
                remaining_seconds=0,
                paused_at=NULL
            WHERE user_id=$user_id
        ");
    }

    echo json_encode(["ok"=>true]);
    exit;
}


if ($state === "stopped") {
    $remaining = max(0, (int)($_POST['remaining_seconds'] ?? 0));

    mysqli_query($link, "
        UPDATE bee_feeding_schedule
        SET timer_state='stopped',
            remaining_seconds=$remaining,
            paused_at=NOW()
        WHERE user_id=$user_id
    ");
    echo json_encode(["ok"=>true]);
    exit;
}


if ($state === "hungry") {
  $sql = "UPDATE bee_feeding_schedule
          SET timer_state='hungry'
          WHERE user_id=$user_id";
  mysqli_query($link, $sql);

  echo json_encode(["ok"=>true]);
  exit;
}
