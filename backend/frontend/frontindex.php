<?php
$trivia = [
    "Stingless bees are important pollinators in the Philippines.",
    "They produce 'Meliponine honey', prized for its medicinal properties.",
    "Stingless bees are gentle and do not sting, perfect for urban beekeeping."
];

$randomTrivia = $trivia[array_rand($trivia)];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HiveCare</title>
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body, html {
      margin: 0;
      padding: 0;
      font-family: 'Roboto', sans-serif;
      scroll-behavior: smooth; 
    }

    header {
      position: relative;
      background-image: url('homepage.jpeg');
      background-size: cover;
      background-position: center;
      height: 80vh;
      color: white;
      text-align: center;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }

    header::after {
      content: "";
      position: absolute;
      top:0; left:0; right:0; bottom:0;
      background: rgba(0,0,0,0.5);
    }

    header .content {
      position: relative;
      z-index: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .top-right-btn {
      position: absolute;
      top: 20px;
      right: 30px;
      z-index: 2;
    }

  header h1 {
    font-family: 'Cursive', 'Brush Script MT', sans-serif;
    font-weight: 100;
    font-size: clamp(3rem, 10vw, 12rem); /* scales between 3rem and 8rem depending on screen */

    }

    header img.logo {
      width: 200px;
      height: auto;
      margin-top: 70px;

    }

    header p {
      font-family: 'Roboto', sans-serif; /* clean sans-serif font */
      font-size: 1.5rem;
      margin-bottom: 60px;
    }

    .btn-custom {
      background-color: #ffb300;
      color: white;
      font-weight: bold;
      border-radius: 50px;
      padding: 12px 30px;
            margin-bottom: 80px;

    }

    .btn-custom:hover {
      background-color: #e6a500;
      color: white;
    }

    .info-section {
      padding: 80px 20px;
      background-color: #fffbee;
    }

    .info-card {
      border-radius: 12px;
      background-color: #fff;
      padding: 30px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.1);
      transition: transform 0.3s;
      text-align: center;
    }

    .info-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }

    .info-card h5 {
      color: #ffb300;
      font-weight: bold;
      margin-bottom: 15px;
    }

    .trivia-section {
      padding: 60px 20px;
      text-align: center;
      background-color: #fff3cd;
      border-left: 8px solid #ffb300;
      max-width: 800px;
      margin: 40px auto;
      border-radius: 12px;
      font-size: 1.2rem;
    }
    body{
      background-image: url('https://media.npr.org/assets/img/2018/10/30/bee1_wide-1dead2b859ef689811a962ce7aa6ace8a2a733d7.jpg');
      background-repeat: no-repeat;
      background-size: cover;
      background-attachment: fixed;
    }

    @media(max-width:768px) {
      header h1 {
        font-size: 2.5rem;
      }
      header p {
        font-size: 1.2rem;
      }
      .top-right-btn {
        top: 10px;
        right: 15px;
      }
      header img.logo {
        width: 60px;
      }
    }
  </style>
</head>
<body>

  <header>
    <a href="homepage.php" class="btn btn-custom top-right-btn">Get Started</a>

    <div class="content">
      <img src="bee.png" alt="Bee Logo" class="logo">
      <h1>HiveCare</h1>
      <p>Learn about stingless bees in the Philippines!</p>
      <a href="#info" class="btn btn-custom btn-lg">Read More</a>
    </div>
  </header>

  <section id="info" class="info-section">
    <div class="container">
      <h2 class="text-center mb-5">Stingless Bees Facts</h2>
      <div class="row g-4 justify-content-center ">
        <div class="col-md-4">
          <div class="info-card">
            <h5>Pollinators</h5>
            <p>Stingless bees are essential pollinators in the Philippines, supporting biodiversity.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="info-card">
            <h5>Honey Production</h5>
            <p>They produce 'Meliponine honey', which has medicinal properties and is highly prized.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="info-card">
            <h5>Gentle Bees</h5>
            <p>These bees are small and do not sting, making them perfect for urban beekeeping.</p>
          </div>
        </div>
        <div class="col-12 text-center mt-4">
        <h2>Did You Know?</h2>
    <p><?php echo $randomTrivia; ?></p>
        
      </div>
    </div>
  </section>


</body>
</html>
