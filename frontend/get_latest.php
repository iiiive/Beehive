<?php
require_once "../config.php";

// Query the very latest row
$sql = "SELECT timestamp, temperature, humidity, weight, fan_status, status 
        FROM beehive_readings 
        ORDER BY timestamp DESC 
        LIMIT 1";
$result = mysqli_query($link, $sql);
$row = mysqli_fetch_assoc($result);

mysqli_close($link);

// Return JSON
header('Content-Type: application/json');
echo json_encode($row);
?>
