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

if (isset($_POST['temp']) && isset($_POST['hum']) && isset($_POST['weight']) && isset($_POST['fan_status'])) {
    $temp       = $_POST['temp'];
    $hum        = $_POST['hum'];
    $weight     = $_POST['weight'];
    $fan_status = $_POST['fan_status']; // 0 or 1 from Arduino

    // Insert data into database
    $sql = "INSERT INTO beehive_readings (temperature, humidity, weight, fan_status) 
            VALUES (?, ?, ?, ?)";
    if ($stmt = mysqli_prepare($link, $sql)) {
        // d = double/float, i = int
        mysqli_stmt_bind_param($stmt, "dddi", $temp, $hum, $weight, $fan_status);

        if (mysqli_stmt_execute($stmt)) {
            echo "Data saved successfully";
        } else {
            echo "Error inserting data: " . mysqli_error($link);
        }
        mysqli_stmt_close($stmt);
    }
} else {
    echo "Error: Missing data (temp, hum, weight, fan_status)";
}

mysqli_close($link);
?>
