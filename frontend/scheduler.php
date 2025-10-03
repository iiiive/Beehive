<?php
$filename = 'next_feeding.json';

// Handle "Done" button click
if (isset($_POST['done'])) {
    $nextDate = new DateTime();
    $nextDate->modify('+3 days'); // next feeding in 3 days
    $data = ['next_feeding' => $nextDate->format('Y-m-d')];
    file_put_contents($filename, json_encode($data));
    $message = "Feeding marked done! Next feeding scheduled in 3 days.";
}

// Load current next feeding date
$nextFeeding = null;
if (file_exists($filename)) {
    $data = json_decode(file_get_contents($filename), true);
    if ($data && isset($data['next_feeding'])) {
        $nextFeeding = $data['next_feeding'];
    }
}

// Check rainy season (June-Nov)
$month = date('n');
$isRainy = $month >= 6 && $month <= 11;
?>
