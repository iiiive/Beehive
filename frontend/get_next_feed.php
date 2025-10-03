<?php
require_once "../config.php";
session_start();
$user_id = $_SESSION['user_id'] ?? 1;

$sql = "SELECT next_feed FROM bee_feeding_schedule WHERE user_id=$user_id LIMIT 1";
$res = mysqli_query($link, $sql);
$row = mysqli_fetch_assoc($res);

header('Content-Type: application/json');
echo json_encode([
    'next_feed' => $row['next_feed'] ?? null
]);

mysqli_close($link);
