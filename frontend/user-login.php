<?php
// You can add PHP logic here later if needed (e.g., login authentication)
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HiveCare Login</title>
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
      margin: 0;
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
      height: 550px;
      width: 360px;  
      box-shadow: 0px 0px 24px #ceae1fff;
      border-radius: 20px;
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(15px);
      -webkit-backdrop-filter: blur(15px);
      border: 1px solid rgba(255,255,255,0.2);
    }

    .screen__content {
      position: relative;  
      height: 100%;
      padding: 40px 20px;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .login-header {
      text-align: center;
      margin-bottom: 40px;
    }

    .login-header img {
      width: 100px;
      height: auto;
      display: block;
      margin: 0 auto 10px;
    }

    .login-header h1 {
      color: #e7d25bff;
      font-size: 3rem;
      font-family: 'Brush Script MT', cursive;
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
      padding: 10px;
      padding-left: 24px;
      font-weight: 700;
      width: 100%;
      transition: .2s;
      color: #fff;
    }

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
  margin-left: 80px;
  margin-top: 20px;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
}
.login__submit::before {
  content: "";
  position: absolute;
  top: 50%;
  left: 50%;
  height: 0;
  width: 0;
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
  width: 100%;
  top: 0;
  left: 0;
  height: 100%;
}
.login__submit:active {
  transform: scale(0.8);
}
    /* Forgot & Back Links */
    .extra-links {
      margin-top: 15px;
      text-align: center;
      font-size: 0.9rem;
    }

    .extra-links a {
      color: #e7d25bff;
      text-decoration: none;
      display: block;
      margin: 5px 0;
    }
    .extra-links a:hover {
  color: #fff;
}

.extra-links a:first-child {
  text-decoration: underline;
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

      <!-- Login Form -->
      <form class="login" method="POST" action="user-dashboard.php">
        <div class="login__field">
          <i class="login__icon fas fa-user"></i>
          <input type="text" name="username" class="login__input" placeholder="User name / Email">
        </div>
        <div class="login__field">
          <i class="login__icon fas fa-lock"></i>
          <input type="password" name="password" class="login__input" placeholder="Password">
        </div>

        <button type="submit" class="login__submit">
          <span class="button__text">Log In Now</span>
          <i class="button__icon fas fa-chevron-right"></i>
        </button>
      </form>

      <!-- Extra Links -->
      <div class="extra-links">
        <a href="user-forgotpassword.php">Forgot Password?</a>
        <a href="homepage.php"><i class="fas fa-arrow-left"></i> Back to Homepage</a>
      </div>

    </div>
  </div>
</div>

</body>
</html>
