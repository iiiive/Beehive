<?php
require_once "../config.php";
session_start();
$user_id = $_SESSION['user_id'] ?? 1; // replace with actual session user_id

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $days    = intval($_POST['days']);
    $hours   = intval($_POST['hours']);
    $minutes = intval($_POST['minutes']);
    
    // convert all to minutes
    $total_minutes = ($days*24*60) + ($hours*60) + $minutes;
    $next_feed = date('Y-m-d H:i:s', strtotime("+$total_minutes minutes"));

    // check if user already has a schedule
    $check = mysqli_query($link, "SELECT * FROM bee_feeding_schedule WHERE user_id=$user_id");
    if(mysqli_num_rows($check) > 0){
        mysqli_query($link, "UPDATE bee_feeding_schedule SET next_feed='$next_feed', interval_minutes=$total_minutes WHERE user_id=$user_id");
    } else {
        mysqli_query($link, "INSERT INTO bee_feeding_schedule (user_id, next_feed, interval_minutes) VALUES ($user_id, '$next_feed', $total_minutes)");
    }

    $success = "Feeding schedule updated successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Set Feeding Time</title>

<style>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
  font-family: Raleway, sans-serif;
}
body {
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}
body::before {
  content: "";
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
background: url("https://beeswiki.com/wp-content/uploads/2023/03/Are-there-stingless-bees-1024x683.png") 
  no-repeat center center / cover;
  filter: brightness(25%);
  z-index: -1;
}
.container {
  width: 600px;
  height: 520px;
  background: #fff7c3ff;
  border-radius: 20px;
  backdrop-filter: blur(15px);
  -webkit-backdrop-filter: blur(15px);
  border: 1px solid rgba(255,255,255,0.2);
  box-shadow: 0px 0px 24px #ceae1fff;
  padding: 30px;
  animation: fadeIn 1s ease-in-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-30px); }
  to { opacity: 1; transform: translateY(0); }
}
h2 {
  text-align: center;
  color: #47300cff;
  margin-bottom: 25px;
  font-size: 35px;
}
form {
  display: flex;
  flex-direction: column;
}
.form-group {
  margin-bottom: 20px;
}
form label {
  color: #47300cff;
  font-weight: bold;
  margin-bottom: 6px;
    font-size:20px;

  display: block;
}
form input {
  width: 100%;
  padding: 12px;
  border-radius: 10px;
  border: none;
  background: rgba(110, 108, 108, 0.23);
  color: #47300cff;
  font-weight: bold;
  font-size:20px;
  transition: all 0.3s ease;
}
form input::placeholder {
  color: #ddd;
}
form input:focus {
  outline: none;
  border: 3px solid #e7d25bff;
  background: rgba(255, 255, 255, 0.25);
}
button {
  width: 100%;
  padding: 14px;
  border: none;
  border-radius: 12px;
  background: #e7d25bff;
  color: #333;
  font-weight: bold;
  font-size: 20px;
  margin-top:30px;
  cursor: pointer;
  transition: all 0.3s ease;
}
button:hover {
  background: #cdbd49;
  color: #000;

  transform: translateY(-2px);
}
button:active {
  transform: scale(0.95);
}
.success, .error {
  text-align: center;
  margin-top: 15px;
  font-weight: bold;
}
.success { 
    color: #299b29ff; 
    margin-bottom: 30px; 
}
.error { 
    color: #ec2f2fff; 
}
.back-btn {
  position: absolute;
  top: 20px;
  left: 20px;
  padding: 10px 20px;
  font-size: 1rem;
  font-weight: bold;
  color: #333;
  background: #e7d25bff;
  border-radius: 20px;
  text-decoration: none;
  box-shadow: 4px 4px 10px rgba(0,0,0,0.3);
  transition: background 0.3s ease, transform 0.2s ease;
  z-index: 1000;
}
.back-btn:hover {
  background: #e7d25bff;
  color: #333;
  transform: scale(1.05);
}
</style>
</head>
<body>

<a href="user-dashboard.php" class="back-btn">← Back</a>

<div class="container">
  <h2>Set Feeding Schedule</h2>

  <?php if(isset($success)) echo "<div class='success'>$success</div>"; ?>

  <form method="POST">
    <div class="form-group">
      <label>Days</label>
      <input type="number" name="days" min="0" value="0" placeholder="Enter days">
    </div>
    <div class="form-group">
      <label>Hours</label>
      <input type="number" name="hours" min="0" max="23" value="0" placeholder="Enter hours">
    </div>
    <div class="form-group">
      <label>Minutes</label>
      <input type="number" name="minutes" min="0" max="59" value="0" placeholder="Enter minutes">
    </div>
    <button type="submit">Set Schedule</button>
  </form>
</div>

</body>
</html>
