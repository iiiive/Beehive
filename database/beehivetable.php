<?php
require_once "config.php";

$search = $_GET['search'] ?? "";
$filter = $_GET['filter'] ?? "";
$page   = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$limit  = 10;
$offset = ($page - 1) * $limit;

$sql = "SELECT * FROM beehive_readings ORDER BY timestamp DESC";

// Filtering
if (!empty($filter)) {
    if ($filter == "statusGood") {
        $sql = "SELECT * FROM beehive_readings WHERE status = 'Good' ORDER BY timestamp DESC";
    } elseif ($filter == "statusBad") {
        $sql = "SELECT * FROM beehive_readings WHERE status = 'Bad' ORDER BY timestamp DESC";
    } elseif ($filter == "highTemp") {
        $sql = "SELECT * FROM beehive_readings WHERE temperature > 32 ORDER BY timestamp DESC";
    } elseif ($filter == "normalTemp") {
        $sql = "SELECT * FROM beehive_readings WHERE temperature BETWEEN 28 AND 32 ORDER BY timestamp DESC";
    } elseif ($filter == "lowHumidity") {
        $sql = "SELECT * FROM beehive_readings WHERE humidity < 65 ORDER BY timestamp DESC";
    } elseif ($filter == "normalHumidity") {
        $sql = "SELECT * FROM beehive_readings WHERE humidity BETWEEN 65 AND 85 ORDER BY timestamp DESC";
    } elseif ($filter == "HighWeight") {
        $sql = "SELECT * FROM beehive_readings WHERE weight >= 5 ORDER BY timestamp DESC";
    } elseif ($filter == "LowWeight") {
        $sql = "SELECT * FROM beehive_readings WHERE weight <= 2 ORDER BY timestamp DESC";
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
               OR status LIKE ? 
            ORDER BY timestamp DESC";
}

// Add pagination
$sql_with_limit = $sql . " LIMIT $limit OFFSET $offset";

if (strpos($sql, '?') !== false) {
    $stmt = mysqli_prepare($link, $sql_with_limit);
    mysqli_stmt_bind_param($stmt, "sssssss", $search, $search, $search, $search, $search, $search, $search);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($link, $sql_with_limit);
}

$data = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
}

// Count total rows for pagination info
$count_sql = "SELECT COUNT(*) as total FROM (" . $sql . ") AS subquery";
if (strpos($sql, '?') !== false) {
    $stmt = mysqli_prepare($link, $count_sql);
    mysqli_stmt_bind_param($stmt, "sssssss", $search, $search, $search, $search, $search, $search, $search);
    mysqli_stmt_execute($stmt);
    $count_result = mysqli_stmt_get_result($stmt);
} else {
    $count_result = mysqli_query($link, $count_sql);
}

$total_rows = 0;
if ($count_result) {
    $total_rows = mysqli_fetch_assoc($count_result)['total'];
}

$total_pages = ceil($total_rows / $limit);

mysqli_close($link);

header("Content-Type: application/json");
echo json_encode([
    "current_page" => $page,
    "total_pages" => $total_pages,
    "total_rows" => $total_rows,
    "data" => $data
]);
?>
