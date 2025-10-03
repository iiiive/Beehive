<?php
require_once "../config.php";

// Assuming you have user session
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
        // update
        mysqli_query($link, "UPDATE bee_feeding_schedule SET next_feed='$next_feed', interval_minutes=$total_minutes WHERE user_id=$user_id");
    } else {
        // insert
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
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

<div class="container">
<h2>Set Feeding Schedule</h2>

<?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>

<form method="POST">
  <div class="mb-3">
    <label>Days</label>
    <input type="number" name="days" class="form-control" min="0" value="0">
  </div>
  <div class="mb-3">
    <label>Hours</label>
    <input type="number" name="hours" class="form-control" min="0" max="23" value="0">
  </div>
  <div class="mb-3">
    <label>Minutes</label>
    <input type="number" name="minutes" class="form-control" min="0" max="59" value="30">
  </div>
  <button type="submit" class="btn btn-primary">Set Schedule</button>
</form>

<a href="user-dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>
</body>
</html>
