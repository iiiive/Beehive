<?php
session_start();
include("../config.php");

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: user-login.php");
    exit;
}

$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 1;
$success = $error = "";

date_default_timezone_set('Asia/Manila');
mysqli_query($link, "SET time_zone = '+08:00'");

// === Hive readings (for charts + latest values) ===
$sql_all = "SELECT timestamp, temperature, humidity, weight, fan_status, mist_status, heater_status
            FROM beehive_readings 
            ORDER BY timestamp ASC";
$result_all = mysqli_query($link, $sql_all);

$timestamps   = [];
$temperatures = [];
$humidities   = [];
$weights      = [];
$fans         = [];
$mists        = [];
$heaters      = [];

while ($row = mysqli_fetch_assoc($result_all)) {
    $timestamps[]   = $row['timestamp'];
    $temperatures[] = $row['temperature'];
    $humidities[]   = $row['humidity'];
    $weights[]      = $row['weight'];
    $fans[]         = $row['fan_status'];
    $mists[]        = $row['mist_status'];
    $heaters[]      = $row['heater_status'];
}

$latestTemp   = end($temperatures);
$latestHum    = end($humidities);
$latestWeight = end($weights);
$latestFan    = end($fans);
$latestMist   = end($mists);
$latestHeater = end($heaters);

$temperature_history = $temperatures;
$humidity_history    = $humidities;
$weight_history      = $weights;

// === Last 5 readings for history table (excluding latest) ===
$sql_last5 = "SELECT timestamp, temperature, humidity, weight, 
                     fan_status, mist_status, heater_status, status
              FROM beehive_readings 
              ORDER BY timestamp DESC 
              LIMIT 6";
$result_last5 = mysqli_query($link, $sql_last5);

$history_rows = [];
while ($row = mysqli_fetch_assoc($result_last5)) {
    $history_rows[] = $row;
}
array_shift($history_rows); // remove very latest row (already shown in cards)

// === Fetch user feeding schedule ===
$sql_feed = "SELECT * FROM bee_feeding_schedule WHERE user_id = $user_id LIMIT 1";
$result_feed = mysqli_query($link, $sql_feed);
$feeding = mysqli_fetch_assoc($result_feed);

$now = new DateTime();
$next_feed   = $feeding ? new DateTime($feeding['next_feed']) : null;
$time_diff   = $next_feed ? $next_feed->getTimestamp() - $now->getTimestamp() : null;
$needs_feeding = ($time_diff !== null && $time_diff <= 0);

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HiveCare - User Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
* { box-sizing:border-box; margin:0; padding:0; font-family:Raleway,sans-serif; }
body {
  min-height: 100vh;
  background: url('https://a-z-animals.com/media/2025/08/shutterstock-2374833763-huge-licensed-scaled.jpg') no-repeat center center/cover;
  position: relative;
  margin: 0; 
  padding: 0;
  color: #212121;
}
body::before {
  content: "";
  position: absolute; inset: 0;
  background-color: rgba(0,0,0,0.4);
  z-index: 0;
}
.container, .dashboard-header, .card{ 
  position: relative; 
  z-index: 1; 
}

.dashboard-header {
  width: 100%;
  padding: 12px 20px;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  background: linear-gradient(145deg, #eef104ff, #D4A373);
  border-radius: 0 0 20px 20px;
  box-shadow: 6px 6px 20px rgba(0,0,0,0.35);
  z-index: 2;
  position: relative;
}

.dashboard-header .title {
  display: flex;
  align-items: center;
  gap: 12px;
  flex: 1 1 auto;
  min-width: 0;
}

.dashboard-header img {
  width: 60px;
  height: 60px;
  flex-shrink: 0;
}

.dashboard-header .title span {
  font-family: 'Brush Script MT', cursive, sans-serif;
  font-size: 2.2rem;
  font-weight: 500;
  color: #212121;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Action buttons */
.header-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  justify-content: flex-end;
}

.settings-btn,
.logout-btn {
  padding: 8px 16px;
  border-radius: 14px;
  font-weight: 700;
  font-size: 0.9rem;
  color: #fff;
  background: #4B2E1E;
  border: none;
  text-decoration: none;
  white-space: nowrap;
  box-shadow: 0 5px 15px rgba(0,0,0,0.3);
  transition: 0.3s;
}

.settings-btn:hover,
.logout-btn:hover {
  background: #6B4226;
  transform: translateY(-2px) scale(1.03);
}


/* Layout */
.container {
  max-width:1100px;
  margin:40px auto;
  display:flex; flex-wrap:wrap;
  justify-content:center; gap:20px;
}

/* Metric Cards */
.card {
  flex:1 1 300px; 
  min-width:280px;
  background: linear-gradient(145deg, #FFF8DC, #9b8c51ff);
  border-radius:25px; 
  border:none;
  padding:25px; 
  text-align:center;
  box-shadow: 8px 8px 20px rgba(0,0,0,0.3), -5px -5px 15px rgba(255,255,255,0.5);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card-title {
  font-weight:700; font-size:1.5rem; margin-bottom:15px;
  display:flex; justify-content:center; align-items:center; gap:10px;
  color:#4B2E1E; text-shadow:1px 1px 2px rgba(0,0,0,0.3);
}
.value {
  font-size:2rem; font-weight:bold; margin-bottom:10px;
  color:#4B2E1E; text-shadow:1px 1px 3px rgba(0,0,0,0.3);
}
.status-good, .status-bad {
  border-radius:15px; padding:10px 20px;
  font-size:1rem; font-weight:700; margin-top:10px;
  display:inline-block; box-shadow:0 4px 10px rgba(0,0,0,0.2);
}
.status-good { background:#ffd83dd8; color:#4b2e1e; }
.status-bad { background:#d2691ed2; color:#FFF; }

canvas { margin-top:20px; height:120px !important; }

/* RESTORED — Original History Log Design */
.history-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 5px;
  margin-top: 20px;
  border-radius: 30px;
  overflow: hidden;
  background: #fff8dc8c !important;
  box-shadow: 0 6px 18px rgba(0,0,0,0.2);
}
.history-table thead {
  background: linear-gradient(135deg, #FFD93D, #E8C547) !important;
  color: #4B2E1E !important;
}
.history-table th,
.history-table td {
  padding: 14px 12px !important;
  text-align: center;
  font-weight: bold;
  border-right: 2px solid #4B2E1E; 
}
.history-table tbody tr:nth-child(even) {
  background: #FFF2A3 !important;
}
.history-table tbody tr:hover {
  background: #FEDE16 !important;
  transform: scale(1.01);
}

/* =========================
   RESPONSIVE
   ========================= */
@media (max-width: 992px) {
  .dashboard-header {
    flex-direction: column;
    align-items: stretch;
    gap: 10px;
  }
  .dashboard-header .title {
    justify-content: center;
    margin-bottom: 8px;
  }
  .dashboard-header img {
    width: 50px;
    height: 50px;
  }
  .dashboard-header .title span {
    font-size: 1.8rem;
    text-align: center;
  }
  .header-actions {
    justify-content: center;
    flex-wrap: wrap;
  }
  .settings-btn,
  .logout-btn {
    padding: 7px 12px;
    font-size: 0.85rem;
  }
}

@media (max-width: 480px) {
  .dashboard-header .title span {
    font-size: 1.4rem;
  }
  .settings-btn,
  .logout-btn {
    font-size: 0.8rem;
    padding: 6px 10px;
  }
}
/* 🐝 Bee Feeding Status Card */
.feeding-card {
  background: linear-gradient(145deg, #FFF8DC, #EED484);
  border: 2px solid #E3B23C;
  border-radius: 25px;
  box-shadow: 6px 6px 20px rgba(0,0,0,0.25);
  transition: 0.3s ease;
}
#feeding-status-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
  margin-top: 15px;
}
.feed-card {
  padding: 20px;
  border-radius: 20px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  transition: 0.3s ease;
  border-left: 6px solid;
  position: relative;
  overflow: hidden;
}
.feed-hungry {
  background: linear-gradient(145deg, #FFEAEA, #FFB6B6);
  border-left-color: #E63946;
  box-shadow: 4px 6px 16px rgba(230, 57, 70, 0.3);
}
.feed-hungry::before {
  content: "⚠️ Hungry Alert!";
  position: absolute;
  top: 10px;
  right: 15px;
  font-weight: 700;
  color: #B22222;
}
.feed-eating {
  background: linear-gradient(145deg, #E8FFE8, #C4F2C4);
  border-left-color: #2A9D8F;
  box-shadow: 4px 6px 16px rgba(42, 157, 143, 0.3);
}
.feed-eating::before {
  content: "🍯 Feeding Time";
  position: absolute;
  top: 10px;
  right: 15px;
  font-weight: 700;
  color: #1E5631;
}
.feed-card h6 {
  font-weight: 800;
  color: #4B2E1E;
  margin-bottom: 5px;
}
.feed-card p {
  margin: 0;
  font-size: 1rem;
  font-weight: 600;
}
.feed-card small {
  color: #4B2E1E;
  display: block;
  font-weight: 600;
  font-size: 0.9rem;
}
.countdown {
  font-weight: bold;
  color: #4B2E1E;
  background: rgba(255,255,255,0.5);
  padding: 4px 10px;
  border-radius: 10px;
  display: inline-block;
  margin-top: 5px;
}
</style>
</head>
<body>

<div class="dashboard-header">
  <div class="title">
    <img src="../frontend/images/bee.png" alt="HiveCare Logo">
    <span>HiveCare - User Dashboard</span>
  </div>

  <div class="header-actions">
    <a href="https://discord.com/channels/1416994358464483481/1425437614458273792" 
       class="logout-btn" 
       target="_blank" 
       rel="noopener noreferrer">
       <i class="bi bi-chat-dots"></i> Need help?
    </a>
    <a href="set_feeding_time.php" class="logout-btn">
      <i class="bi bi-clock-fill"></i> Set Feeding Time
    </a>
    <a href="user-profile.php" class="settings-btn">
      <i class="bi bi-person-fill"></i> Edit Profile
    </a>
    <a href="homepage.php" class="logout-btn">
      <i class="bi bi-box-arrow-right"></i> Logout
    </a>
  </div>
</div>


<div class="container">
  <!-- Temperature -->
  <div class="card">
    <h5 class="card-title"><i class="bi bi-thermometer-half" style="color:#D2691E;"></i> Temperature</h5>
    <div id="temp-value" class="value"><?php echo $latestTemp; ?> °C</div>
    <div id="temp-status" class="<?php echo ($latestTemp>25.90||$latestTemp<22.30)?'status-bad':'status-good';?>">
      <?php echo ($latestTemp>25.90||$latestTemp<22.30)?'Temperature is Bad ✖':'Temperature is Good ✔';?>
    </div>
    <canvas id="tempChart"></canvas>
  </div>

  <!-- Humidity -->
  <div class="card">
    <h5 class="card-title"><i class="bi bi-droplet" style="color:#4B2E1E;"></i> Humidity</h5>
    <div id="hum-value" class="value"><?php echo $latestHum; ?> %</div>
    <div id="hum-status" class="<?php echo ($latestHum>=79.20&&$latestHum<=86.40)?'status-good':'status-bad';?>">
      <?php echo ($latestHum>=79.20&&$latestHum<=86.40)?'Humidity is Good ✔':'Humidity is Bad ✖';?>
    </div>
    <canvas id="humChart"></canvas>
  </div>

  <!-- Weight -->
  <div class="card">
    <h5 class="card-title"><i class="bi bi-box-seam" style="color:#FFD93D;"></i> Weight</h5>
    <div id="weight-value" class="value"><?php echo $latestWeight; ?> kg</div>
    <div id="weight-status" class="<?php echo ($latestWeight>=5)?'status-good':'status-bad';?>">
      <?php echo ($latestWeight>=5)?'The Hive is Heavy!':'The Hive is still Light';?>
    </div>
    <canvas id="weightChart"></canvas>
  </div>

  <!-- Exhaust Fan -->
  <div class="card">
    <h5 class="card-title"><i class="bi bi-fan" style="color:#FFD93D;"></i> Exhaust Fan</h5>
    <div id="fan-value" class="value">
      <?= ($latestFan==1) ? "ON" : "OFF" ?>
    </div>
    <div id="fan-status" class="<?= ($latestFan==1)?'status-good':'status-bad' ?>">
      <?= ($latestFan==1)?'Exhaust Fan is Running ✔':'Exhaust Fan is Off ✖' ?>
    </div>
  </div>

  <!-- Heater -->
  <div class="card">
    <h5 class="card-title"><i class="bi bi-fire" style="color:#D2691E;"></i> Heater</h5>
    <div id="heater-value" class="value">
      <?= ($latestHeater==1) ? "ON" : "OFF" ?>
    </div>
    <div id="heater-status" class="<?= ($latestHeater==1)?'status-good':'status-bad' ?>">
      <?= ($latestHeater==1)?'Heater is ON ✔':'Heater is OFF ✖' ?>
    </div>
  </div>

  <!-- Mist -->
  <div class="card">
    <h5 class="card-title"><i class="bi bi-cloud-rain-fill" style="color:#4B2E1E;"></i> Mist</h5>
    <div id="mist-value" class="value">
      <?= ($latestMist==1) ? "ON" : "OFF" ?>
    </div>
    <div id="mist-status" class="<?= ($latestMist==1)?'status-good':'status-bad' ?>">
      <?= ($latestMist==1)?'Misting is Active ✔':'Misting is Off ✖' ?>
    </div>
  </div>

  <!-- Feeding Scheduler Card -->
  <div class="card feeding-card">
    <h5 class="card-title">
      <i class="bi bi-hourglass-split" style="color:#FFD93D;"></i> Feeding Scheduler
    </h5>

    <div id="feeding-area">
      <div id="feeding-status" class="status-good"></div>
      <div class="countdown-container">
        <span id="countdown" class="countdown-text"></span>
      </div>
      <button id="feed-done-btn" class="feed-btn" style="display:none;">
        <i class="bi bi-check-circle"></i> Feed Done
      </button>
      <div class="mt-3 d-flex justify-content-center gap-2 flex-wrap">
  <button id="pause-btn" class="feed-btn" type="button" style="display:none;">
    <i class="bi bi-pause-circle"></i> Pause
  </button>

  <button id="resume-btn" class="feed-btn" type="button" style="display:none;">
    <i class="bi bi-play-circle"></i> Resume
  </button>

  <button id="stop-btn" class="feed-btn" type="button" style="display:none;">
    <i class="bi bi-stop-circle"></i> Stop
  </button>
</div>

    </div>
  </div>
</div>

<!-- History Log Section -->
<div class="card p-4 mt-4">
  <h4 class="card-title"><i class="bi bi-clock-history"></i> History Log </h4>
  <div class="table-responsive">
    <table class="history-table">
      <thead class="table-warning">
        <tr>
          <th>Timestamp</th>
          <th>Temperature (°C)</th>
          <th>Humidity (%)</th>
          <th>Weight (kg)</th>
          <th>Exhaust Fan</th>
          <th>Mist</th>
          <th>Heater</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody id="history-body">
        <?php foreach ($history_rows as $row): ?>
          <tr>
            <td><?= $row['timestamp'] ?></td>
            <td><?= $row['temperature'] ?></td>
            <td><?= $row['humidity'] ?></td>
            <td><?= $row['weight'] ?></td>
            <td><?= $row['fan_status']   == 1 ? 'ON' : 'OFF'; ?></td>
            <td><?= $row['mist_status']  == 1 ? 'ON' : 'OFF'; ?></td>
            <td><?= $row['heater_status']== 1 ? 'ON' : 'OFF'; ?></td>
            <td><?= $row['status'] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
const tempData   = <?php echo json_encode($temperature_history); ?>;
const humData    = <?php echo json_encode($humidity_history); ?>;
const weightData = <?php echo json_encode($weight_history); ?>;

function create3DChart(id, data, color) {
  new Chart(document.getElementById(id), {
    type:'line',
    data:{ 
      labels:data.map((_,i)=>i+1),
      datasets:[{
        data,
        borderColor:color,
        backgroundColor:color+'55',
        fill:true,
        tension:0.4,
        pointRadius:4,
        pointBackgroundColor:color,
        pointHoverRadius:6,
        borderWidth:3
      }]
    },
    options:{ 
      responsive:true,
      maintainAspectRatio:false,
      plugins:{ legend:{ display:false } },
      scales:{ 
        x:{ display:false }, 
        y:{ beginAtZero:false } 
      } 
    }
  });
}

create3DChart('tempChart',   tempData,   '#D2691E');
create3DChart('humChart',    humData,    '#4B2E1E');
create3DChart('weightChart', weightData, '#4B2E1E');

function updateStatus(id, obj) {
  const el = document.getElementById(id);
  if (!el) return;
  el.className = obj.cls;
  el.innerText = obj.text;
}

async function reloadValues() {
  try {
    const response = await fetch("get_latest.php"); 
    const data = await response.json();

    // numeric values
    if (document.getElementById("temp-value") && data.temperature !== undefined)
      document.getElementById("temp-value").innerText   = data.temperature + " °C";
    if (document.getElementById("hum-value") && data.humidity !== undefined)
      document.getElementById("hum-value").innerText    = data.humidity + " %";
    if (document.getElementById("weight-value") && data.weight !== undefined)
      document.getElementById("weight-value").innerText = data.weight + " kg";
    if (document.getElementById("fan-value") && data.fan_status !== undefined)
      document.getElementById("fan-value").innerText    = (data.fan_status == 1 ? "ON" : "OFF");

    // Temperature status
    if (data.temperature !== undefined) {
      updateStatus("temp-status",
        (data.temperature >= 22.30 && data.temperature <= 25.90)
          ? {text:"Temperature is Good ✔", cls:"status-good"}
          : {text:"Temperature is Bad ✖",  cls:"status-bad"}
      );
    }

    // Humidity status
    if (data.humidity !== undefined) {
      updateStatus("hum-status",
        (data.humidity >=79.20 && data.humidity <= 86.40)
          ? {text:"Humidity is Good ✔", cls:"status-good"}
          : {text:"Humidity is Bad ✖",  cls:"status-bad"}
      );
    }

    // Weight status
    if (data.weight !== undefined) {
      updateStatus("weight-status",
        (data.weight >= 5)
          ? {text:"The Hive is Heavy!", cls:"status-good"}
          : {text:"The Hive is still Light", cls:"status-bad"}
      );
    }

    // Exhaust Fan status
    if (data.fan_status !== undefined) {
      updateStatus("fan-status",
        (data.fan_status == 1)
          ? {text:"Exhaust Fan is Running ✔", cls:"status-good"}
          : {text:"Exhaust Fan is Off ✖",    cls:"status-bad"}
      );
    }

    // Heater status
    if (data.heater_status !== undefined) {
      if (document.getElementById("heater-value")) {
        document.getElementById("heater-value").innerText =
          (data.heater_status == 1 ? "ON" : "OFF");
      }
      updateStatus("heater-status",
        (data.heater_status == 1)
          ? {text:"Heater is ON ✔", cls:"status-good"}
          : {text:"Heater is OFF ✖", cls:"status-bad"}
      );
    }

    // Mist status
    if (data.mist_status !== undefined) {
      if (document.getElementById("mist-value")) {
        document.getElementById("mist-value").innerText =
          (data.mist_status == 1 ? "ON" : "OFF");
      }
      updateStatus("mist-status",
        (data.mist_status == 1)
          ? {text:"Misting is Active ✔", cls:"status-good"}
          : {text:"Misting is Off ✖",    cls:"status-bad"}
      );
    }

  } catch (err) {
    console.error("Error fetching latest data:", err);
  }
}

// Run immediately + every 5 seconds
reloadValues();
setInterval(reloadValues, 5000);

async function reloadHistory() {
  try {
    const res = await fetch("get_history.php");
    const data = await res.json();

    const tbody = document.getElementById("history-body");
    tbody.innerHTML = ""; // clear old rows

    data.forEach(row => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${row.timestamp}</td>
        <td>${row.temperature}</td>
        <td>${row.humidity}</td>
        <td>${row.weight}</td>
        <td>${row.fan_status == 1 ? "ON" : "OFF"}</td>
        <td>${row.mist_status == 1 ? "ON" : "OFF"}</td>
        <td>${row.heater_status == 1 ? "ON" : "OFF"}</td>
        <td>${row.status}</td>
      `;
      tbody.appendChild(tr);
    });
  } catch (err) {
    console.error("History fetch error:", err);
  }
}

reloadHistory();
setInterval(reloadHistory, 5000);

/* ==================== FEEDING SCHEDULER ==================== */

const feedingStatusEl = document.getElementById("feeding-status");
const countdownEl     = document.getElementById("countdown");
const feedDoneBtn     = document.getElementById("feed-done-btn");

const pauseBtn  = document.getElementById("pause-btn");
const resumeBtn = document.getElementById("resume-btn");
const stopBtn   = document.getElementById("stop-btn");

let hungerAlertInterval = null;

// ---- Persistent keys ----
const FEED_KEY = "hivecare_feed_state_v1"; 
// stored shape:
// {
//   mode: "running" | "paused" | "stopped",
//   pausedRemainingMs: number|null,
//   stoppedForNextFeed: string|null,  // the next_feed string we stopped on
// }

function loadState() {
  try {
    const s = JSON.parse(localStorage.getItem(FEED_KEY));
    if (!s || !s.mode) return { mode: "running", pausedRemainingMs: null, stoppedForNextFeed: null };
    return {
      mode: s.mode,
      pausedRemainingMs: typeof s.pausedRemainingMs === "number" ? s.pausedRemainingMs : null,
      stoppedForNextFeed: typeof s.stoppedForNextFeed === "string" ? s.stoppedForNextFeed : null
    };
  } catch {
    return { mode: "running", pausedRemainingMs: null, stoppedForNextFeed: null };
  }
}

function saveState(obj) {
  localStorage.setItem(FEED_KEY, JSON.stringify(obj));
}

let state = loadState();

// ---- Timer runtime ----
let targetTs = null;           // ms timestamp end time (when running)
let pausedRemainingMs = state.pausedRemainingMs ?? 0;

let latestNextFeedStr = null;  // raw string from API
let lastShownSec = null;       // used to update UI only when second changes

function safeParseTimestamp(str) {
  if (!str) return NaN;
  // MySQL "YYYY-MM-DD HH:MM:SS" -> safer ISO-like
  const normalized = str.replace(" ", "T");
  const ts = Date.parse(normalized);
  return isNaN(ts) ? NaN : ts;
}

// 🔔 Single hungry notification (alert + Discord)
function notifyHungryOnce() {
  alert("The bees are hungry! Time to feed them 🍯");
  fetch("check_feeding_status.php").catch(() => {});
}

function startHungerAlerts() {
  if (hungerAlertInterval) return;
  notifyHungryOnce();
  hungerAlertInterval = setInterval(() => {
    notifyHungryOnce();
  }, 600000); // 10 minutes
}

function stopHungerAlerts() {
  if (hungerAlertInterval) {
    clearInterval(hungerAlertInterval);
    hungerAlertInterval = null;
  }
}

function showControls({ pause, resume, stop, feedDone }) {
  pauseBtn.style.display   = pause ? "inline-block" : "none";
  resumeBtn.style.display  = resume ? "inline-block" : "none";
  stopBtn.style.display    = stop ? "inline-block" : "none";
  feedDoneBtn.style.display= feedDone ? "inline-block" : "none";
}

function renderSeconds(diffMs) {
  const diff = Math.max(0, diffMs);

  const days    = Math.floor(diff / (1000 * 60 * 60 * 24));
  const hours   = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
  const seconds = Math.floor((diff % (1000 * 60)) / 1000);

  countdownEl.innerText = `${days}d ${hours}h ${minutes}m ${seconds}s`;
}

// -------------------- UI Modes --------------------

function setNoScheduleUI() {
  feedingStatusEl.innerText = "⚠ No feeding schedule set.";
  feedingStatusEl.className = "status-bad";
  countdownEl.innerText = "";
  showControls({ pause:false, resume:false, stop:false, feedDone:false });
  stopHungerAlerts();
}

function setStoppedUI() {
  feedingStatusEl.innerText = "🐝 Timer stopped. Click Feed Done after feeding.";
  feedingStatusEl.className = "status-bad";
  countdownEl.innerText = "";

  // ONLY Feed Done (no Stop / Pause / Resume)
  showControls({ pause:false, resume:false, stop:false, feedDone:true });

  stopHungerAlerts();
}



function setPausedUI() {
  feedingStatusEl.innerText = "⏸ Feeding timer paused";
  feedingStatusEl.className = "status-bad";
  showControls({ pause:false, resume:true, stop:true, feedDone:false });
  renderSeconds(pausedRemainingMs);
  stopHungerAlerts();
}

function setRunningUI(diffMs) {
  feedingStatusEl.innerText = "🍯 Bees are eating";
  feedingStatusEl.className = "status-good";

  // Stop only visible while running
  showControls({ pause:true, resume:false, stop:true, feedDone:false });

  renderSeconds(diffMs);
  stopHungerAlerts();
}


function setHungryUI() {
  feedingStatusEl.innerText = "🐝 Bees are hungry! Feed them now.";
  feedingStatusEl.className = "status-bad";
  countdownEl.innerText = "";

  // Only Feed Done when hungry
  showControls({ pause:false, resume:false, stop:false, feedDone:true });

  startHungerAlerts();
}


// -------------------- Stable Ticker (NO restarting intervals) --------------------
// This runs continuously and updates the UI only when the displayed SECOND changes.
function tick() {
  // STOP mode: do nothing unless DB schedule changes (handled in fetch sync)
  if (state.mode === "stopped") {
    setStoppedUI();
    requestAnimationFrame(tick);
    return;
  }

  if (state.mode === "hungry") {
  setHungryUI();
  requestAnimationFrame(tick);
  return;
}

  // no schedule
  if (!latestNextFeedStr) {
    setNoScheduleUI();
    requestAnimationFrame(tick);
    return;
  }

  // if paused
  if (state.mode === "paused") {
    const showSec = Math.floor(pausedRemainingMs / 1000);
    if (showSec !== lastShownSec) {
      lastShownSec = showSec;
      setPausedUI();
    }
    requestAnimationFrame(tick);
    return;
  }

  // running
  if (!targetTs) {
    // if we have next feed but no targetTs yet
    const parsed = safeParseTimestamp(latestNextFeedStr);
    if (!isNaN(parsed)) targetTs = parsed;
  }

  if (!targetTs) {
    setNoScheduleUI();
    requestAnimationFrame(tick);
    return;
  }

  const diffMs = targetTs - Date.now();
  const showSec = Math.floor(diffMs / 1000);

  if (diffMs <= 0) {
    if (lastShownSec !== 0) {
      lastShownSec = 0;
      setHungryUI();
    }
    requestAnimationFrame(tick);
    return;
  }

  if (showSec !== lastShownSec) {
    lastShownSec = showSec;
    setRunningUI(diffMs);
  }

  requestAnimationFrame(tick);
}

// -------------------- Fetch Sync --------------------
// IMPORTANT: Poll less often. 1s polling causes jitter + unnecessary load.
// 5s is enough. Your countdown stays smooth because tick() runs locally.
async function fetchFeedingData() {
  const res = await fetch("get_next_feed.php");
  const data = await res.json();

  if (!data.has_schedule) {
    latestNextFeedStr = null;
    targetTs = null;
    setNoScheduleUI();
    return;
  }

  latestNextFeedStr = data.next_feed;

  if (data.timer_state === "paused" || data.timer_state === "stopped") {
    pausedRemainingMs = (data.remaining_seconds || 0) * 1000;
    state.mode = data.timer_state; // paused/stopped
    targetTs = null;               // IMPORTANT: don't rebuild from next_feed
    return;
  }

  if (data.timer_state === "hungry") {
    state.mode = "hungry";
    targetTs = null;
    return;
  }

  // running
  state.mode = "running";
  targetTs = safeParseTimestamp(data.next_feed);
}



async function notifyDiscord(msg) {
  try {
    await fetch("discord_alertfeed.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({ message: msg })
    });
  } catch (e) {
    console.error("Discord notify failed:", e);
  }
}

async function updateTimerState(state, remainingSeconds = 0) {
  await fetch("update_timer.php", {
    method: "POST",
    headers: {"Content-Type":"application/x-www-form-urlencoded"},
    body: new URLSearchParams({
      state,
      remaining_seconds: remainingSeconds
    })
  });
}





// -------------------- Buttons --------------------

pauseBtn.addEventListener("click", async () => {
  if (state.mode !== "running" || !targetTs) return;

  const remainingSeconds = Math.max(0, Math.ceil((targetTs - Date.now()) / 1000));
  await updateTimerState("paused", remainingSeconds);

  await fetchFeedingData();
  lastShownSec = null;

  alert("⏸ Feeding timer was paused.");
  await notifyDiscord("⏸ Feeding timer was PAUSED by the user.");
});



resumeBtn.addEventListener("click", async () => {
  if (state.mode !== "paused") return;

  const remainingSeconds = Math.max(0, Math.ceil(pausedRemainingMs / 1000));
  await updateTimerState("running", remainingSeconds);

  await fetchFeedingData();
  lastShownSec = null;

  alert("▶ Feeding timer resumed.");
  await notifyDiscord("▶ Feeding timer was RESUMED by the user.");
});





stopBtn.addEventListener("click", async () => {
  let remainingSeconds = 0;

  if (state.mode === "running" && targetTs) {
    remainingSeconds = Math.max(0, Math.ceil((targetTs - Date.now()) / 1000));
  } else if (state.mode === "paused") {
    remainingSeconds = Math.max(0, Math.ceil(pausedRemainingMs / 1000));
  }

  await updateTimerState("stopped", remainingSeconds);

  await fetchFeedingData();
  lastShownSec = null;

  alert("⏹ Feeding timer was stopped.");
  await notifyDiscord("⏹ Feeding timer was STOPPED by the user.");
});






// Feed Done button → stop alerts + trigger backend update
feedDoneBtn.addEventListener("click", async () => {
  try {
    feedDoneBtn.disabled = true;

    const res = await fetch("feed_done.php", { method: "POST" });
    const out = await res.json();
    if (!out.ok) throw new Error(out.error || "Feed done failed");

    stopHungerAlerts();
    await notifyDiscord("✅ Feeding marked as DONE.");

    await fetchFeedingData(); // re-sync from DB
    lastShownSec = null;

  } catch (e) {
    console.error("Feed done error:", e);
    alert("Feed Done failed: " + e.message);
  } finally {
    feedDoneBtn.disabled = false;
  }
});




// ---- Start ----
fetchFeedingData();
setInterval(fetchFeedingData, 5000); // ✅ less jitter, less load
requestAnimationFrame(tick);



</script>


</body>
</html>
