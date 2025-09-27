<?php
require_once "config.php";

$search = $_GET['search'] ?? "";
$filter = $_GET['filter'] ?? "";

$sql = "SELECT * FROM beehive_readings ORDER BY timestamp DESC";

if (!empty($filter)) {
    if ($filter == "statusGood") {
        $sql = "SELECT * FROM beehive_readings WHERE status = 'Good' ORDER BY timestamp DESC";
    } elseif ($filter == "statusBad") {
        $sql = "SELECT * FROM beehive_readings WHERE status = 'Bad' ORDER BY timestamp DESC";
    } elseif ($filter == "highTemp") {
        $sql = "SELECT * FROM beehive_readings WHERE temperature >= 35 ORDER BY timestamp DESC";
    } elseif ($filter == "normalTemp") {
        $sql = "SELECT * FROM beehive_readings WHERE temperature BETWEEN 28 AND 32 ORDER BY timestamp DESC";
    } elseif ($filter == "lowHumidity") {
        $sql = "SELECT * FROM beehive_readings WHERE humidity < 65 ORDER BY timestamp DESC";
    } elseif ($filter == "normalHumidity") {
        $sql = "SELECT * FROM beehive_readings WHERE humidity BETWEEN 65 AND 85 ORDER BY timestamp DESC";
    } elseif ($filter == "fanOn") {
        $sql = "SELECT * FROM beehive_readings WHERE fan_status = 1 ORDER BY timestamp DESC";
    } elseif ($filter == "fanOff") {
        $sql = "SELECT * FROM beehive_readings WHERE fan_status = 0 ORDER BY timestamp DESC";
    } elseif ($filter == "orderAsc") {
        $sql = "SELECT * FROM beehive_readings ORDER BY timestamp ASC";
    } elseif ($filter == "orderDesc") {
        $sql = "SELECT * FROM beehive_readings ORDER BY timestamp DESC";
    }
} elseif (!empty($search)) {
    $search = "%" . $search . "%";
    $sql = "SELECT * FROM beehive_readings 
            WHERE reading_id LIKE ? 
               OR timestamp LIKE ? 
               OR temperature LIKE ? 
               OR humidity LIKE ? 
               OR weight LIKE ? 
               OR fan_status LIKE ? 
               OR status LIKE ?";
}

if (strpos($sql, '?') !== false) {
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "sssssss", $search, $search, $search, $search, $search, $search, $search);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($link, $sql);
}

$data = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
}

mysqli_close($link);

header("Content-Type: application/json");
echo json_encode($data);
?>
