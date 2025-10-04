<?php
require_once "../config.php";
session_start();
date_default_timezone_set('Asia/Manila');

$user_id = $_SESSION['user_id'] ?? 1;
$fed_by_user_id = $user_id;

// Fetch last feeding interval for this user (if any)
$sql = "SELECT interval_minutes FROM bee_feeding_schedule WHERE user_id = $user_id ORDER BY id DESC LIMIT 1";
$res = mysqli_query($link, $sql);
$row = mysqli_fetch_assoc($res);

$interval = $row['interval_minutes'] ?? 30; // default 30 mins
$next_feed = date('Y-m-d H:i:s', strtotime("+$interval minutes"));

// ✅ Insert new record (keep history)
$insert_sql = "
    INSERT INTO bee_feeding_schedule (user_id, fed_by_user_id, last_fed, fed_at, next_feed, interval_minutes)
    VALUES ($user_id, $fed_by_user_id, NOW(), NOW(), '$next_feed', $interval)
";

if (mysqli_query($link, $insert_sql)) {
    echo "Feeding recorded successfully.";
} else {
    echo "Error: " . mysqli_error($link);
}

mysqli_close($link);
?>
