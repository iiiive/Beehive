<?php
$filename = 'next_feeding.json';
$message = '';

// Handle form submission
if (isset($_POST['save'])) {
    $days = intval($_POST['days'] ?? 0);
    $hours = intval($_POST['hours'] ?? 0);
    $minutes = intval($_POST['minutes'] ?? 0);

    // Save as total milliseconds for easy JS use
    $data = [
        'days' => $days,
        'hours' => $hours,
        'minutes' => $minutes,
        'saved_at' => date('Y-m-d H:i:s') // optional
    ];

    file_put_contents($filename, json_encode($data));
    $message = "Feeding countdown time saved successfully!";
}

// Load existing values
$existing = ['days'=>3, 'hours'=>0, 'minutes'=>0];
if (file_exists($filename)) {
    $saved = json_decode(file_get_contents($filename), true);
    if ($saved) $existing = $saved;
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
    <h2>Set Feeding Countdown Time</h2>
    <?php if ($message) echo "<div class='alert alert-success'>$message</div>"; ?>

    <form method="post">
        <div class="row mb-2">
            <div class="col">
                <input type="number" name="days" class="form-control" placeholder="Days" min="0" value="<?= $existing['days'] ?>">
            </div>
            <div class="col">
                <input type="number" name="hours" class="form-control" placeholder="Hours" min="0" max="23" value="<?= $existing['hours'] ?>">
            </div>
            <div class="col">
                <input type="number" name="minutes" class="form-control" placeholder="Minutes" min="0" max="59" value="<?= $existing['minutes'] ?>">
            </div>
        </div>
        <button type="submit" name="save" class="btn btn-primary">Save Time</button>
    </form>
</div>

</body>
</html>
