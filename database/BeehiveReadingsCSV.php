<?php
require_once "config.php";

// Fetch all readings including new columns
$sql = "SELECT reading_id, timestamp, temperature, humidity, weight, 
               fan_status, heater_status, mist_status, status
        FROM beehive_readings";
$result = mysqli_query($link, $sql);

// CSV Headers
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=BeehiveReadingsTable.csv');

// Open output stream
$output = fopen('php://output', 'w');

// CSV column headers
fputcsv($output, [
    'Reading ID',
    'Timestamp',
    'Temperature (°C)',
    'Humidity (%)',
    'Weight (kg)',
    'Exhaust Fan Status',
    'Heater Status',
    'Mist Status',
    'Status'
]);

// Write rows
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {

        // Convert numeric values to ON/OFF text for readability
        $fan   = ($row['fan_status'] == 1) ? "ON" : "OFF";
        $heater = ($row['heater_status'] == 1) ? "ON" : "OFF";
        $mist   = ($row['mist_status'] == 1) ? "ON" : "OFF";

        fputcsv($output, [
            $row['reading_id'],
            $row['timestamp'],
            $row['temperature'],
            $row['humidity'],
            $row['weight'],
            $fan,
            $heater,
            $mist,
            $row['status']
        ]);
    }
}

fclose($output);
mysqli_close($link);
exit;

?>
