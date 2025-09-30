<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'beemonitoring');

// Create connection
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

if (isset($_POST['temp']) && isset($_POST['hum']) && isset($_POST['weight'])) {
    $temp   = $_POST['temp'];
    $hum    = $_POST['hum'];
    $weight = $_POST['weight'];

    // Decide fan_status automatically (example: ON if temp > 32)
    $fan_status = ($temp > 32) ? 1 : 0;

    // Insert without status (it will stay NULL by default)
    $sql = "INSERT INTO beehive_readings (temperature, humidity, weight, fan_status) 
            VALUES (?, ?, ?, ?)";
    if ($stmt = mysqli_prepare($link, $sql)) {
        // i = int, d = double/float
        mysqli_stmt_bind_param($stmt, "iidi", $temp, $hum, $weight, $fan_status);

        if (mysqli_stmt_execute($stmt)) {
            echo "Data saved successfully";
        } else {
            echo "Error inserting data: " . mysqli_error($link);
        }
        mysqli_stmt_close($stmt);
    }
} else {
    echo "Error: Missing data (temp, hum, weight)";
}

mysqli_close($link);
?>
