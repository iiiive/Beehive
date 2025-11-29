<?php
require_once "../config.php"; // adjust path

header('Content-Type: application/json');

$sql = "SELECT timestamp, temperature, humidity, weight, fan_status, status
        FROM beehive_readings
        ORDER BY timestamp DESC
        LIMIT 1";
$result = mysqli_query($link, $sql);

if ($result && $row = mysqli_fetch_assoc($result)) {
    // send as JSON (numeric values preserved)
    echo json_encode([
      'timestamp'   => $row['timestamp'],
      'temperature' => $row['temperature'],
      'humidity'    => $row['humidity'],
      'weight'      => $row['weight'],
      'fan_status'  => (int)$row['fan_status'],
      'status'      => $row['status']
    ]);
} else {
    echo json_encode([]);
}
mysqli_close($link);
