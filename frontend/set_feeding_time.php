<?php
$filename = 'next_feeding.json';
$message = "";

// Default interval
$existing = ['days'=>3, 'hours'=>0, 'minutes'=>0];

// Load existing values
if (file_exists($filename)) {
    $saved = json_decode(file_get_contents($filename), true);
    if (is_array($saved)) {
        $existing['days']    = isset($saved['days']) ? intval($saved['days']) : 3;
        $existing['hours']   = isset($saved['hours']) ? intval($saved['hours']) : 0;
        $existing['minutes'] = isset($saved['minutes']) ? intval($saved['minutes']) : 0;
    }
}

// Save new interval
if (isset($_POST['save_time'])) {
    $days    = intval($_POST['days']);
    $hours   = intval($_POST['hours']);
    $minutes = intval($_POST['minutes']);

    // Keep last feeding if exists
    $last_feeding = isset($saved['last_feeding']) ? $saved['last_feeding'] : date('Y-m-d H:i:s');

    file_put_contents($filename, json_encode([
        'days'         => $days,
        'hours'        => $hours,
        'minutes'      => $minutes,
        'last_feeding' => $last_feeding
    ]));

    $message = "Feeding time saved!";
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
