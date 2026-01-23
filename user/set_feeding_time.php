<?php
require_once "../config.php";
session_start();
$user_id = $_SESSION['user_id'] ?? 0;

if (!$user_id) {
    header("Location: user-login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $days    = intval($_POST['days']);
    $hours   = intval($_POST['hours']);
    $minutes = intval($_POST['minutes']);

    $total_minutes = ($days * 24 * 60) + ($hours * 60) + $minutes;

    if ($total_minutes <= 0) {
        $error = "Please set a feeding interval greater than 0.";
    } else {
        $check = mysqli_query($link, "SELECT id FROM bee_feeding_schedule WHERE user_id = $user_id LIMIT 1");

        if (mysqli_num_rows($check) > 0) {
            $sql = "UPDATE bee_feeding_schedule 
                    SET interval_minutes = $total_minutes,
                        next_feed = NULL,
                        last_fed = NULL,
                        timer_state = 'stopped',
                        remaining_seconds = " . ($total_minutes * 60) . ",
                        paused_at = NULL
                    WHERE user_id = $user_id";
        } else {
            $sql = "INSERT INTO bee_feeding_schedule 
                    (user_id, interval_minutes, next_feed, last_fed, timer_state, remaining_seconds, paused_at)
                    VALUES ($user_id, $total_minutes, NULL, NULL, 'stopped', " . ($total_minutes * 60) . ", NULL)";
        }

        if (mysqli_query($link, $sql)) {
            $success = "Feeding schedule updated successfully!";
        } else {
            $error = "Error saving schedule.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Set Feeding Time</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
  font-family: Raleway, sans-serif;
}

body {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  position: relative;
  padding: 20px;
}

body::before {
  content: "";
  position: absolute;
  inset: 0;
  background: url("https://beeswiki.com/wp-content/uploads/2023/03/Are-there-stingless-bees-1024x683.png")
    no-repeat center/cover;
  filter: brightness(25%);
  z-index: -1;
}

.container {
  width: 400px; /* smaller than 600px */
  max-width: 90%; /* ensures it fits on small screens */
  background: #fff7c3ff;
  border-radius: 20px;
  box-shadow: 0px 0px 24px #ceae1fff;
  padding: 25px; /* slightly smaller padding */
  animation: fadeIn 0.8s ease-in-out;
}


@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-20px); }
  to { opacity: 1; transform: translateY(0); }
}

h2 {
  text-align: center;
  color: #47300cff;
  margin-bottom: 25px;
  font-size: 34px;
}

.form-group {
  margin-bottom: 18px;
}

label {
  font-weight: bold;
  font-size: 20px;
  color: #47300cff;
  margin-bottom: 6px;
  display: block;
}

input {
  width: 100%;
  padding: 12px;
  border-radius: 10px;
  border: none;
  background: rgba(110, 108, 108, 0.23);
  font-size: 18px;
  font-weight: bold;
  color: #47300cff;
}

input:focus {
  outline: none;
  border: 3px solid #e7d25bff;
}

button {
  width: 100%;
  padding: 14px;
  font-size: 18px;
  font-weight: bold;
  border: none;
  border-radius: 12px;
  background: #e7d25bff;
  color: #333;
  margin-top: 25px;
  cursor: pointer;
  transition: 0.3s;
}

button:hover {
  background: #cdbd49;
  transform: translateY(-2px);
}

.success, .error {
  text-align: center;
  font-weight: bold;
  margin-bottom: 15px;
}

.success { color: #299b29ff; }
.error { color: #ec2f2fff; }

.back-btn {
  position: absolute;
  top: 15px;
  left: 15px;
  padding: 8px 16px;
  background: #e7d25bff;
  border-radius: 18px;
  text-decoration: none;
  font-weight: bold;
  color: #333;
}

/* =========================
   📱 MOBILE RESPONSIVE
   ========================= */
@media (max-width: 480px) {

  body {
    align-items: flex-start;
  }

  .container {
    width: 95%;
    padding: 15px;
    margin-top: 50px;
    border-radius: 16px;
  }

  h2 {
    font-size: 22px;
    margin-bottom: 18px;
  }

  label {
    font-size: 15px;
  }

  input {
    padding: 9px;
    font-size: 14px;
  }

  /* 👇 SMALL BUTTON ON PHONE */
  button {
    padding: 8px;
    font-size: 13px;
    border-radius: 8px;
    margin-top: 18px;
  }

  .back-btn {
    font-size: 0.8rem;
    padding: 6px 12px;
  }
}
</style>
</head>

<body>

<a href="user-dashboard.php" class="back-btn">← Back</a>

<div class="container">
  <h2>Set Feeding Schedule</h2>

  <?php if (!empty($success)): ?>
    <div class="success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <?php if (!empty($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="form-group">
      <label>Days</label>
      <input type="number" name="days" min="0" value="0">
    </div>

    <div class="form-group">
      <label>Hours</label>
      <input type="number" name="hours" min="0" max="23" value="0">
    </div>

    <div class="form-group">
      <label>Minutes</label>
      <input type="number" name="minutes" min="0" max="59" value="0">
    </div>

    <button type="submit">Set Schedule</button>
  </form>
</div>

</body>
</html>
