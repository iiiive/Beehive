<?php
require_once "config.php";

$sql = "SELECT * FROM beehive_readings ORDER BY timestamp DESC";
$result = mysqli_query($link, $sql);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

mysqli_close($link);

header('Content-Type: application/json');
echo json_encode($data);
?>
