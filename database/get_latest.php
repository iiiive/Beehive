<?php
require_once "../config.php";
header("Content-Type: application/json; charset=utf-8");

date_default_timezone_set('Asia/Manila');
mysqli_query($link, "SET time_zone = '+08:00'");

// -------------------------
// Helper: check if table exists
// -------------------------
function table_exists($link, $tableName) {
  $tableNameSafe = mysqli_real_escape_string($link, $tableName);
  $q = mysqli_query($link, "SHOW TABLES LIKE '{$tableNameSafe}'");
  return ($q && mysqli_num_rows($q) > 0);
}

// -------------------------
// 1) Choose correct table (support both names)
// -------------------------
$table = null;
if (table_exists($link, "beehive_reading")) {
  $table = "beehive_reading";
} elseif (table_exists($link, "beehive_readings")) {
  $table = "beehive_readings";
} else {
  http_response_code(500);
  echo json_encode(["error" => "No beehive readings table found (beehive_reading / beehive_readings)."]);
  mysqli_close($link);
  exit;
}

// -------------------------
// 2) Detect columns (some tables may not have actuator fields)
// -------------------------
$colsRes = mysqli_query($link, "SHOW COLUMNS FROM `$table`");
$cols = [];
if ($colsRes) {
  while ($c = mysqli_fetch_assoc($colsRes)) $cols[] = $c['Field'];
}

$hasFan    = in_array("fan_status", $cols);
$hasMist   = in_array("mist_status", $cols);
$hasHeater = in_array("heater_status", $cols);
$hasStatus = in_array("status", $cols);
$hasRid    = in_array("reading_id", $cols);
$hasTs     = in_array("timestamp", $cols);

// Fallback ordering: reading_id if exists else timestamp
$orderBy = $hasRid ? "reading_id DESC" : ($hasTs ? "timestamp DESC" : "1 DESC");

// Build select safely
$select = [];
$select[] = $hasTs ? "timestamp" : "NULL AS timestamp";
$select[] = "temperature";
$select[] = "humidity";
$select[] = "weight";
$select[] = $hasFan ? "fan_status" : "0 AS fan_status";
$select[] = $hasMist ? "mist_status" : "0 AS mist_status";
$select[] = $hasHeater ? "heater_status" : "0 AS heater_status";
$select[] = $hasStatus ? "status" : "'OK' AS status";

$sql = "SELECT " . implode(", ", $select) . " FROM `$table` ORDER BY $orderBy LIMIT 1";
$res = mysqli_query($link, $sql);

if (!$res || mysqli_num_rows($res) === 0) {
  echo json_encode(["error" => "No readings found"]);
  mysqli_close($link);
  exit;
}

$row = mysqli_fetch_assoc($res);

// Normalize
$temperature = isset($row['temperature']) ? (float)$row['temperature'] : 0.0;
$humidity    = isset($row['humidity']) ? (float)$row['humidity'] : 0.0;
$weight      = isset($row['weight']) ? (float)$row['weight'] : 0.0;

$fan_status    = isset($row['fan_status']) ? (int)$row['fan_status'] : 0;
$mist_status   = isset($row['mist_status']) ? (int)$row['mist_status'] : 0;
$heater_status = isset($row['heater_status']) ? (int)$row['heater_status'] : 0;

$statusText = isset($row['status']) ? (string)$row['status'] : "OK";
$timestamp  = isset($row['timestamp']) ? (string)$row['timestamp'] : "";

// -------------------------
// 3) +20% Baseline fields (beehive_weight_baseline)
// -------------------------
$baseline_weight = 0.0;

if (table_exists($link, "beehive_weight_baseline")) {
  $baseRes = mysqli_query($link, "SELECT baseline_weight FROM beehive_weight_baseline WHERE id = 1 LIMIT 1");
  if ($baseRes && mysqli_num_rows($baseRes) > 0) {
    $baseline_weight = (float)mysqli_fetch_assoc($baseRes)['baseline_weight'];
  }
}

// If baseline not set, use current weight (non-breaking)
if ($baseline_weight <= 0) $baseline_weight = $weight;

$target_weight = $baseline_weight * 1.20;
$gain_kg = $weight - $baseline_weight;
$gain_pct = ($baseline_weight > 0) ? (($gain_kg / $baseline_weight) * 100.0) : 0.0;
$triggered_20 = ($weight >= $target_weight) ? 1 : 0;

mysqli_close($link);

// -------------------------
// Output JSON
// -------------------------
echo json_encode([
  "timestamp" => $timestamp,
  "temperature" => $temperature,
  "humidity" => $humidity,
  "weight" => $weight,
  "fan_status" => $fan_status,
  "mist_status" => $mist_status,
  "heater_status" => $heater_status,
  "status" => $statusText,

  // ✅ +20% baseline fields
  "baseline_weight" => $baseline_weight,
  "target_weight" => $target_weight,
  "gain_kg" => $gain_kg,
  "gain_pct" => $gain_pct,
  "triggered_20" => $triggered_20
], JSON_UNESCAPED_UNICODE);