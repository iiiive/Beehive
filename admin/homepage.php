<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>HiveCare</title>

  <!-- FONTS -->
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Playfair+Display:wght@600&family=Montserrat:wght@300;400;600&family=Poppins:wght@500;600&display=swap" rel="stylesheet">

  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Brush Script MT', cursive;
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

   .logo {
  width: 120px;
  height: 120px;
  margin: 0 auto 10px auto; /* centers the logo horizontally */
  display: block; /* ensures proper centering */
  animation: bounce 2s infinite;
}

    @keyframes bounce {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-15px); }
    }

    /* Title wrapper */
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
      font-family: 'Cursive', 'Brush Script MT', sans-serif;
      font-size: 3.8rem;
      color: #FFC900;
      text-shadow: 3px 3px 9px rgba(0,0,0,0.45);
    }

    /* Subtitle */
    h2 {
      font-family: 'Montserrat', sans-serif;
      font-size: 1.3rem;
      font-weight: 300;
      margin-top: 20px;
      margin-bottom: 30px;
      color: #fff;
      text-shadow: 1px 1px 4px rgba(0,0,0,0.4);
    }

    /* Button */
    .btn {
      display: inline-block;
      padding: 15px 45px;
      font-family: 'Poppins', sans-serif;
      font-size: 1.1rem;
      font-weight: 600;
      border-radius: 50px;
      text-decoration: none;
      color: #432d18;
      background: linear-gradient(135deg, #B6771D, #FFC900);
      box-shadow: 0 8px 20px rgba(0,0,0,0.3);
      transition: all 0.3s ease;
    }

    .btn:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 28px rgba(0,0,0,0.45);
      background: linear-gradient(135deg, #7B542F, #FFE19A);
      color: #000;
    }

    @media (max-width: 600px) {
      .welcome { font-size: 1.7rem; }
      .hivecare { font-size: 2.7rem; }
      h2 { font-size: 1rem; }
      .logo { width: 100px; height: 100px; }
    }
  </style>
</head>

<body>
  <div class="container">
    <img src="../frontend/images/bee.png" alt="Bee Logo" class="logo"/>

    <!-- SAME LINE TITLE -->
    <div class="title-row">
      <h1 class="welcome">Welcome to</h1>
      <h1 class="hivecare">HiveCare!</h1>
    </div>

    <h2>Your smart beehive monitoring system is ready</h2>

    <a href="admin-login.php" class="btn">Login to your Account</a>
  </div>
</body>
</html>
