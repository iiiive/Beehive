<?php
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
      margin: 0;
    }

    body::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
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
      height: 400px;
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
      width: 80px;
      height: auto;
      display: block;
      margin: 0 auto 10px;
    }

    .login-header h1 {
      color: #e7d25bff;
      font-size: 32px;
      font-family: 'Cursive', 'Brush Script MT', sans-serif;
      font-weight: 100;
    }

    .forgot {
      width: 100%;
    }

    .forgot__field {
      padding: 20px 0px;  
      position: relative;  
    }

    .forgot__icon {
      position: absolute;
      top: 30px;
      color: #e7d25bff;
    }

    .forgot__input {
      border: none;
      border-bottom: 2px solid #D1D1D4;
      background: none;
      padding: 10px;
      padding-left: 24px;
      font-weight: 700;
      width: 100%;
      transition: .2s;
      color: #fff;
    }

    .forgot__input:active,
    .forgot__input:focus,
    .forgot__input:hover {
      outline: none;
      border-bottom-color: #e7d25bff;
    }

    .forgot__submit {
      padding: 15px 25px;
      border: 0;
      border-radius: 15px;
      color: #6d611bff;
      z-index: 1;
      background: #e8e8e8;
      position: relative;
      font-weight: 1000;
      font-size: 17px;
      -webkit-box-shadow: 4px 8px 19px -3px rgba(0, 0, 0, 0.27);
      box-shadow: 4px 8px 19px -3px rgba(0, 0, 0, 0.27);
      transition: all 250ms;
      margin-left: 60px;
      margin-top: 20px;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .forgot__submit::before {
      content: "";
      position: absolute;
      top: 50%;
      left: 50%;
      height: 0;
      width: 0;
      border-radius: 15px;
      background-color: #e7d25bff;
      z-index: -1;
      transition: all 250ms;
    }

    .forgot__submit:hover {
      color: #e8e8e8;
    }

    .forgot__submit:hover::before {
      width: 100%;
      top: 0;
      left: 0;
      height: 100%;
    }

    .forgot__submit:active {
      transform: scale(0.8);
    }

    .back-link {
      margin-top: 15px;
      font-size: 0.9rem;
    }

    .back-link a {
      color: #e7d25bff;
      text-decoration: none;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="screen">
    <div class="screen__content">
      
      <div class="login-header">
        <img src="images/bee.png" alt="Bee Logo">
        <h1>HiveCare</h1>
      </div>

      <form class="forgot" method="POST" action="send_reset.php">
        <div class="forgot__field">
          <i class="forgot__icon fas fa-envelope"></i>
          <input type="email" name="email" class="forgot__input" placeholder="Enter your email" required>
        </div>
        <button type="submit" class="forgot__submit">
          <span>Send Reset Code</span>
          <i class="fas fa-paper-plane" style="margin-left:8px;"></i>
        </button>
      </form>

      <div class="back-link">
        <a href="admin-login.php"><i class="fas fa-arrow-left"></i> Back to Login</a>
      </div>

    </div>
  </div>
</div>

</body>
</html>
