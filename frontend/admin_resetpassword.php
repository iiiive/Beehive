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
    $check = $link->prepare("SELECT admin_id FROM admins WHERE reset_token = ? AND reset_expires > NOW()");
    $check->bind_param("s", $token);
    $check->execute();
    $res = $check->get_result();
    if ($row = $res->fetch_assoc()) {
        $showForm = true;
        $adminId = $row['admin_id'];
    } else {
        $message = "Invalid or expired token.";
        $messageClass = "error";
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && $showForm) {
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if (strlen($password) < 8) {
        $message = "Password must be at least 8 characters long.";
        $messageClass = "error";
    } elseif ($password !== $confirm) {
        $message = "Passwords do not match.";
        $messageClass = "error";
    } else {
        $newPass = password_hash($password, PASSWORD_DEFAULT);
        $update = $link->prepare("UPDATE admins SET password_hash = ?, reset_token = NULL, reset_expires = NULL WHERE admin_id = ?");
        $update->bind_param("si", $newPass, $adminId);
        $update->execute();

        $message = "Password has been reset successfully! Redirecting to login...";
        $messageClass = "success";
        $showForm = false;
        echo "<script>setTimeout(() => window.location='admin-login.php', 3000);</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Reset Password - HiveCare</title>
<link href="https://fonts.googleapis.com/css?family=Raleway:400,700" rel="stylesheet">
<style>
body {
  height: 100vh; display: flex; align-items: center; justify-content: center;
  font-family: Raleway, sans-serif;
  background: url('images/beehive.jpeg') no-repeat center center/cover;
  position: relative; margin: 0;
}
h2 { color: #e7d25bff; font-family: 'Cursive','Brush Script MT',sans-serif; font-size: 3rem; font-weight: 100; }

body::before { content: ""; position: absolute; inset: 0; background: rgba(0,0,0,0.7); }
.container {
  position: relative; z-index: 1;
  background: rgba(255,255,255,0.1); backdrop-filter: blur(15px);
  border-radius: 20px; padding: 40px; width: 360px;
  box-shadow: 0 0 24px #ceae1fff; text-align: center;
}
.input-wrapper {
  position: relative;
  margin: 12px 0;
}
input { width: 100%; padding: 12px; border-radius: 10px;
  border: none; background: rgba(255,255,255,0.2); color: #fff; }
input::placeholder { color: #eee; }
.eye-icon {
  position: absolute; top: 50%; right: 12px; transform: translateY(-50%);
  cursor: pointer; color: #fff; font-size: 18px;
}
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
  <h2>Admin Reset Password</h2>
  <?php if ($showForm) { ?>
  <form method="POST">
    <div class="input-wrapper">
      <input type="password" id="password" name="password" placeholder="Enter new password" minlength="8" required>
      <span class="eye-icon" onclick="togglePassword('password')">&#128065;</span>
    </div>
    <div class="input-wrapper">
      <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" minlength="8" required>
      <span class="eye-icon" onclick="togglePassword('confirm_password')">&#128065;</span>
    </div>
    <button type="submit">Reset Password</button>
  </form>
  <?php } ?>
  <?php if (!empty($message)) echo "<p class='message $messageClass'>$message</p>"; ?>
</div>

<script>
function togglePassword(id) {
  const input = document.getElementById(id);
  if (input.type === "password") {
    input.type = "text";
  } else {
    input.type = "password";
  }
}
</script>
</body>
</html>
