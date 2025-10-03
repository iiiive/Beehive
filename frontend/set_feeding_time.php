<?php
require_once "../config.php";

// === Handle form submission first ===
if(isset($_POST['save_time'])) {
    $days = intval($_POST['days']);
    $hours = intval($_POST['hours']);
    $minutes = intval($_POST['minutes']);
    $hive_id = 1; // example hive

    // Check if record exists
    $check = mysqli_query($link, "SELECT * FROM feeding_schedule WHERE hive_id=$hive_id");
    if(mysqli_num_rows($check) > 0) {
        mysqli_query($link, "UPDATE feeding_schedule 
                             SET interval_days=$days, interval_hours=$hours, interval_minutes=$minutes 
                             WHERE hive_id=$hive_id");
    } else {
        $now = date('Y-m-d H:i:s');
        mysqli_query($link, "INSERT INTO feeding_schedule (last_feeding, interval_days, interval_hours, interval_minutes, hive_id) 
                             VALUES ('$now', $days, $hours, $minutes, $hive_id)");
    }

    $message = "Feeding interval updated!";
}

// === Load current interval for form display ===
$hive_id = 1;
$result = mysqli_query($link, "SELECT * FROM feeding_schedule WHERE hive_id=$hive_id");
if(mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $existing = [
        'days' => $row['interval_days'],
        'hours' => $row['interval_hours'],
        'minutes' => $row['interval_minutes']
    ];
} else {
    $existing = ['days'=>3, 'hours'=>0, 'minutes'=>0];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Set Feeding Time</title>
</head>
<body>
    <h2>Set Feeding Time</h2>
    <?php if(!empty($message)) echo "<p style='color:green;'>$message</p>"; ?>
    <form method="post">
        <input type="number" name="days" min="0" value="<?= $existing['days'] ?>"> Days
        <input type="number" name="hours" min="0" max="23" value="<?= $existing['hours'] ?>"> Hours
        <input type="number" name="minutes" min="0" max="59" value="<?= $existing['minutes'] ?>"> Minutes
        <button type="submit" name="save_time">Save</button>
    </form>
    <a href="user-dashboard.php">Go to Dashboard</a>
</body>
</html>
