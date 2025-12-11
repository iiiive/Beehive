<?php
session_start();
require_once "../config.php";

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? 0;
if (!$user_id) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT interval_minutes, last_fed, next_feed
        FROM bee_feeding_schedule
        WHERE user_id = $user_id
        LIMIT 1";

$res = mysqli_query($link, $sql);

if ($row = mysqli_fetch_assoc($res)) {
    $next = $row['next_feed'];
    if ($next === '0000-00-00 00:00:00') {
        $next = null;
    }

    echo json_encode([
        'interval_minutes' => (int)$row['interval_minutes'],
        'last_fed'         => $row['last_fed'] ?: null,
        'next_feed'        => $next ?: null,
    ]);
} else {
    echo json_encode([]);
}

mysqli_close($link);
