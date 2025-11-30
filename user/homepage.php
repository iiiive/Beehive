<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>HiveCare</title>
  <!-- Montserrat Font -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Montserrat', sans-serif;
      background: url('../frontend/images/background.jpg') no-repeat center center fixed;
      background-size: cover;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      position: relative;
      overflow: hidden;
    }

    /* Overlay */
    body::before {
      content: '';
      position: absolute;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.5);
      z-index: 0;
    }

    .container {
      position: relative;
      z-index: 1;
      text-align: center;
      color: #fff;
      padding: 50px 30px;
      background: rgba(255,255,255,0.1);
      border-radius: 20px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.5);
      backdrop-filter: blur(10px);
      animation: fadeInUp 1s ease forwards;
    }

    @keyframes fadeInUp {
      0% { opacity: 0; transform: translateY(30px); }
      100% { opacity: 1; transform: translateY(0); }
    }

    .logo {
      width: 120px;
      height: 120px;
      margin-bottom: 20px;
      animation: bounce 2s infinite;
    }

    @keyframes bounce {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-15px); }
    }

    h1 {
      font-size: 3rem;
      font-weight: 700;
      margin-bottom: 15px;
      color: #FFD166;
      text-shadow: 1px 1px 5px rgba(0,0,0,0.4);
    }

    h2 {
      font-size: 1.4rem;
      font-weight: 400;
      margin-bottom: 30px;
      color: #fff;
    }

    .btn {
      display: inline-block;
      padding: 15px 45px;
      margin: 10px;
      font-size: 1.1rem;
      font-weight: 600;
      border-radius: 50px;
      text-decoration: none;
      color: #fff;
      background: linear-gradient(135deg, #FEE440, #F8961E);
      box-shadow: 0 8px 20px rgba(0,0,0,0.3);
      transition: all 0.3s ease;
    }

    .btn:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 25px rgba(0,0,0,0.4);
      background: linear-gradient(135deg, #F8961E, #FEE440);
      color: #000;
    }

    .btn-back {
      background: transparent;
      border: 2px solid #fff;
      color: #fff;
    }

    .btn-back:hover {
      background: #fff;
      color: #000;
      transform: translateY(-5px);
      border-color: #FFD166;
    }

    @media (max-width: 600px) {
      h1 { font-size: 2.2rem; }
      h2 { font-size: 1rem; }
      .btn { padding: 12px 35px; font-size: 1rem; }
      .logo { width: 100px; height: 100px; }
    }

  </style>
</head>
<body>

  <div class="container">
    <img src="../frontend/images/bee.png" alt="Bee Logo" class="logo"/>
    <h1>Welcome to HiveCare!</h1>
    <h2>Your smart beehive monitoring system is ready</h2>

    <a href="user-login.php" class="btn">Login to your Account</a>
    <a href="frontindex.php" class="btn btn-back">⬅ Back</a>
  </div>

</body>
</html>
