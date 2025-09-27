<?php
require_once "../config.php";

$sql = "SELECT timestamp, temperature, humidity, weight, status
        FROM beehive_readings
        ORDER BY timestamp DESC
        LIMIT 5";
$result = mysqli_query($link, $sql);

$rows = [];
while ($r = mysqli_fetch_assoc($result)) {
    $rows[] = $r;
}

header('Content-Type: application/json');
echo json_encode($rows);

mysqli_close($link);
