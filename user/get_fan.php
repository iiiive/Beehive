<?php
require_once "../config.php";

$sql = "SELECT fan_status FROM beehive_readings ORDER BY timestamp DESC LIMIT 1";
$result = mysqli_query($link, $sql);

if ($row = mysqli_fetch_assoc($result)) {
    echo json_encode(["fan_status" => (int)$row['fan_status']]);
} else {
    echo json_encode(["fan_status" => 0]);
}

mysqli_close($link);
?>
