<?php
require_once "config.php";


$sql = "SELECT * FROM users";
$result = mysqli_query($link, $sql);


header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=UsersInfoTable.csv');



$output = fopen('php://output', 'w');



fputcsv($output, ['User ID', 'First Name', 'Last Name', 'Username', 'Email Address', 'Contact Number', 'Status', 'Created At']);


if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [ 
                                    $row['user_id'], 
                                    $row['firstname'],  
                                    $row['lastname'],
                                    $row['username'],
                                    $row['email'],
                                    $row['contact_number'],
                                    $row['status'],
                                    $row['created_at']
                                    ]);}
                                    
    
}


fclose($output);
mysqli_close($link);
exit;

?>