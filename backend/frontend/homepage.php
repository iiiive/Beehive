<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>HiveCare</title>
  <style>
    body {
      font-family: 'Verdana', sans-serif;
      margin: 0;
      padding: 0;
      background-image: url('background.png');
      background-attachment: fixed;
      background-size: cover;
      display: flex;
      flex-direction: column;
      justify-content: center; 
      align-items: center;
      min-height: 100vh;
    }

    .header {
      text-align: center;
    }

    .header img {
      width: 150px;
      height: 150px;
    }

    h1 {
      font-family: 'Cursive', 'Brush Script MT', sans-serif;
      font-size: 5rem;
      margin: 10px 0;
      color: #333;
    }

    h2 {
      font-size: 1.5rem;
      color: #333;
      margin: 20px 0;
      text-align: center;
    }

    .container {
      display: flex;
      justify-content: center;
      gap: 20px;
      flex-wrap: wrap;
      margin-top: 20px;
    }

    .card {
      border-radius: 20px;
      padding: 20px;
      width: 160px;
      height: 170px;
      background: linear-gradient(145deg, #dfdccb, #fffff1);
      box-shadow: 10px 10px 20px #74512d,
                  -10px -10px 20px #74512d;
      transition: transform 0.3s ease, background 0.3s ease;
      cursor: pointer;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-decoration: none;
    }

    .card:hover {
      transform: scale(1.05);
      background: linear-gradient(145deg, #fff7d1, #feba17);
    }

    .card img {
      width: 80px;
      height: 80px;
      margin-bottom: 10px;
    }

    .label {
      font-weight: bold;
      font-size: 1.2rem;
      color: #333;
    }
        @media (max-width: 600px) {
      h1 {
        font-size: 3rem;
      }
      .card {
        width: 130px;
        height: 150px;
      }
      .card img {
        width: 60px;
        height: 60px;
      }
    }
  </style>
</head>
<body>

  <div class="header">
    <img src="bee.png" alt="Bee Logo"/>
    <h1>hivecare</h1>
  </div>

  <h2>Select your role to continue</h2>

  <div class="container">
    <a href="guest-dashboard.php" class="card">
      <img src="guest.png" alt="Guest Icon"/>
      <div class="label">GUEST</div>
    </a>

    <a href="user-login.php" class="card">
      <img src="user.png" alt="User Icon"/>
      <div class="label">USER</div>
    </a>

    <a href="admin-login.php" class="card">
      <img src="admin.png" alt="Admin Icon"/>
      <div class="label">ADMIN</div>
    </a>
  </div>

</body>
</html>
