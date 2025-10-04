<?php
require_once "../config.php";
session_start();
date_default_timezone_set('Asia/Manila'); // make sure times are PH-local

$user_id = $_SESSION['user_id'] ?? 1; // current logged-in user
$fed_by_user_id = $user_id; // the one who fed the bees

// Get the current feeding schedule for this user
$sql = "SELECT * FROM bee_feeding_schedule WHERE user_id=$user_id LIMIT 1";
$res = mysqli_query($link, $sql);
$feeding = mysqli_fetch_assoc($res);

if ($feeding) {
    // Get feeding interval (default 30 mins if not set)
    $interval = $feeding['interval_minutes'] ?? 1;

    // Calculate the new next_feed time based on current time
    $next_feed = date('Y-m-d H:i:s', strtotime("+$interval minutes"));

    // ✅ Insert new feeding record instead of updating
    $insert_sql = "
        INSERT INTO bee_feeding_schedule (user_id, fed_by_user_id, last_fed, fed_at, next_feed, interval_minutes)
        VALUES ($user_id, $fed_by_user_id, NOW(), NOW(), '$next_feed', $interval)
    ";

    mysqli_query($link, $insert_sql);
}

mysqli_close($link);
?>
