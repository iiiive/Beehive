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

.wrapper {
    margin: 0px auto; /* smaller top margin */
    text-align: center;
    padding-top: 0px; /* adds small breathing room */
}

/* Title */
h2 {
    font-family: 'Cursive', 'Brush Script MT', sans-serif;
    font-size: 4.5rem;
    margin-top: 10px;  /* reduced top space */
    margin-bottom: 60px; /* smaller gap below */
    color: #FEDE16; /* School Bus Yellow */
    text-shadow: 2px 2px 5px rgba(0,0,0,0.6);
}

/* Cards container */
.container-cards {
    display: grid;
    grid-template-columns: repeat(2, 1fr); /* 2 columns */
    gap: 25px; /* spacing between cards */
    justify-content: center;
    align-items: center;
    place-items: center; /* centers content nicely */
    width: 100%;
    max-width: 600px;
    margin: 0 auto;
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
    margin-left: 30px;
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
        /* RESPONSIVENESS */
        @media (max-width: 992px) {
            .wrapper {
                margin-top: 80px;
            }

            h2 {
                font-size: 3.5rem;
                margin-bottom: 40px;
            }

            .container-cards {
                grid-template-columns: repeat(2, 1fr);
                gap: 30px;
            }

            .card-link {
                max-width: 220px;
                height: 200px;
            }
        }

        @media (max-width: 768px) {
            h2 {
                font-size: 3rem;
                margin-bottom: 30px;
            }

            .container-cards {
                grid-template-columns: 1fr;
                gap: 25px;
            }

            .card-link {
                width: 80%;
                height: 180px;
            }

            .card-link img {
                width: 75px;
                height: 75px;
            }

            .label {
                font-size: 1rem;
            }
        }

        @media (max-width: 480px) {
            h2 {
                font-size: 2.5rem;
            }

            .back-btn {
                padding: 8px 16px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <a href="admin-dashboard.php" class="back-btn">⬅ Back</a>

    <div class="wrapper">
        <h2>HiveCare Monitoring Records</h2>

        <div class="container-cards">
            <a href="../index.php" class="card-link">
                <img src="images/bee2.png" alt="Beehive Icon">
                <div class="label">Beehive Readings</div>
            </a>

            <a href="../feedindex.php" class="card-link">
                <img src="images/info.png" alt="Feeding Icon">
                <div class="label">Bee Feeding Information</div>
            </a>

            <a href="../userindex.php" class="card-link">
                <img src="images/user.png" alt="User Icon">
                <div class="label">User Information</div>
            </a>

            <a href="../adminindex.php" class="card-link">
                <img src="images/admin.png" alt="Admin Icon">
                <div class="label">Admin Information</div>
            </a>
        </div>
    </div>
</body>
</html>
