<?php
require_once "config.php";

// Query all feeding schedule records
$sql = "SELECT * FROM bee_feeding_schedule ORDER BY id ASC";
$result = mysqli_query($link, $sql);

// Tell browser this is a CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=BeeFeedingSchedule_' . date('Y-m-d') . '.csv');

// Open output stream
$output = fopen('php://output', 'w');

// Write the column headers
fputcsv($output, [
    'ID',
    'User ID',
    'Interval (minutes)',
    'Next Feed',
    'Last Fed',
    'Fed By User ID',
    'Fed At',
    'Created At',
    'Updated At'
]);

// Write each row safely
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [
            $row['id'] ?? '',
            $row['user_id'] ?? '',
            $row['interval_minutes'] ?? '',
            $row['next_feed'] ?? '',
            $row['last_fed'] ?? '',
            $row['fed_by_user_id'] ?? '',
            $row['fed_at'] ?? '',
            $row['created_at'] ?? '',
            $row['updated_at'] ?? ''
        ]);
    }
} else {
    fputcsv($output, ['No data found']);
}

// Clean up
fclose($output);
mysqli_close($link);
exit;
?>
