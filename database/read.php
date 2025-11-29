<?php
if (isset($_GET["reading_id"]) && !empty(trim($_GET["reading_id"]))) {
    require_once "config.php";
    
    $sql = "SELECT * FROM beehive_readings WHERE reading_id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $param_reading_id);
        $param_reading_id = trim($_GET["reading_id"]);
        
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                // Assign values
                $timestamp   = $row["timestamp"];
                $temperature = $row["temperature"];
                $humidity    = $row["humidity"];
                $weight      = $row["weight"];
                $fan_status  = $row["fan_status"];
                $status      = $row["status"];

            } else {
                header("location: error.php");
                exit();
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
    mysqli_stmt_close($stmt);
    mysqli_close($link);
} else {
    header("location: error.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Beehive Record</title>
    <style>
        /* Card Colors */
        :root {
            --white: hsl(0, 0%, 100%);
            --black: hsl(240, 15%, 9%);
            --paragraph: hsla(0, 0%, 9%, 1.00);
            --line: hsl(240, 9%, 17%);
            --primary: hsla(54, 93%, 61%, 1.00);
        }
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden; /* 🚫 disables scrolling */
        }
        body {
            font-family: 'Raleway', sans-serif;
            min-height: 100vh;
            background: url("https://static.vecteezy.com/system/resources/previews/000/532/210/original/vector-bee-hive-background.jpg") no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px;
        }

        .card {
            position: relative;          
            display: flex;
            flex-direction: column;
            gap: 1rem;
            padding: 1.5rem;
            width: 25rem;

            background-color: hsla(61, 84%, 57%, 1.00);
            background-image: 
                radial-gradient(at 88% 40%, hsla(0, 0%, 100%, 1.00) 0px, transparent 85%),
                radial-gradient(at 49% 30%, hsla(0, 0%, 100%, 1.00) 0px, transparent 85%),
                radial-gradient(at 14% 26%, hsla(0, 0%, 100%, 1.00) 0px, transparent 85%),
                radial-gradient(at 0% 64%, hsla(54, 99%, 26%, 1.00) 0px, transparent 85%),
                radial-gradient(at 41% 94%, hsla(56, 87%, 50%, 1.00) 0px, transparent 85%),
                radial-gradient(at 100% 99%, hsla(66, 88%, 53%, 1.00) 0px, transparent 85%);
            
            border-radius: 1rem;
            box-shadow: 0px -16px 24px 0px rgba(255, 255, 255, 0.25) inset;
        }

        .card .card__border {
            overflow: hidden;
            pointer-events: none;
            position: absolute;
            z-index: -10;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: calc(100% + 2px);
            height: calc(100% + 2px);
            background-image: linear-gradient(0deg, hsla(0, 0%, 1%, 1.00) -50%, hsl(0,0%,40%) 100%);
            border-radius: 1rem;
        }

        .card .card__border::before {
            content: "";
            pointer-events: none;
            position: fixed;
            z-index: 200;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(0deg);
            transform-origin: left;
            width: 200%;
            height: 10rem;
            background-image: linear-gradient(
                0deg,
                hsla(64, 82%, 56%, 1.00) 40%,
                hsla(64, 82%, 56%, 1.00) 60%,
                hsla(0, 0%, 100%, 0.00) 100%
            );
            animation: rotate 2s linear infinite;
        }

        @keyframes rotate {
            to {
                transform: rotate(360deg);
            }
        }

        .card_title__container {
            text-align: center;
        }

        .card_title__container .card_title {
            font-size: 1.8rem;
            color: var(--black);
            font-weight: bold;
        }

        .card_title__container .card_paragraph {
            margin-top: 0.25rem;
            font-size: 1.0rem;
            color: var(--paragraph);
        }

        .line {
            width: 100%;
            height: 0.2rem;
            background-color: var(--line);
            border: none;
            margin-bottom: 20px;
        }

        .card__list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-left: 0px;
        }

        .card__list_item {
            display: flex;
            justify-content: space-between;
            background: rgba(99, 98, 98, 0.21);
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            color: var(--black);
            font-weight: bold;
            margin-right:25px;
        }

        .button {
            cursor: pointer;
            padding: 0.6rem;
            width: 100%;
            background-color: #74512D;
            font-size: 1.00rem;
            color: #ffff;
            font-weight: bold;
            border: 0;
            border-radius: 9999px;
            text-decoration: none;
            text-align: center;
            display: inline-block;
        }

        .button:hover {
            transform: translateY(-2px) scale(1.02);
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card__border"></div>
        <div class="card_title__container">
            <span class="card_title">Beehive Reading Details</span>
            <p class="card_paragraph">Real-time data for hive</p>
        </div>
        <hr class="line" />

        <ul class="card__list">
            <li class="card__list_item">
                <span>Timestamp:</span>
                <span><?php echo $timestamp; ?></span>
            </li>
            <li class="card__list_item">
                <span>Temperature (°C):</span>
                <span><?php echo $temperature; ?></span>
            </li>
            <li class="card__list_item">
                <span>Humidity (%):</span>
                <span><?php echo $humidity; ?></span>
            </li>
            <li class="card__list_item">
                <span>Weight (kg):</span>
                <span><?php echo $weight; ?></span>
            </li>
            <li class="card__list_item">
                <span>Fan Status:</span>
                <span><?php echo $fan_status; ?></span>
            </li>
            <li class="card__list_item">
                <span>Status:</span>
                <span><?php echo $status; ?></span>
            </li>
        </ul>

        <a href="index.php" class="button">Back</a>
    </div>
</body>
</html>
