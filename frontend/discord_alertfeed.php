<?php
// === discord_alert.php ===
function sendDiscordAlert($message) {
    $webhookURL = "https://discord.com/api/webhooks/1423648258718175353/KSxyHb61KFaDh8F1o-gTBXyOZ3vMkJYm8jWWoYSD7lSDTwrmALqUx045moHLhGnGiMQb"; // 🔁 Replace with your webhook URL

    $data = json_encode(["content" => $message], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    $ch = curl_init($webhookURL);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
}
?>
