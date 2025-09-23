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
      background-image: url('images/background.png');
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
  display: flex;
  flex-direction: column;
  align-items: center;
}

.header img {
  width: 150px;
  height: 150px;
  margin: 0; /* no space around the logo */
  padding: 0;
}

h1 {
  font-family: 'Cursive', 'Brush Script MT', sans-serif;
  font-weight: 100;
  font-size: clamp(3rem, 10vw, 12rem);
  color: #333;
  margin: -10px 0 0 0; /* pull text closer to logo */
  padding: 0;
}


    h2 {
      font-size: 1.5rem;
      color: #333;
      margin: 10px 0; /* tighten spacing */
      text-align: center;
    }

    .container {
      display: flex;
      justify-content: center;
      gap: 20px;
      flex-wrap: wrap;
      margin: 10px 0; /* reduce spacing above cards */
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

    .back-btn {
      display: inline-block;
      padding: 12px 25px;
      font-size: 1rem;
      font-weight: bold;
      color: #fff;
      background: #74512d;
      border-radius: 25px;
      text-decoration: none;
      box-shadow: 4px 4px 10px rgba(0,0,0,0.3);
      transition: background 0.3s ease, transform 0.2s ease;
      margin-top: 15px; /* tighter spacing */
    }

    .back-btn:hover {
      background: #feba17;
      color: #333;
      transform: scale(1.05);
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
    <img src="images/bee.png" alt="Bee Logo"/>
    <h1>HiveCare</h1>
  </div>

  <h2>Select your role to continue</h2>

  <div class="container">
    <a href="guest-dashboard.php" class="card">
      <img src="images/guest.png" alt="Guest Icon"/>
      <div class="label">GUEST</div>
    </a>

    <a href="user-login.php" class="card">
      <img src="images/user.png" alt="User Icon"/>
      <div class="label">USER</div>
    </a>

    <a href="admin-login.php" class="card">
      <img src="images/admin.png" alt="Admin Icon"/>
      <div class="label">ADMIN</div>
    </a>
  </div>

  <a href="frontindex.php" class="back-btn">⬅ Back</a>

</body>
</html>
