<?php
require_once "../config.php";

// Discord webhook URL
$webhookUrl = "https://discord.com/api/webhooks/1423648258718175353/KSxyHb61KFaDh8F1o-gTBXyOZ3vMkJYm8jWWoYSD7lSDTwrmALqUx045moHLhGnGiMQb";

$message = $_POST['message'] ?? "🟢 It's time to feed the bees!";

$payload = json_encode(["content" => $message]);

$ch = curl_init($webhookUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$curlErr  = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

header('Content-Type: application/json');

if ($response === false || $httpCode < 200 || $httpCode >= 300) {
    echo json_encode([
        "success" => false,
        "http_code" => $httpCode,
        "curl_error" => $curlErr,
        "response" => $response
    ]);
    exit;
}

echo json_encode([
    "success" => true,
    "http_code" => $httpCode,
    "response" => $response
]);
