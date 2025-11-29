<?php
require_once "config.php";

// ✅ Get feeding record by ID
if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $sql = "
        SELECT 
            f.id,
            f.user_id,
            f.next_feed,
            f.interval_minutes,
            f.last_fed,
            f.created_at,
            f.fed_at,
            CONCAT(u.firstname, ' ', u.lastname) AS fed_by
        FROM bee_feeding_schedule AS f
        LEFT JOIN users AS u ON f.fed_by_user_id = u.user_id
        WHERE f.id = ?
    ";

    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        $param_id = trim($_GET["id"]);

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {
                $next_feed        = $row["next_feed"];
                $interval_minutes = $row["interval_minutes"];
                $last_fed         = $row["last_fed"];
                $fed_at           = $row["fed_at"];
                $fed_by           = $row["fed_by"] ?: "Not Recorded";
                $created_at       = $row["created_at"];
                $user_id       = $row["user_id"];

            } else {
                echo "<h2>No record found for ID: " . htmlspecialchars($param_id) . "</h2>";
                exit();
            }
        } else {
            echo "Error executing query.";
            exit();
        }
        mysqli_stmt_close($stmt);
    }

    mysqli_close($link);
} else {
    echo "<h2>Invalid or missing ID parameter.</h2>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Feeding Record Details</title>
        <style>
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
            overflow-y: auto;
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
            padding: 1.5rem 2rem;
            width: 28rem;
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
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .card__list_item {
            display: flex;
            justify-content: space-between;
            background: rgba(99, 98, 98, 0.21);
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            color: var(--black);
            font-weight: bold;
        }

        .button {
            cursor: pointer;
            padding: 0.6rem;
            width: 100%;
            background-color: #74512D;
            font-size: 1rem;
            color: #fff;
            font-weight: bold;
            border: 0;
            border-radius: 9999px;
            text-decoration: none;
            text-align: center;
            display: inline-block;
            margin-top: 15px;
        }

        .button:hover {
            transform: translateY(-2px) scale(1.02);
        }
    </style>

</head>
<body>
    <div class="card">
        <div class="card_title__container">
            <span class="card_title">Feeding Record Details</span>
            <p class="card_paragraph">Schedule Information</p>
        </div>
        <hr class="line" />

        <ul class="card__list">
            <li class="card__list_item"><span>ID:</span><span><?= htmlspecialchars($param_id) ?></span></li>
            <li class="card__list_item"><span>Next Feed:</span><span><?= htmlspecialchars($next_feed) ?></span></li>
            <li class="card__list_item"><span>Interval (minutes):</span><span><?= htmlspecialchars($interval_minutes) ?></span></li>
            <li class="card__list_item"><span>Last Fed:</span><span><?= htmlspecialchars($last_fed ?: '—') ?></span></li>
            <li class="card__list_item"><span>Fed At:</span><span><?= htmlspecialchars($fed_at ?: '—') ?></span></li>
            <li class="card__list_item"><span>Fed By:</span><span><?= htmlspecialchars($fed_by) ?></span></li>
            <li class="card__list_item"><span>User ID:</span><span><?= htmlspecialchars($user_id ?: '—') ?></span></li>
            <li class="card__list_item"><span>Created At:</span><span><?= htmlspecialchars($created_at) ?></span></li>
        </ul>

        <a href="feedindex.php" class="button">Back</a>
    </div>
</body>
</html>
