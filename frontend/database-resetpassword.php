<?php
session_start();
include("../config.php");

// Set PHP timezone
date_default_timezone_set('Asia/Manila');

$token = $_GET['token'] ?? '';
$message = "";
$messageClass = "";
$showForm = false;

// Check if token exists and is not expired
if (!empty($token)) {
    $check = $link->prepare("SELECT db_id FROM db_access WHERE reset_token = ? AND reset_expires > NOW()");
    $check->bind_param("s", $token);
    $check->execute();
    $res = $check->get_result();
    if ($row = $res->fetch_assoc()) {
        $showForm = true;
        $dbId = $row['db_id'];
    } else {
        $message = "Invalid or expired token.";
        $messageClass = "error";
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && $showForm) {
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm) {
        $message = "Passwords do not match.";
        $messageClass = "error";
    } else {
        $newPass = password_hash($password, PASSWORD_DEFAULT);
        $update = $link->prepare("UPDATE db_access SET password_hash = ?, reset_token = NULL, reset_expires = NULL WHERE db_id = ?");
        $update->bind_param("si", $newPass, $dbId);
        $update->execute();

        $message = "Password has been reset successfully! Redirecting to login...";
        $messageClass = "success";
        $showForm = false;
        echo "<script>setTimeout(() => window.location='database_access.php', 3000);</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>DB User Reset Password - HiveCare</title>
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
h2 { color: #fff; margin-bottom: 20px; }
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
  <h2>DB User Reset Password</h2>
  <?php if ($showForm) { ?>
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
