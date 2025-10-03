<?php
require_once "../config.php";
session_start();
$user_id = $_SESSION['user_id'] ?? 1;

$sql = "SELECT * FROM bee_feeding_schedule WHERE user_id=$user_id LIMIT 1";
$res = mysqli_query($link, $sql);
$feeding = mysqli_fetch_assoc($res);

if($feeding){
    $interval = $feeding['interval_minutes'];
    $next_feed = date('Y-m-d H:i:s', strtotime("+$interval minutes"));

    mysqli_query($link, "UPDATE bee_feeding_schedule 
                        SET last_fed=NOW(), next_feed='$next_feed' 
                        WHERE user_id=$user_id");
}
mysqli_close($link);
?>
