<?php
require_once "../config.php";
session_start();
date_default_timezone_set('Asia/Manila');

$user_id = $_SESSION['user_id'] ?? 1;

// ✅ Fetch the *latest* feeding record for this user
$sql = "
    SELECT u.username, f.last_fed, f.next_feed
    FROM bee_feeding_schedule f
    JOIN users u ON f.fed_by_user_id = u.user_id
    WHERE f.user_id = $user_id
    ORDER BY f.id DESC
    LIMIT 1
";

$result = mysqli_query($link, $sql);
$data = [];

if ($row = mysqli_fetch_assoc($result)) {
    $data = $row;
}

mysqli_close($link);

header('Content-Type: application/json');
echo json_encode($data);
?>
