<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>HiveCare</title>

  <!-- FONTS -->
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Playfair+Display:wght@600&family=Montserrat:wght@300;400;600;700&family=Poppins:wght@500;600&display=swap" rel="stylesheet">

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

    /* Dark overlay */
    body::before {
      content: '';
      position: absolute;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0, 0, 0, 0.55);
      z-index: 0;
    }

   .container {
      position: relative;
      z-index: 1;
      text-align: center;
      color: #fff;
      padding: 50px 30px;
      background: rgba(255,255,255,0.15);
      border-radius: 20px;
      backdrop-filter: blur(10px);
      box-shadow: 0 8px 35px rgba(0,0,0,0.5);
      animation: fadeInUp 1s ease forwards;
    }


    @keyframes fadeInUp {
      0% { opacity: 0; transform: translateY(30px); }
      100% { opacity: 1; transform: translateY(0); }
    }

    /* LOGO */
    .logo {
      width: 120px;
      height: 120px;
      margin: 0 auto 15px auto;
      display: block;
      animation: bounce 2s infinite;
    }

    @keyframes bounce {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-15px); }
    }

    /* SAME LINE TITLE */
    .title-row {
      display: inline-flex;
      align-items: baseline;
      gap: 10px;
      justify-content: center;
    }

    /* "Welcome to" */
    .welcome {
      font-family: 'Poppins', sans-serif;
      font-size: 2.5rem;
      font-weight: 600;
      color: #FFE19A;
      text-shadow: 2px 2px 6px rgba(0,0,0,0.4);
    }

    /* "HiveCare" */
    .hivecare {
      font-family: 'Brush Script MT', cursive;
      font-size: 3rem;
      color: #FFC900;
      text-shadow: 3px 3px 9px rgba(0,0,0,0.45);
    }

    h2 {
      font-size: 1.3rem;
      font-weight: 300;
      margin-top: 20px;
      margin-bottom: 30px;
      color: #fff;
      text-shadow: 1px 1px 4px rgba(0,0,0,0.4);
    }

    /* BUTTONS */
    .btn {
      display: inline-block;
      padding: 15px 45px;
      margin: 10px;
      font-size: 1.1rem;
      font-weight: 600;
      border-radius: 50px;
      text-decoration: none;
      background: linear-gradient(135deg, #B6771D, #FFC900);
      color: #432d18;
      box-shadow: 0 8px 20px rgba(0,0,0,0.3);
      transition: 0.3s ease;
    }

    .btn:hover {
      transform: translateY(-5px);
      background: linear-gradient(135deg, #7B542F, #FFE19A);
      color: #000;
    }

    /* BACK BUTTON */
    .btn-back {
      background: transparent;
      border: 2px solid #fff;
      color: #fff;
    }

    .btn-back:hover {
      background: #fff;
      color: #000;
      border-color: #FFD166;
    }

    /* RESPONSIVE */
    @media (max-width: 600px) {
      .welcome { font-size: 1.6rem; }
      .hivecare { font-size: 2.5rem; }
      h2 { font-size: 1rem; }
      .logo { width: 100px; height: 100px; }
    }

  </style>
</head>

<body>

  <div class="container">

    <img src="../frontend/images/bee.png" alt="Bee Logo" class="logo"/>

    <div class="title-row">
      <h1 class="welcome">Welcome to</h1>
      <h1 class="hivecare">HiveCare!</h1>
    </div>

    <h2>Your smart beehive monitoring system is ready</h2>

    <a href="user-login.php" class="btn">Login to your Account</a>
    <a href="frontindex.php" class="btn btn-back">⬅ Back</a>

  </div>

</body>
</html>
