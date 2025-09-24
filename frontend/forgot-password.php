<?php
session_start();
include("../config.php");

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
        
        $resetLink = "http://yourdomain.com/reset_password.php?token=$token";
        mail($email, "HiveCare Password Reset", "Click this link to reset your password: $resetLink");
        
        $message = "A password reset link has been sent to your email.";
    } else {
        $message = "If this email exists, a reset link has been sent."; // generic message
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
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
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
input {
  width: 100%;
  padding: 12px;
  margin: 12px 0;
  border-radius: 10px;
  border: none;
  background: rgba(255,255,255,0.2);
  color: #fff;
}
button {
  width: 100%;
  padding: 14px;
  border: none;
  border-radius: 12px;
  background: #e7d25b;
  color: #6d611b;
  font-weight: bold;
  cursor: pointer;
  transition: 0.3s;
}
button:hover { background: #cdbd49; color: #000; }
.message { color: lightgreen; margin-top: 15px; font-weight: bold; }
</style>
</head>
<body>
<div class="container">
  <h2>Forgot Password</h2>
  <form method="POST">
    <input type="email" name="email" placeholder="Enter your email" required>
    <button type="submit">Send Reset Link</button>
  </form>
  <?php if (!empty($message)) echo "<p class='message'>$message</p>"; ?>
</div>
</body>
</html>
