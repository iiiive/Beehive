<?php
session_start();
include("../config.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // make sure Composer installed PHPMailer

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);

    // Check if user exists
    $sql = "SELECT user_id FROM users WHERE email = ? LIMIT 1";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $token = bin2hex(random_bytes(16));
        $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

        $update = "UPDATE users SET reset_token = ?, reset_expires = ? WHERE user_id = ?";
        $updStmt = $link->prepare($update);
        $updStmt->bind_param("ssi", $token, $expires, $row['user_id']);
        $updStmt->execute();

        $resetLink = "http://yourdomain.com/reset-password.php?token=$token";

        // === PHPMailer instead of mail() ===
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'yourgmail@gmail.com';   // your Gmail
            $mail->Password   = 'your-app-password';     // Gmail App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('yourgmail@gmail.com', 'HiveCare');
            $mail->addAddress($email);
            $mail->Subject = 'HiveCare Password Reset';
            $mail->Body    = "Click this link to reset your password: $resetLink";

            $mail->send();
            $message = "A password reset link has been sent to your email.";
        } catch (Exception $e) {
            $message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $message = "If this email exists, a reset link has been sent.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Forgot Password - HiveCare</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css?family=Raleway:400,700" rel="stylesheet">
<style>
body {
  height: 100vh; display: flex; align-items: center; justify-content: center;
  font-family: Raleway, sans-serif;
  background: url('images/beehive.jpeg') no-repeat center center/cover;
  position: relative; margin: 0;
}
body::before { content: ""; position: absolute; inset: 0; background: rgba(0,0,0,0.7); }
.container {
  position: relative; z-index: 1;
  background: rgba(255,255,255,0.1); backdrop-filter: blur(15px);
  border-radius: 20px; padding: 40px; width: 360px;
  box-shadow: 0 0 24px #ceae1fff; text-align: center;
}
input { width: 100%; padding: 12px; margin: 12px 0; border-radius: 10px;
  border: none; background: rgba(255,255,255,0.2); color: #fff; }
input::placeholder { color: #eee; }
button { width: 100%; padding: 14px; border: none; border-radius: 12px;
  background: #e7d25b; color: #6d611b; font-weight: bold; cursor: pointer; transition: 0.3s; }
button:hover { background: #cdbd49; color: #000; }
.message { margin-top: 15px; font-weight: bold; }
.message.success { color: lightgreen; }
.message.error { color: #ff8080; }
.info-text { font-size: 12px; color: #ccc; margin-top: 8px; }
</style>
</head>
<body>
<div class="container">
  <h2>Forgot Password</h2>
  <?php if (empty($message)) { ?>
  <form method="POST">
    <input type="email" name="email" placeholder="Enter your email" required>
    <button type="submit">Send Reset Link</button>
  </form>
  <p class="info-text">You'll receive a reset link if this email is registered.</p>
  <?php } ?>
  <?php if (!empty($message)) echo "<p class='message $messageClass'>$message</p>"; ?>
</div>
</body>
</html>
