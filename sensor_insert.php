<?php
require_once "config.php"; // adjust path

$temperature = $_POST['temperature'];
$humidity    = $_POST['humidity'];
$weight      = $_POST['weight'];
$fan_status  = $_POST['fan_status'];

$sql = "INSERT INTO beehive_readings (temperature, humidity, weight, fan_status)
        VALUES ('$temperature','$humidity','$weight','$fan_status')";

if (mysqli_query($link, $sql)) {
    echo "Data inserted successfully";
} else {
    echo "Error: " . mysqli_error($link);
}
?>
