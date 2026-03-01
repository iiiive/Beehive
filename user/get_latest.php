<?php
require_once "../config.php";

header('Content-Type: application/json');

// Get the latest reading including heater & mist
$sql = "
    SELECT 
        timestamp, 
        temperature, 
        humidity, 
        weight, 
        fan_status,
        COALESCE(heater_status, 0) AS heater_status,
        COALESCE(mist_status, 0)   AS mist_status,
        status
    FROM beehive_reading
    ORDER BY timestamp DESC
    LIMIT 1
";

$result = mysqli_query($link, $sql);

if ($result && $row = mysqli_fetch_assoc($result)) {
    echo json_encode([
        'timestamp'      => $row['timestamp'],
        'temperature'    => (float)$row['temperature'],
        'humidity'       => (float)$row['humidity'],
        'weight'         => (float)$row['weight'],
        'fan_status'     => (int)$row['fan_status'],
        'heater_status'  => (int)$row['heater_status'],
        'mist_status'    => (int)$row['mist_status'],
        'status'         => $row['status']
    ]);
} else {
    // Fallback if no data yet
    echo json_encode([
        'timestamp'      => null,
        'temperature'    => 0,
        'humidity'       => 0,
        'weight'         => 0,
        'fan_status'     => 0,
        'heater_status'  => 0,
        'mist_status'    => 0,
        'status'         => 'No data'
    ]);
}

mysqli_close($link);
