<?php
session_start();
include("../config.php");

// ==== CONFIG ====
$message = "";
$messageClass = "";
$discordWebhook = "https://discord.com/api/webhooks/1420685985032831179/ew3Y7QIpekBBDnCjPe4VFBETbl0X03RXCePmSQmvjARI5AFjyBwBqYOnfmmRA76uILta"; // paste your webhook here
$localIP = "127.0.0.1"; // or your LAN IP for testing
$baseUrl = "http://localhost/thesis/Beehive/frontend"; 
// =================

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);

    // Check if user exists
    $sql = "SELECT user_id FROM users WHERE email = ? LIMIT 1";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $token = bin2hex(random_bytes(16)); // raw token
        $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

        $update = "UPDATE users SET reset_token = ?, reset_expires = ? WHERE user_id = ?";
        $updStmt = $link->prepare($update);
        $updStmt->bind_param("ssi", $token, $expires, $row['user_id']);
        $updStmt->execute();



        $resetLink = "$baseUrl/reset_password.php?token=$token";

        // Send to Discord
        $data = json_encode([
            "content" => "**Password Reset Request**\nEmail: $email\nReset Link: $resetLink"
        ]);
        $ch = curl_init($discordWebhook);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);

        // Optional: show link in browser for local testing
        $message = "Reset link sent! <a href='$resetLink' target='_blank'>Click here to test</a>";
        $messageClass = "success";

    } else {
        $message = "If this email exists, a reset link has been sent.";
        $messageClass = "success";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Forgot Password - HiveCare</title>
<link href="https://fonts.googleapis.com/css?family=Raleway:400,700" rel="stylesheet">
<style>
body { height: 100vh; display:flex; align-items:center; justify-content:center; font-family:Raleway, sans-serif; 
       background: url('images/beehive.jpeg') no-repeat center center/cover; margin:0; position:relative; }
body::before { content:""; position:absolute; inset:0; background: rgba(0,0,0,0.7); }
.container { position:relative; z-index:1; background: rgba(255,255,255,0.1); backdrop-filter: blur(15px);
            border-radius:20px; padding:40px; width:360px; box-shadow:0 0 24px #ceae1fff; text-align:center; }
input { width:100%; padding:12px; margin:12px 0; border-radius:10px; border:none; background: rgba(255,255,255,0.2); color:#fff; }
input::placeholder { color:#eee; }
button { width:100%; padding:14px; border:none; border-radius:12px; background:#e7d25b; color:#6d611b; font-weight:bold; cursor:pointer; transition:0.3s; }
button:hover { background:#cdbd49; color:#000; }
.message { margin-top:15px; font-weight:bold; }
.message.success { color: lightgreen; }
.message.error { color:#ff8080; }
</style>
</head>
<body>
<div class="container">
<h2>Forgot Password</h2>
<form method="POST">
  <input type="email" name="email" placeholder="Enter your email" required>
  <button type="submit">Send Reset Link</button>
</form>
<?php if(!empty($message)) echo "<p class='message $messageClass'>$message</p>"; ?>
</div>
</body>
</html>
