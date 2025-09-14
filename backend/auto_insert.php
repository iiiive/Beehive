<?php
// config.php must contain your DB connection
require_once "config.php";

// Example: generate random values (replace with sensor data if available)
$temperature = rand(30, 40);   // °C
$humidity    = rand(60, 90);   // %
$weight      = rand(1, 20); // grams
$fan_status  = rand(0, 1);     // 0 = off, 1 = on

$sql = "INSERT INTO beehive_readings (timestamp, temperature, humidity, weight, fan_status) 
        VALUES (NOW(), ?, ?, ?, ?)";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "iiii", $temperature, $humidity, $weight, $fan_status);
    if (mysqli_stmt_execute($stmt)) {
        echo "Data inserted successfully\n";
    } else {
        echo "Error: Could not insert data\n";
    }
    mysqli_stmt_close($stmt);
}
mysqli_close($link);
?>
