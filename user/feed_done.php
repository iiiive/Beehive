<?php
session_start();
require_once "../config.php";

header('Content-Type: application/json');
date_default_timezone_set('Asia/Manila');

$user_id = (int)($_SESSION['user_id'] ?? 0);
if (!$user_id) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Not logged in']);
    exit;
}

// Ensure interval exists and > 0
$res = mysqli_query($link, "SELECT interval_minutes FROM bee_feeding_schedule WHERE user_id=$user_id LIMIT 1");
if (!$res) {
    echo json_encode(['ok'=>false,'error'=>mysqli_error($link)]);
    exit;
}
$row = mysqli_fetch_assoc($res);
if (!$row) {
    echo json_encode(['ok'=>false,'error'=>'No schedule found']);
    exit;
}

$interval = (int)$row['interval_minutes'];
if ($interval <= 0) {
    echo json_encode(['ok'=>false,'error'=>'No interval set']);
    exit;
}

// Update using SQL (source of truth)
$upd = mysqli_query($link, "
  UPDATE bee_feeding_schedule
  SET
    last_fed = NOW(),
    next_feed = DATE_ADD(NOW(), INTERVAL interval_minutes MINUTE),
    timer_state='running',
    remaining_seconds=0,
    paused_at=NULL
  WHERE user_id=$user_id
");

if (!$upd) {
    echo json_encode(['ok'=>false,'error'=>mysqli_error($link)]);
    exit;
}

// Return what is actually stored
$res2 = mysqli_query($link, "SELECT last_fed, next_feed FROM bee_feeding_schedule WHERE user_id=$user_id LIMIT 1");
if (!$res2) {
    echo json_encode(['ok'=>false,'error'=>mysqli_error($link)]);
    exit;
}
$saved = mysqli_fetch_assoc($res2);

echo json_encode([
    'ok'        => true,
    'last_fed'  => $saved['last_fed'],
    'next_feed' => $saved['next_feed']
]);

mysqli_close($link);
