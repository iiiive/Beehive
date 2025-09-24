<?php
session_start();
include("../config.php");

$token = $_GET['token'] ?? '';
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $newPass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "UPDATE users SET password_hash = ?, reset_token = NULL, reset_expires = NULL 
            WHERE reset_token = ? AND reset_expires > NOW()";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("ss", $newPass, $token);
    if ($stmt->execute()) {
        $message = "Password has been reset successfully! You can now <a href='user-login.php'>login</a>.";
    } else {
        $message = "Invalid or expired token.";
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
  filter: brightness(20%);
}
.container {
  background: rgba(255,255,255,0.1);
  backdrop-filter: blur(15px);
  border-radius: 20px;
  padding: 40px;
  width: 360px;
  box-shadow: 0 0 24px #ceae1fff;
  text-align: center;
}
input { width: 100%; padding: 12px; margin: 12px 0; border-radius: 10px; border: none; background: rgba(255,255,255,0.2); color: #fff; }
button { width: 100%; padding: 14px; border: none; border-radius: 12px; background: #e7d25b; color: #6d611b; font-weight: bold; cursor: pointer; transition: 0.3s; }
button:hover { background: #cdbd49; color: #000; }
.message { color: lightgreen; margin-top: 15px; font-weight: bold; }
</style>
</head>
<body>
<div class="container">
  <h2>Reset Password</h2>
  <?php if (empty($message)) { ?>
  <form method="POST">
    <input type="password" name="password" placeholder="Enter new password" required>
    <button type="submit">Reset Password</button>
  </form>
  <?php } ?>
  <?php if (!empty($message)) echo "<p class='message'>$message</p>"; ?>
</div>
</body>
</html>
