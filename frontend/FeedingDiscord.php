<?php
require_once "../config.php";

// Discord webhook URL
$webhookUrl = "https://discord.com/api/webhooks/1423648258718175353/KSxyHb61KFaDh8F1o-gTBXyOZ3vMkJYm8jWWoYSD7lSDTwrmALqUx045moHLhGnGiMQb";

// Get optional message from POST
$message = $_POST['message'] ?? "🟢 It's time to feed the bees!";

// Prepare payload
$payload = json_encode([
    "content" => $message
]);

// Send webhook
$ch = curl_init($webhookUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

// Return response
header('Content-Type: application/json');
echo json_encode(['success' => true, 'response' => $response]);
