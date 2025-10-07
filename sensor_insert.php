<?php
require_once "config.php"; // Database connection

// === 1. Get POST data ===
$temperature = $_POST['temperature'];
$humidity    = $_POST['humidity'];
$weight      = $_POST['weight'];
$fan_status  = $_POST['fan_status'];

// === 2. Insert into database ===
$sql_insert = "INSERT INTO beehive_readings (temperature, humidity, weight, fan_status)
               VALUES ('$temperature', '$humidity', '$weight', '$fan_status')";

if (mysqli_query($link, $sql_insert)) {
    echo "Data inserted successfully<br>";

    // === 3. Get the latest inserted record ===
    $sql_latest = "SELECT timestamp, temperature, humidity, weight, fan_status 
                   FROM beehive_readings 
                   ORDER BY timestamp DESC 
                   LIMIT 1";
    $result = mysqli_query($link, $sql_latest);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        $timestamp   = $row['timestamp'];
        $temperature = $row['temperature'];
        $humidity    = $row['humidity'];
        $weight      = $row['weight'];
        $fan_status  = $row['fan_status'];

        // === 4. Prepare alert messages ===
        $alerts = [];

        if ($temperature > 25.90) {
            $alerts[] = "🔥 **Hive too hot!** Temperature: {$temperature}°C at {$timestamp}";
            $alerts[] = "🌀 Fan is ON! at {$timestamp}";

        } elseif ($temperature < 22.30) {
            $alerts[] = "❄️ **Hive too cold!** Temperature: {$temperature}°C at {$timestamp}";
        }

        if ($humidity > 86.40) {
            $alerts[] = "💧 **Humidity too high!** Humidity: {$humidity}% at {$timestamp}";
        } elseif ($humidity < 79.20) {
            $alerts[] = "💧 **Humidity too low!** Humidity: {$humidity}% at {$timestamp}";
        }

        if ($weight >= 5) {
            $alerts[] = "⚠️ **Beehive too heavy!** Possible harvest or swarm. Weight: {$weight}kg at {$timestamp}";
        } elseif ($weight < 2) {
            $alerts[] = "⚠️ **Beehive too light!** Possible hive loss. Weight: {$weight}kg at {$timestamp}";
        }

        // === 5. Send alerts to Discord ===
        if (!empty($alerts)) {
            $webhookurl = "https://discord.com/api/webhooks/1416997260431982674/H2otdDl8uB6uXYdbaAfSS8HqquYhgkjz2eNe58jaaZybra5V4H3i1M2pPYBKf5H7t6JD";

            foreach ($alerts as $alert) {
                $json_data = json_encode(["content" => $alert]);

                $ch = curl_init($webhookurl);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_exec($ch);
                curl_close($ch);
            }

            echo "Alerts sent to Discord.";
        } else {
            echo "No alert conditions triggered.";
        }
    } else {
        echo "Error fetching latest record.";
    }
} else {
    echo "Error inserting data: " . mysqli_error($link);
}

mysqli_close($link);
?>
