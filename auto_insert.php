<?php
// config.php must contain your DB connection
require_once "config.php";

// Example: generate random values (replace with sensor data if available)
$temperature = rand(30, 40);   // °C
$humidity    = rand(60, 90);   // %
$weight      = rand(1, 20);    // kg
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
    $timestamp   = $row['timestamp'];
    $temperature = $row['temperature'];
    $humidity    = $row['humidity'];
    $weight      = $row['weight'];
    $fan_status  = $row['fan_status'];
    $status      = $row['status']; 

    // Prepare alert messages
    $alerts = [];

    if ($temperature > 32) {
        $alerts[] = "🔥 Hive too hot! Fan turned On! Please put the hive on a shade. Temperature: {$temperature}°C at {$timestamp}";
    } elseif ($temperature < 28) {
        $alerts[] = "❄️ Hive too cold! Temperature: {$temperature}°C at {$timestamp}";
    }

    if ($humidity > 80) {
        $alerts[] = "💧 Humidity too high! Humidity: {$humidity}% at {$timestamp}";
    } elseif ($humidity < 28) {
        $alerts[] = "💧 Humidity too low! Please provide water source for the Bees! Humidity: {$humidity}% at {$timestamp}";
    }

    if ($weight > 5) {
        $alerts[] = "⚠️ Beehive is too Heavy! Check for Potential Swarming/Harvest. Weight: {$weight}Kg at {$timestamp}";
    } elseif ($weight < 2) {
        $alerts[] = "⚠️ Beehive weight is critically low! Please check for potential hive loss. Weight: {$weight}Kg at {$timestamp}";
    }

    // ✅ Add fan status
    $fan_text = ($fan_status == 1) ? "🌀 Fan is ON" : "🛑 Fan is OFF";
    $alerts[] = "{$fan_text} at {$timestamp}";

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
