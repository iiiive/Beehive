<?php
session_start();
include("../config.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!empty($email)) {
        // Example only: In production, generate token and send via email
        $_SESSION['reset_notice'] = "If this email exists in our system, a reset link has been sent.";
        header("Location: forgot-password.php");
        exit;
    } else {
        $error = "Please enter your email address.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HiveCare - Forgot Password</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css?family=Raleway:400,700" rel="stylesheet">
<style>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
  font-family: Raleway, sans-serif;
}

body {
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}
body::before {
  content: "";
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: url('images/beehive.jpeg') no-repeat center center/cover;
  filter: brightness(20%);
  z-index: -1;
}

.container {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
}

.screen {
  position: relative;
  height: 480px;
  width: 360px;
  box-shadow: 0px 0px 24px #ceae1fff;
  border-radius: 20px;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(15px);
  -webkit-backdrop-filter: blur(15px);
  border: 1px solid rgba(255,255,255,0.2);
}

.screen__content {
  z-index: 1;
  position: relative;
  height: 100%;
  padding: 40px 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.login-header {
  text-align: center;
  margin-bottom: 30px;
}
.login-header img {
  width: 100px;
  height: auto;
  margin: 0 auto 10px;
  display: block;
}
.login-header h1 {
  color: #e7d25bff;
  font-family: 'Cursive', 'Brush Script MT', sans-serif;
  font-size: 2.2rem;
  font-weight: 100;
}

.login {
  width: 100%;
}

.login__field {
  padding: 20px 0;
  position: relative;
}

.login__icon {
  position: absolute;
  top: 30px;
  color: #e7d25bff;
}

.login__input {
  border: none;
  border-bottom: 2px solid #D1D1D4;
  background: none;
  padding: 10px 10px 10px 24px;
  font-weight: 700;
  width: 100%;
  transition: .2s;
  color: #fff;
}
.login__input:active,
.login__input:focus,
.login__input:hover {
  outline: none;
  border-bottom-color: #e7d25bff;
}

.login__submit {
  padding: 15px 25px;
  border: 0;
  border-radius: 15px;
  color: #6d611bff;
  z-index: 1;
  background: #e8e8e8;
  position: relative;
  font-weight: 1000;
  font-size: 17px;
  box-shadow: 4px 8px 19px -3px rgba(0, 0, 0, 0.27);
  transition: all 250ms;
  margin-left: 65px;
  margin-top: 20px;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
}
.login__submit::before {
  content: "";
  position: absolute;
  top: 50%; left: 50%;
  height: 0; width: 0;
  border-radius: 15px;
  background-color: #e7d25bff;
  z-index: -1;
  box-shadow: 4px 8px 19px -3px rgba(0, 0, 0, 0.27);
  transition: all 250ms;
}
.login__submit:hover {
  color: #e8e8e8;
}
.login__submit:hover::before {
  width: 100%; top: 0; left: 0; height: 100%;
}
.login__submit:active {
  transform: scale(0.8);
}

.extra-links {
  margin-top: 20px;
  text-align: center;
}
.extra-links a {
  display: block;
  color: #FFD93D;
  text-decoration: underline;
  font-weight: bold;
  margin: 8px 0;
  transition: color 0.3s ease;
}
.extra-links a:hover {
  color: #fff;
}

.error {
  color: #ff5c5c;
  text-align: center;
  margin-top: 10px;
  font-size: 0.9rem;
}
.success {
  color: #66ff99;
  text-align: center;
  margin-top: 10px;
  font-size: 0.9rem;
}
</style>
</head>
<body>
<div class="container">
  <div class="screen">
    <div class="screen__content">
      <div class="login-header">
        <img src="images/bee.png" alt="Bee Logo">
        <h1>Reset Password</h1>
      </div>

      <form class="login" method="POST">
        <div class="login__field">
          <i class="login__icon fas fa-envelope"></i>
          <input type="email" name="email" class="login__input" placeholder="Enter your email" required>
        </div>
        <button type="submit" class="button login__submit">
          <span class="button__text">Send Reset Link</span>
          <i class="button__icon fas fa-paper-plane"></i>
        </button>
      </form>

      <div class="extra-links">
        <a href="user-login.php">Back to Login</a>
      </div>

      <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <?php if (!empty($_SESSION['reset_notice'])): ?>
        <div class="success"><?= htmlspecialchars($_SESSION['reset_notice']) ?></div>
        <?php unset($_SESSION['reset_notice']); ?>
      <?php endif; ?>
    </div>
  </div>
</div>
</body>
</html>
