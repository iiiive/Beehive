<?php
session_start();
require_once "../config.php";

header('Content-Type: application/json');
date_default_timezone_set('Asia/Manila');

$user_id = $_SESSION['user_id'] ?? 0;
if (!$user_id) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Not logged in']);
    exit;
}

// get interval
$sql = "SELECT interval_minutes FROM bee_feeding_schedule WHERE user_id = $user_id LIMIT 1";
$res = mysqli_query($link, $sql);

if ($row = mysqli_fetch_assoc($res)) {
    $interval = (int)$row['interval_minutes'];
    if ($interval <= 0) {
        echo json_encode(['ok' => false, 'error' => 'No interval set']);
        exit;
    }

    $now  = new DateTime('now', new DateTimeZone('Asia/Manila'));
    $next = clone $now;
    $next->modify('+' . $interval . ' minutes');

    $last_fed  = $now->format('Y-m-d H:i:s');
    $next_feed = $next->format('Y-m-d H:i:s');

    mysqli_query(
        $link,
        "UPDATE bee_feeding_schedule
         SET last_fed = '$last_fed',
             next_feed = '$next_feed'
         WHERE user_id = $user_id"
    );

    echo json_encode([
        'ok'        => true,
        'last_fed'  => $last_fed,
        'next_feed' => $next_feed
    ]);

} else {
    echo json_encode(['ok' => false, 'error' => 'No schedule found']);
}

mysqli_close($link);
