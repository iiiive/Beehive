<?php
session_start();
include("../config.php");

$token = $_GET['token'] ?? '';
$hashedToken = $token ? hash('sha256', $token) : '';
$message = "";
$messageClass = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm) {
        $message = "Passwords do not match.";
        $messageClass = "error";
    } else {
        $newPass = password_hash($password, PASSWORD_DEFAULT);

        $sql = "UPDATE users 
                SET password_hash = ?, reset_token = NULL, reset_expires = NULL 
                WHERE reset_token = ? AND reset_expires > NOW()";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("ss", $newPass, $hashedToken);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $message = "Password has been reset successfully! Redirecting to login...";
            $messageClass = "success";
            echo "<script>setTimeout(() => window.location='user-login.php', 3000);</script>";
        } else {
            $message = "Invalid or expired token.";
            $messageClass = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Reset Password - HiveCare</title>
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
</style>
</head>
<body>
<div class="container">
  <h2>Reset Password</h2>
  <?php if (empty($message)) { ?>
  <form method="POST">
    <input type="password" name="password" placeholder="Enter new password" minlength="8" required>
    <input type="password" name="confirm_password" placeholder="Confirm new password" minlength="8" required>
    <button type="submit">Reset Password</button>
  </form>
  <?php } ?>
  <?php if (!empty($message)) echo "<p class='message $messageClass'>$message</p>"; ?>
</div>
</body>
</html>
