<?php
// Discord Webhook URL
$webhookurl = "https://discord.com/api/webhooks/1416997260431982674/H2otdDl8uB6uXYdbaAfSS8HqquYhgkjz2eNe58jaaZybra5V4H3i1M2pPYBKf5H7t6JD"; // Replace with your webhook

// Test message
$message = "✅ Test alert from Beehive system! Everything is working.";

// Send alert to Discord
$json_data = json_encode(["content" => $message]);

$ch = curl_init($webhookurl);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec($ch);
curl_close($ch);

echo "Test alert sent!";
?>
