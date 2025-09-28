<?php
require_once "config.php";


$sql = "SELECT * FROM admins";
$result = mysqli_query($link, $sql);


header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=AdminInfoTable.csv');



$output = fopen('php://output', 'w');



fputcsv($output, ['Admin ID', 'First Name', 'Last Name', 'Username', 'Email Address', 'Created At']);


if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [ 
                                    $row['admin_id'], 
                                    $row['firstname'],  
                                    $row['lastname'],
                                    $row['username'],
                                    $row['email'],
                                    $row['created_at']
                                    ]);}
                                    
    
}


fclose($output);
mysqli_close($link);
exit;

?>