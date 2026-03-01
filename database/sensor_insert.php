<?php
require_once "config.php"; // $link = mysqli connection

// -------------------------
// 0) Helpers
// -------------------------
function post_str($key, $default = "") {
  return isset($_POST[$key]) ? trim((string)$_POST[$key]) : $default;
}
function post_float($key, $default = 0.0) {
  return isset($_POST[$key]) ? (float)$_POST[$key] : (float)$default;
}
function onoff_to_int($v) {
  $v = strtoupper(trim((string)$v));
  return ($v === "ON" || $v === "1" || $v === "TRUE") ? 1 : 0;
}
function out_of_range($v, $min, $max) {
  return ($v < $min || $v > $max);
}

// -------------------------
// 1) Read POST data (ESP32)
// -------------------------
$temperature = post_float('temperature', 0.0);
$humidity    = post_float('humidity', 0.0);
$weight      = post_float('weight', 0.0);

// Accept either fan_status/mist_status/heater_status OR old pump_status key
$fan_status_raw    = post_str('fan_status', 'OFF');
$mist_status_raw   = post_str('mist_status', post_str('pump_status', 'OFF')); // backward compatible
$heater_status_raw = post_str('heater_status', 'OFF');

// Convert to tinyint(1)
$fan_status    = onoff_to_int($fan_status_raw);
$mist_status   = onoff_to_int($mist_status_raw);
$heater_status = onoff_to_int($heater_status_raw);

// Optional: human readable status column
$status_text = "OK";

// -------------------------
// 2) Insert into database (SAFE)
// -------------------------
$sql_insert = "INSERT INTO beehive_reading
  (temperature, humidity, weight, fan_status, mist_status, heater_status, status)
  VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($link, $sql_insert);
if (!$stmt) {
  http_response_code(500);
  die("Prepare failed: " . mysqli_error($link));
}

mysqli_stmt_bind_param(
  $stmt,
  "dddiiis",
  $temperature,
  $humidity,
  $weight,
  $fan_status,
  $mist_status,
  $heater_status,
  $status_text
);

if (!mysqli_stmt_execute($stmt)) {
  http_response_code(500);
  die("Insert failed: " . mysqli_stmt_error($stmt));
}

$insert_id = mysqli_insert_id($link);
mysqli_stmt_close($stmt);

// -------------------------
// 3) Fetch EXACT inserted record (same table!)
// -------------------------
$sql_latest = "SELECT reading_id, timestamp, temperature, humidity, weight, fan_status, mist_status, heater_status
              FROM beehive_reading
              WHERE reading_id = ?
              LIMIT 1";

$stmt2 = mysqli_prepare($link, $sql_latest);
if (!$stmt2) {
  http_response_code(500);
  die("Prepare failed: " . mysqli_error($link));
}
mysqli_stmt_bind_param($stmt2, "i", $insert_id);
mysqli_stmt_execute($stmt2);
$result = mysqli_stmt_get_result($stmt2);

if (!$result || mysqli_num_rows($result) === 0) {
  echo "Error fetching inserted record.";
  mysqli_close($link);
  exit;
}

$row = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt2);

$timestamp     = $row['timestamp'];
$temperatureDB = (float)$row['temperature'];
$humidityDB    = (float)$row['humidity'];
$weightDB      = (float)$row['weight'];
$fanDB         = (int)$row['fan_status'];
$mistDB        = (int)$row['mist_status'];
$heaterDB      = (int)$row['heater_status'];

// -------------------------
// 4) Get previous weight for sudden drop detection
// -------------------------
$prev_weight = null;
$prev_sql = "SELECT weight
             FROM beehive_reading
             WHERE weight IS NOT NULL
             ORDER BY reading_id DESC
             LIMIT 1,1";
$prev_result = mysqli_query($link, $prev_sql);
if ($prev_result && mysqli_num_rows($prev_result) > 0) {
  $prev_weight = (float)mysqli_fetch_assoc($prev_result)['weight'];
}

// -------------------------
// 5) Alert Rules (UPDATED thresholds)
// -------------------------
$TEMP_MIN = 25.0; $TEMP_MAX = 35.0;
$HUM_MIN  = 70.0; $HUM_MAX  = 90.0;
$W_MIN    = 5.0;  $W_MAX    = 8.0;
$SUDDEN_DROP_KG = 2.0;

$alerts = [];

// Convert int status to words for alerts
$fanText    = $fanDB ? "ON" : "OFF";
$mistText   = $mistDB ? "ON" : "OFF";
$heaterText = $heaterDB ? "ON" : "OFF";

// Temperature
if (out_of_range($temperatureDB, $TEMP_MIN, $TEMP_MAX)) {
  $side = ($temperatureDB > $TEMP_MAX) ? "HIGH" : "LOW";
  $alerts[] = "🌡️ **TEMP {$side} (OUT OF RANGE)** | {$temperatureDB}°C | {$timestamp}\nFan: {$fanText} | Mist: {$mistText} | Heater: {$heaterText}";
}

// Humidity
if (out_of_range($humidityDB, $HUM_MIN, $HUM_MAX)) {
  $side = ($humidityDB > $HUM_MAX) ? "HIGH" : "LOW";
  $alerts[] = "💧 **HUMIDITY {$side} (OUT OF RANGE)** | {$humidityDB}% | {$timestamp}\nFan: {$fanText} | Mist: {$mistText} | Heater: {$heaterText}";
}

// Weight
if (out_of_range($weightDB, $W_MIN, $W_MAX)) {
  $side = ($weightDB > $W_MAX) ? "HIGH" : "LOW";
  $alerts[] = "⚖️ **WEIGHT {$side} (OUT OF RANGE)** | {$weightDB}kg | {$timestamp}\nFan: {$fanText} | Mist: {$mistText} | Heater: {$heaterText}";
}

// Sudden drop
if ($prev_weight !== null && ($prev_weight - $weightDB) > $SUDDEN_DROP_KG) {
  $drop = $prev_weight - $weightDB;
  $alerts[] = "⚠️ **SUDDEN WEIGHT DROP** | -{$drop}kg | Prev: {$prev_weight}kg → Now: {$weightDB}kg | {$timestamp}";
}

// -------------------------
// 6) Send alerts to Discord
// -------------------------
if (!empty($alerts)) {
  $webhookurl = "https://discord.com/api/webhooks/1416997260431982674/H2otdDl8uB6uXYdbaAfSS8HqquYhgkjz2eNe58jaaZybra5V4H3i1M2pPYBKf5H7t6JD";

  foreach ($alerts as $alert) {
    $json_data = json_encode(["content" => $alert], JSON_UNESCAPED_UNICODE);

    $ch = curl_init($webhookurl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_exec($ch);
    curl_close($ch);
  }

  echo "Alerts sent to Discord.";
} else {
  echo "No alert conditions triggered.";
}

mysqli_close($link);
?>