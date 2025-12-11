<?php
require_once "config.php";

$search = $_GET['search'] ?? "";
$filter = $_GET['filter'] ?? "";
$page   = (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) ? (int)$_GET['page'] : 1;

$limit  = 10;
$offset = ($page - 1) * $limit;

// Base SELECT with new columns included
$sql = "SELECT reading_id, timestamp, temperature, humidity, weight,
               fan_status, heater_status, mist_status, status
        FROM beehive_readings
        ORDER BY timestamp DESC";

// === FILTERING ===
if (!empty($filter) && empty($search)) {
    if ($filter == "statusGood") {
        $sql = "SELECT reading_id, timestamp, temperature, humidity, weight,
                       fan_status, heater_status, mist_status, status
                FROM beehive_readings
                WHERE status = 'Good'
                ORDER BY timestamp DESC";
    } elseif ($filter == "statusBad") {
        $sql = "SELECT reading_id, timestamp, temperature, humidity, weight,
                       fan_status, heater_status, mist_status, status
                FROM beehive_readings
                WHERE status = 'Bad'
                ORDER BY timestamp DESC";
    } elseif ($filter == "highTemp") {
        $sql = "SELECT reading_id, timestamp, temperature, humidity, weight,
                       fan_status, heater_status, mist_status, status
                FROM beehive_readings
                WHERE temperature > 32
                ORDER BY timestamp DESC";
    } elseif ($filter == "normalTemp") {
        $sql = "SELECT reading_id, timestamp, temperature, humidity, weight,
                       fan_status, heater_status, mist_status, status
                FROM beehive_readings
                WHERE temperature BETWEEN 28 AND 32
                ORDER BY timestamp DESC";
    } elseif ($filter == "lowHumidity") {
        $sql = "SELECT reading_id, timestamp, temperature, humidity, weight,
                       fan_status, heater_status, mist_status, status
                FROM beehive_readings
                WHERE humidity < 65
                ORDER BY timestamp DESC";
    } elseif ($filter == "normalHumidity") {
        $sql = "SELECT reading_id, timestamp, temperature, humidity, weight,
                       fan_status, heater_status, mist_status, status
                FROM beehive_readings
                WHERE humidity BETWEEN 65 AND 85
                ORDER BY timestamp DESC";
    } elseif ($filter == "HighWeight") {
        $sql = "SELECT reading_id, timestamp, temperature, humidity, weight,
                       fan_status, heater_status, mist_status, status
                FROM beehive_readings
                WHERE weight >= 5
                ORDER BY timestamp DESC";
    } elseif ($filter == "LowWeight") {
        $sql = "SELECT reading_id, timestamp, temperature, humidity, weight,
                       fan_status, heater_status, mist_status, status
                FROM beehive_readings
                WHERE weight <= 2
                ORDER BY timestamp DESC";
    } elseif ($filter == "fanOn") {
        $sql = "SELECT reading_id, timestamp, temperature, humidity, weight,
                       fan_status, heater_status, mist_status, status
                FROM beehive_readings
                WHERE fan_status = 1
                ORDER BY timestamp DESC";
    } elseif ($filter == "fanOff") {
        $sql = "SELECT reading_id, timestamp, temperature, humidity, weight,
                       fan_status, heater_status, mist_status, status
                FROM beehive_readings
                WHERE fan_status = 0
                ORDER BY timestamp DESC";
    } elseif ($filter == "heaterOn") {
        $sql = "SELECT reading_id, timestamp, temperature, humidity, weight,
                       fan_status, heater_status, mist_status, status
                FROM beehive_readings
                WHERE heater_status = 1
                ORDER BY timestamp DESC";
    } elseif ($filter == "heaterOff") {
        $sql = "SELECT reading_id, timestamp, temperature, humidity, weight,
                       fan_status, heater_status, mist_status, status
                FROM beehive_readings
                WHERE heater_status = 0
                ORDER BY timestamp DESC";
    } elseif ($filter == "mistOn") {
        $sql = "SELECT reading_id, timestamp, temperature, humidity, weight,
                       fan_status, heater_status, mist_status, status
                FROM beehive_readings
                WHERE mist_status = 1
                ORDER BY timestamp DESC";
    } elseif ($filter == "mistOff") {
        $sql = "SELECT reading_id, timestamp, temperature, humidity, weight,
                       fan_status, heater_status, mist_status, status
                FROM beehive_readings
                WHERE mist_status = 0
                ORDER BY timestamp DESC";
    } elseif ($filter == "orderAsc") {
        $sql = "SELECT reading_id, timestamp, temperature, humidity, weight,
                       fan_status, heater_status, mist_status, status
                FROM beehive_readings
                ORDER BY timestamp ASC";
    } elseif ($filter == "orderDesc") {
        $sql = "SELECT reading_id, timestamp, temperature, humidity, weight,
                       fan_status, heater_status, mist_status, status
                FROM beehive_readings
                ORDER BY timestamp DESC";
    }
}
// === SEARCH HANDLING (overrides filters) ===
elseif (!empty($search)) {
    $searchTerm = "%" . $search . "%";

    $sql = "SELECT reading_id, timestamp, temperature, humidity, weight,
                   fan_status, heater_status, mist_status, status
            FROM beehive_readings
            WHERE reading_id    LIKE ?
               OR timestamp     LIKE ?
               OR temperature   LIKE ?
               OR humidity      LIKE ?
               OR weight        LIKE ?
               OR fan_status    LIKE ?
               OR heater_status LIKE ?
               OR mist_status   LIKE ?
               OR status        LIKE ?
            ORDER BY timestamp DESC";
}

// Add pagination
$sql_with_limit = $sql . " LIMIT $limit OFFSET $offset";

// Run main query
if (strpos($sql, '?') !== false) {
    // SEARCH MODE – use prepared statement with 9 params
    $stmt = mysqli_prepare($link, $sql_with_limit);
    mysqli_stmt_bind_param(
        $stmt,
        "sssssssss",
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm
    );
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    // FILTER / DEFAULT MODE – no placeholders
    $result = mysqli_query($link, $sql_with_limit);
}

// Fetch rows
$data = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row; // this now includes heater_status & mist_status
    }
}

// Count total rows for pagination
$count_sql = "SELECT COUNT(*) as total FROM (" . $sql . ") AS subquery";

if (strpos($sql, '?') !== false) {
    $stmt = mysqli_prepare($link, $count_sql);
    mysqli_stmt_bind_param(
        $stmt,
        "sssssssss",
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm
    );
    mysqli_stmt_execute($stmt);
    $count_result = mysqli_stmt_get_result($stmt);
} else {
    $count_result = mysqli_query($link, $count_sql);
}

$total_rows = 0;
if ($count_result) {
    $row        = mysqli_fetch_assoc($count_result);
    $total_rows = $row ? (int)$row['total'] : 0;
}

$total_pages = ($total_rows > 0) ? ceil($total_rows / $limit) : 1;

mysqli_close($link);

header("Content-Type: application/json");
echo json_encode([
    "current_page" => $page,
    "total_pages"  => $total_pages,
    "total_rows"   => $total_rows,
    "data"         => $data
]);
?>
