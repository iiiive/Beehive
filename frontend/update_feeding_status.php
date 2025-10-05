<?php
session_start();
require_once "../config.php";
date_default_timezone_set('Asia/Manila');
mysqli_query($link, "SET time_zone = '+08:00'");

$user_id = $_SESSION['user_id'] ?? null; // logged-in user
if (!$user_id) {
    http_response_code(400);
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$now = date('Y-m-d H:i:s');
$interval_minutes = 30; // or pull from database if dynamic
$next_feed = date('Y-m-d H:i:s', strtotime("+$interval_minutes minutes"));

// ✅ Always update the latest record (assuming single hive)
$sql = "UPDATE bee_feeding_schedule
        SET last_fed = '$now',
            next_feed = '$next_feed',
            fed_by_user_id = '$user_id',
            fed_at = '$now'
        WHERE id = (SELECT id FROM bee_feeding_schedule ORDER BY id DESC LIMIT 1)";

if (mysqli_query($link, $sql)) {
    echo json_encode(["status" => "success", "message" => "Feeding updated"]);
} else {
    echo json_encode(["status" => "error", "message" => mysqli_error($link)]);
}

mysqli_close($link);
?>
