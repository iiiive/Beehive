<?php
require_once "../config.php";

$sql = "SELECT timestamp, temperature, humidity, weight, status
        FROM beehive_readings
        ORDER BY timestamp DESC
        LIMIT 1";
$result = mysqli_query($link, $sql);
$data = mysqli_fetch_assoc($result);

header('Content-Type: application/json');
echo json_encode($data);

mysqli_close($link);
