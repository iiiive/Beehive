<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
  http_response_code(401);
  echo json_encode(["error" => "Unauthorized"]);
  exit;
}

require_once "../config.php";
header("Content-Type: application/json; charset=utf-8");

date_default_timezone_set('Asia/Manila');
mysqli_query($link, "SET time_zone = '+08:00'");

$page  = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$search = trim($_GET['search'] ?? '');
$filter = trim($_GET['filter'] ?? '');

$where = [];
$params = [];
$types  = "";

/**
 * ✅ Search (keep it simple + safe)
 * Search only fields that make sense for this table
 */
if ($search !== '') {
  $where[] = "(id LIKE CONCAT('%', ?, '%')
           OR user_id LIKE CONCAT('%', ?, '%')
           OR fed_by_user_id LIKE CONCAT('%', ?, '%'))";
  $params[] = $search;
  $params[] = $search;
  $params[] = $search;
  $types .= "sss";
}

/**
 * ✅ Date-based filters ONLY (last_fed & next_feed)
 *
 * NOTE: We'll define week as Monday–Sunday (standard for MySQL WEEKDAY()).
 *
 * this_week_next  -> next_feed within current week
 * next_week_next  -> next_feed within next week
 * last_week_last  -> last_fed within last week
 * next_7_days     -> next_feed within next 7 days (including today)
 */

if ($filter === "this_week_next") {
  // Monday of this week 00:00:00 to next Monday 00:00:00
  $where[] = "next_feed >= DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)
              AND next_feed <  DATE_ADD(DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY), INTERVAL 7 DAY)";
}
elseif ($filter === "next_week_next") {
  // Monday of next week 00:00:00 to Monday after next
  $where[] = "next_feed >= DATE_ADD(DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY), INTERVAL 7 DAY)
              AND next_feed <  DATE_ADD(DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY), INTERVAL 14 DAY)";
}
elseif ($filter === "last_week_last") {
  // Monday of last week 00:00:00 to Monday of this week
  $where[] = "last_fed >= DATE_SUB(DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY), INTERVAL 7 DAY)
              AND last_fed <  DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)";
}
elseif ($filter === "next_7_days") {
  // now to now + 7 days
  $where[] = "next_feed >= NOW() AND next_feed < DATE_ADD(NOW(), INTERVAL 7 DAY)";
}
elseif ($filter === "last_7_days") {
  // last 7 days based on last_fed
  $where[] = "last_fed >= DATE_SUB(NOW(), INTERVAL 7 DAY) AND last_fed <= NOW()";
}

$whereSql = "";
if (!empty($where)) $whereSql = "WHERE " . implode(" AND ", $where);

/** ✅ Count */
$countSql = "SELECT COUNT(*) AS total FROM bee_feeding_schedule $whereSql";
$countStmt = mysqli_prepare($link, $countSql);
if (!$countStmt) {
  http_response_code(500);
  echo json_encode(["error" => "Prepare failed (count): " . mysqli_error($link)]);
  exit;
}
if (!empty($params)) mysqli_stmt_bind_param($countStmt, $types, ...$params);
mysqli_stmt_execute($countStmt);
$countRes = mysqli_stmt_get_result($countStmt);
$totalRows = (int)(mysqli_fetch_assoc($countRes)['total'] ?? 0);
mysqli_stmt_close($countStmt);

$totalPages = (int)ceil($totalRows / $limit);

/** ✅ Data query */
$dataSql = "SELECT
              id,
              user_id,
              interval_minutes,
              next_feed,
              last_fed,
              fed_by_user_id,
              fed_at,
              created_at
            FROM bee_feeding_schedule
            $whereSql
            ORDER BY id DESC
            LIMIT ? OFFSET ?";

$dataStmt = mysqli_prepare($link, $dataSql);
if (!$dataStmt) {
  http_response_code(500);
  echo json_encode(["error" => "Prepare failed (data): " . mysqli_error($link)]);
  exit;
}

if (!empty($params)) {
  $types2 = $types . "ii";
  $params2 = array_merge($params, [$limit, $offset]);
  mysqli_stmt_bind_param($dataStmt, $types2, ...$params2);
} else {
  mysqli_stmt_bind_param($dataStmt, "ii", $limit, $offset);
}

mysqli_stmt_execute($dataStmt);
$res = mysqli_stmt_get_result($dataStmt);

$rows = [];
while ($r = mysqli_fetch_assoc($res)) $rows[] = $r;

mysqli_stmt_close($dataStmt);
mysqli_close($link);

echo json_encode([
  "current_page" => $page,
  "total_pages"  => max(1, $totalPages),
  "total_rows"   => $totalRows,
  "data"         => $rows
]);