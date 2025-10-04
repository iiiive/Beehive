<?php
session_start();
if (!isset($_SESSION['db_logged_in']) || $_SESSION['db_logged_in'] !== true) {
    header("Location: db-login.php"); // redirect back to login if not logged in
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Beehive Monitoring Databases</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
   <style>
    body {
    position: relative;
    font-family: 'Verdana', sans-serif;
    min-height: 100vh;
    margin: 0;
}

/* Background with overlay for 50% opacity */
body::before {
    content: "";
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background-image: url("https://beeswiki.com/wp-content/uploads/2023/03/Are-there-stingless-bees-1024x683.png");
    background-size: cover;
    filter: brightness(50%); /* darker background */
    z-index: -1; /* push it behind content */
}

/* Wrapper */
.wrapper {
    margin: 50px auto;
    text-align: center;
}

/* Title */
h2 {
    font-family: 'Cursive', 'Brush Script MT', sans-serif;
    font-size: 5rem;
    margin-top: 100px;
    margin-bottom: 80px;
    color: #FEDE16; /* School Bus Yellow */
    text-shadow: 2px 2px 5px rgba(0,0,0,0.6);
}

/* Cards container */
.container-cards {
    display: flex;
    justify-content: center;
    gap: 30px;
    flex-wrap: wrap;
}

/* Card style */
.card-link {
    border-radius: 20px;
    padding: 20px;
    width: 220px;
    height: 220px;
    background: linear-gradient(145deg, #FFF2A3, #E9E7D8); /* Vanilla + Eggshell */
    box-shadow: 6px 6px 15px rgba(116, 81, 45, 0.6); /* Coffee shadow */
    transition: transform 0.3s ease, background 0.3s ease;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-decoration: none;
}

.card-link:hover {
    transform: scale(1.05);
    background: linear-gradient(145deg, #FEDE16, #FFF2A3); /* Brighter hover */
}

.card-link img {
    width: 90px;
    height: 90px;
    margin-bottom: 10px;
}

.label {
    font-weight: bold;
    font-size: 1.2rem;
    color: #0B0806; /* Smoky Black for text */
}

/* Back Button */
.back-btn {
    display: inline-block;
    margin-top: 80px;
    padding: 12px 25px;
    font-size: 1rem;
    font-weight: bold;
    color: #ffffffff;
    background: #47300cff; /* Coffee */
    border-radius: 25px;
    text-decoration: none;
    box-shadow: 4px 4px 10px rgba(0,0,0,0.3);
    transition: background 0.3s ease, transform 0.2s ease;
}

.back-btn:hover {
    background: #FEDE16; /* Yellow */
    color: #0B0806;
    transform: scale(1.05);
}

</style>
</head>
<body>
    <div class="wrapper">
        <h2>HiveCare Monitoring Records</h2>

        <div class="container-cards">
            <a href="../index.php" class="card-link">
                <img src="images/bee.png" alt="Beehive Icon">
                <div class="label">Beehive Readings</div>
            </a>

            <a href="../userindex.php" class="card-link">
                <img src="images/user.png" alt="User Icon">
                <div class="label">User Information</div>
            </a>

            <a href="../adminindex.php" class="card-link">
                <img src="images/admin.png" alt="Admin Icon">
                <div class="label">Admin Information</div>
            </a>

            <a href="../feedindex.php" class="card-link">
                <img src="images/admin.png" alt="Admin Icon">
                <div class="label">Bee Feeding Information</div>
            </a>
        </div>

        <a href="admin-dashboard.php" class="back-btn">⬅ Back</a>
    </div>
</body>
</html>
