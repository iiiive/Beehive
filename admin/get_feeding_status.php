<?php
require_once "../config.php";
date_default_timezone_set('Asia/Manila');
mysqli_query($link, "SET time_zone = '+08:00'");

$sql = "SELECT u.username, f.last_fed, f.next_feed
        FROM bee_feeding_schedule f
        JOIN users u ON f.fed_by_user_id = u.user_id
        ORDER BY f.id DESC LIMIT 1"; // assuming only 1 hive

$result = mysqli_query($link, $sql);
$data = [];

if ($row = mysqli_fetch_assoc($result)) {
    $data = $row;
}

mysqli_close($link);

header('Content-Type: application/json');
echo json_encode($data);
?>
