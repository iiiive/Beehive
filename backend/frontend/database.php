<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Beehive Monitoring Databases</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-image: url("https://t3.ftcdn.net/jpg/06/31/48/06/360_F_631480602_mStNuYekDgq1eU9qbAKCtk0V6LxBZxBw.jpg");
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed;
            font-family: 'Verdana', sans-serif;
        }

        .wrapper {
            margin: 50px auto;
            text-align: center;
        }

        h2 {
            font-family: 'Cursive', 'Brush Script MT', sans-serif;
            font-size: 3rem;
            margin-bottom: 50px;
            color: #1f1111ff;
            text-shadow: 2px 2px 5px rgba(0,0,0,0.6);
        }

        .container-cards {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .card-link {
            border-radius: 20px;
            padding: 20px;
            width: 180px;
            height: 200px;
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

        .card-link:hover {
            transform: scale(1.05);
            background: linear-gradient(145deg, #fff7d1, #feba17);
        }

        .card-link img {
            width: 90px;
            height: 90px;
            margin-bottom: 10px;
        }

        .label {
            font-weight: bold;
            font-size: 1.2rem;
            color: #333;
        }

        .back-btn {
            display: inline-block;
            margin-top: 40px;
            padding: 12px 25px;
            font-size: 1rem;
            font-weight: bold;
            color: #fff;
            background: #74512d;
            border-radius: 25px;
            text-decoration: none;
            box-shadow: 4px 4px 10px rgba(0,0,0,0.3);
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .back-btn:hover {
            background: #feba17;
            color: #333;
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
        </div>

        <a href="admin-dashboard.php" class="back-btn">⬅ Back</a>
    </div>
</body>
</html>
