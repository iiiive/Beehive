<?php
// Include your database config
require_once "config.php";

// Discord Webhook URL
$webhookurl = "https://discord.com/api/webhooks/1416997260431982674/H2otdDl8uB6uXYdbaAfSS8HqquYhgkjz2eNe58jaaZybra5V4H3i1M2pPYBKf5H7t6JD";

// Get latest reading
$sql = "SELECT timestamp, temperature, humidity, weight, fan_status, status 
        FROM beehive_readings 
        ORDER BY timestamp DESC 
        LIMIT 1";
$result = mysqli_query($link, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $timestamp = $row['timestamp'];
    $temperature = $row['temperature'];
    $humidity = $row['humidity'];
    $weight = $row['weight'];
    $fan_status = $row['fan_status'];
    $status = $row['status']; // <-- fixed missing semicolon

    // Prepare alert messages
    $alerts = [];

    if ($temperature > 35) {
        $alerts[] = "🔥 Hive too hot! Temperature: {$temperature}°C at {$timestamp}";
    } elseif ($temperature < 20) {
        $alerts[] = "❄️ Hive too cold! Temperature: {$temperature}°C at {$timestamp}";
    }

    if ($humidity > 80) {
        $alerts[] = "💧 Humidity too high! Humidity: {$humidity}% at {$timestamp}";
    }

    // Optional weight alert
    // $prev_sql = "SELECT weight FROM beehive_readings ORDER BY timestamp DESC LIMIT 1,1";
    // $prev_result = mysqli_query($link, $prev_sql);
    // if ($prev_result && mysqli_num_rows($prev_result) > 0) {
    //     $prev_weight = mysqli_fetch_assoc($prev_result)['weight'];
    //     if (($prev_weight - $weight) > 2) {
    //         $alerts[] = "⚠️ Sudden weight drop detected! Previous: {$prev_weight}kg, Now: {$weight}kg at {$timestamp}";
    //     }
    // }

    // Send alerts to Discord
    foreach ($alerts as $alert) {
        $json_data = json_encode(["content" => $alert]);

        $ch = curl_init($webhookurl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        curl_close($ch);
    }
} else {
    echo "No readings found.";
}

mysqli_close($link);
?>
