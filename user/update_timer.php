<?php
session_start();
require_once "../config.php";

if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_logged_in'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

$state = $_POST['state'] ?? null;

$allowed = ['running', 'paused', 'stopped'];
if (!in_array($state, $allowed)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid state"]);
    exit;
}

// ✅ Update ONLY the latest schedule row
$sql = "
  UPDATE bee_feeding_schedule
  SET timer_state = ?
  ORDER BY id DESC
  LIMIT 1
";

$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "s", $state);
$ok = mysqli_stmt_execute($stmt);

echo json_encode([
    "success" => $ok,
    "state"   => $state
]);
