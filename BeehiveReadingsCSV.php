<?php
require_once "config.php";


$sql = "SELECT * FROM beehive_readings";
$result = mysqli_query($link, $sql);


header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=BeehiveReadingsTable.csv');



$output = fopen('php://output', 'w');



fputcsv($output, ['Reading ID', 'Timestamp', 'Temperature', 'Humidity', 'Weight', 'Fan Status', 'Status']);


if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [ 
                                    $row['reading_id'], 
                                    $row['timestamp'],  
                                    $row['temperature'],
                                    $row['humidity'],
                                    $row['weight'],
                                    $row['fan_status'],
                                    $row['status']
                                    ]);}
                                    
    
}


fclose($output);
mysqli_close($link);
exit;

?>