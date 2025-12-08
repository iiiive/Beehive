<?php

session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit;
}

require_once "../config.php";

// Query 1: Get ALL readings for charts and latest values
$sql_all = "SELECT timestamp, temperature, humidity, weight, fan_status, heater_status, mist_status, status
            FROM beehive_readings 
            ORDER BY timestamp ASC";
$result_all = mysqli_query($link, $sql_all);

$timestamps   = [];
$temperatures = [];
$humidities   = [];
$weights      = [];
$fan_statuses = [];
$heater_stats = [];
$mist_stats   = [];
$statuses     = [];

while ($row = mysqli_fetch_assoc($result_all)) {
    $timestamps[]   = $row['timestamp'];
    $temperatures[] = $row['temperature'];
    $humidities[]   = $row['humidity'];
    $weights[]      = $row['weight'];
    $fan_statuses[] = $row['fan_status'];
    $heater_stats[] = $row['heater_status'] ?? 0;
    $mist_stats[]   = $row['mist_status']   ?? 0;
    $statuses[]     = $row['status'];
}

$latestTemp    = !empty($temperatures) ? end($temperatures) : 0;
$latestHum     = !empty($humidities)   ? end($humidities)   : 0;
$latestWeight  = !empty($weights)      ? end($weights)      : 0;
$latestFan     = !empty($fan_statuses) ? end($fan_statuses) : 0;
$latestHeater  = !empty($heater_stats) ? end($heater_stats) : 0;
$latestMist    = !empty($mist_stats)   ? end($mist_stats)   : 0;

// For charts
$temperature_history = $temperatures;
$humidity_history    = $humidities;
$weight_history      = $weights;

// Query 2: Get ONLY the last 5 previous readings (excluding the very latest one)
$sql_last5 = "SELECT timestamp, temperature, humidity, weight, fan_status, status 
              FROM beehive_readings 
              ORDER BY timestamp DESC 
              LIMIT 6";  // get 6: latest + 5 previous
$result_last5 = mysqli_query($link, $sql_last5);

$history_rows = [];
while ($row = mysqli_fetch_assoc($result_last5)) {
    $history_rows[] = $row;
}

// Remove the very latest row (first row in DESC order)
if (!empty($history_rows)) {
    array_shift($history_rows);
}

// Latest feeding record (for feeding status card)
$sql = "SELECT u.username, f.last_fed, f.next_feed
        FROM bee_feeding_schedule f
        JOIN users u ON f.fed_by_user_id = u.user_id
        ORDER BY f.id DESC
        LIMIT 1";

$result = mysqli_query($link, $sql);
$data = [];

if ($row = mysqli_fetch_assoc($result)) {
    $data = $row;
}

mysqli_close($link);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HiveCare - Super Admin Dashboard</title>
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

/* Header */
.dashboard-header {
  width:100%; 
  padding:15px 25px;
  display:flex; 
  justify-content:space-between; 
  align-items:center;
  background: linear-gradient(145deg, #eef104ff, #D4A373);
  border-radius:0 0 20px 20px;
  box-shadow: 6px 6px 20px rgba(0,0,0,0.35);
}
.dashboard-header .title {
  display:flex; align-items:center; gap:15px;
}
.dashboard-header .title span {
  font-family: 'Cursive','Brush Script MT',sans-serif;
  font-size: 2.5rem; color:#212121;
}
.dashboard-header img { 
  height:70px; 
  width:70px; }
/* Group buttons to the right */
.header-actions,
.dashboard-header > div:last-child {
  display: flex;
  align-items: center;
  gap: 10px;
}

.settings-btn, .logout-btn {
  padding: 10px 20px;
  border-radius: 15px;
  font-weight: 700;
  color: #fff;
  background: #4B2E1E;
  border: none;
  text-decoration: none;
  box-shadow: 0 5px 15px rgba(0,0,0,0.3);
  transition: 0.3s;
}

.settings-btn:hover, .logout-btn:hover {
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

/* History Table */
.history-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 5;
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
.history-table th, .history-table td {
  padding: 14px 12px !important;
  text-align: center;
  font-weight: bold;
  border-right: 2px solid #4B2E1E;
}
.history-table tbody tr:nth-child(even) { background: #FFF2A3 !important; }
.history-table tbody tr:hover {
  background: #FEDE16 !important;
  transform: scale(1.01);
}

/* ================= RESPONSIVE FIXES ================= */

/* Tablet */
@media (max-width: 992px) {
  .dashboard-header {
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 15px;
  }
  .dashboard-header img {
    height: 60px;
    width: 60px;
  }
  .dashboard-header .title span {
    font-size: 2rem;
  }
  .dashboard-header > div:last-child {
    flex-wrap: wrap;
    justify-content: center;
    gap: 10px;
  }
  .settings-btn, .logout-btn {
    padding: 8px 15px;
    font-size: 0.9rem;
  }
  .container {
    margin: 20px auto;
    gap: 15px;
  }
  .card {
    flex: 1 1 45%;
  }
}

/* Mobile */
@media (max-width: 768px) {
  .dashboard-header {
    padding: 10px;
    gap: 10px;
  }
  .dashboard-header .title {
    flex-direction: column;
    gap: 5px;
  }
  .dashboard-header img {
    height: 50px;
    width: 50px;
  }
  .dashboard-header > div:last-child {
    flex-direction: column;
    width: 100%;
  }
  .settings-btn, .logout-btn {
    width: 100%;
    text-align: center;
  }
  .container {
    flex-direction: column;
    align-items: center;
    gap: 15px;
  }
  .card {
    flex: 1 1 100%;
    width: 95%;
    min-width: unset;
  }
  .card-title {
    font-size: 1.2rem;
  }
  .value {
    font-size: 1.5rem;
  }
  .history-table th, .history-table td {
    font-size: 0.85rem;
    padding: 8px;
  }
}

/* Extra Small Phones */
@media (max-width: 480px) {
  .dashboard-header .title span {
    font-size: 1.4rem;
  }
  .dashboard-header img {
    height: 40px;
    width: 40px;
  }
  .card {
    padding: 15px;
  }
  .card-title {
    font-size: 1rem;
  }
  .value {
    font-size: 1.2rem;
  }
  .history-table {
    display: block;
    overflow-x: auto;
    white-space: nowrap;
  }
  .history-table thead, .history-table tbody, .history-table tr, .history-table th, .history-table td {
    display: inline-block;
    min-width: 100px;
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

/* Inner card for each status */
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

/* 🐝 Hungry Mode */
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

/* 🍯 Eating Mode */
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

/* Common text */
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
    <span>HiveCare - Super Admin Dashboard</span>
  </div>
  <div>
    <a href="admin-feedsched.php" class="settings-btn">
      <i class="bi bi-calendar-event"></i> Feeding History
    </a>
    <a href="database_access.php" class="settings-btn">
      <i class="bi bi-database"></i> Database
    </a>
    <a href="manage-users.php" class="settings-btn">
      <i class="bi bi-person-lines-fill"></i> Manage Users
    </a>
    <a href="admin-profile.php" class="settings-btn">
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
    <div id="temp-status" class="<?php echo ($latestTemp >= 22.30 && $latestTemp <= 25.90) ? 'status-good' : 'status-bad'; ?>">
      <?php echo ($latestTemp >= 22.30 && $latestTemp <= 25.90) ? 'Temperature is Good ✔' : 'Temperature is Bad ✖'; ?>
    </div>
    <canvas id="tempChart"></canvas>
  </div>

  <!-- Humidity -->
  <div class="card">
    <h5 class="card-title"><i class="bi bi-droplet" style="color:#4B2E1E;"></i> Humidity</h5>
    <div id="hum-value" class="value"><?php echo $latestHum; ?> %</div>
    <div id="hum-status" class="<?php echo ($latestHum >= 79.20 && $latestHum <= 86.40) ? 'status-good' : 'status-bad'; ?>">
      <?php echo ($latestHum >= 79.20 && $latestHum <= 86.40) ? 'Humidity is Good ✔' : 'Humidity is Bad ✖'; ?>
    </div>
    <canvas id="humChart"></canvas>
  </div>

  <!-- Weight -->
  <div class="card">
    <h5 class="card-title"><i class="bi bi-box-seam" style="color:#FFD93D;"></i> Weight</h5>
    <div id="weight-value" class="value"><?php echo $latestWeight; ?> kg</div>
    <div id="weight-status" class="<?php echo ($latestWeight >= 5) ? 'status-good' : 'status-bad'; ?>">
      <?php echo ($latestWeight >= 5) ? 'The Hive is Heavy!' : 'The Hive is still Light'; ?>
    </div>
    <canvas id="weightChart"></canvas>
  </div>

  <!-- Exhaust Fan -->
  <div class="card">
    <h5 class="card-title"><i class="bi bi-fan" style="color:#FFD93D;"></i> Exhaust Fan</h5>
    <div id="fan-value" class="value">
      <?= ($latestFan == 1) ? "ON" : "OFF" ?>
    </div>
    <div id="fan-status" class="<?= ($latestFan == 1) ? 'status-good' : 'status-bad' ?>">
      <?= ($latestFan == 1) ? 'Exhaust Fan is Running ✔' : 'Exhaust Fan is Off ✖' ?>
    </div>
  </div>

  <!-- Heater -->
  <div class="card">
    <h5 class="card-title"><i class="bi bi-fire" style="color:#D2691E;"></i> Heater</h5>
    <div id="heater-value" class="value">
      <?= ($latestHeater == 1) ? "ON" : "OFF" ?>
    </div>
    <div id="heater-status" class="<?= ($latestHeater == 1) ? 'status-good' : 'status-bad' ?>">
      <?= ($latestHeater == 1) ? 'Heater is ON ✔' : 'Heater is OFF ✖' ?>
    </div>
  </div>

  <!-- Mist -->
  <div class="card">
    <h5 class="card-title"><i class="bi bi-cloud-rain-fill" style="color:#4B2E1E;"></i> Mist</h5>
    <div id="mist-value" class="value">
      <?= ($latestMist == 1) ? "ON" : "OFF" ?>
    </div>
    <div id="mist-status" class="<?= ($latestMist == 1) ? 'status-good' : 'status-bad' ?>">
      <?= ($latestMist == 1) ? 'Misting is Active ✔' : 'Misting is Off ✖' ?>
    </div>
  </div>

  <!-- 🐝 Bee Feeding Status Card -->
  <div class="card feeding-card">
    <h5 class="card-title">
      <i class="bi bi-check-circle-fill" style="color:#D2691E;"></i> Bee Feeding Status
    </h5>
    <div id="feeding-status-list"></div>
  </div>
</div>

<!-- History Log Section -->
<div class="card p-4 mt-4">
  <h4 class="card-title"><i class="bi bi-clock-history"></i> History Log</.h4>
  <div class="table-responsive">
    <table class="history-table">
      <thead class="table-warning">
        <tr>
          <th>Timestamp</th>
          <th>Temperature (°C)</th>
          <th>Humidity (%)</th>
          <th>Weight (kg)</th>
          <th>Fan Status</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody id="history-body">
        <?php foreach ($history_rows as $row): ?>
          <tr>
            <td><?= $row['timestamp'] ?></td>
            <td><?= $row['temperature'] ?> °C</td>
            <td><?= $row['humidity'] ?> %</td>
            <td><?= $row['weight'] ?> kg</td>
            <td><?= ($row['fan_status'] == 1 ? 'ON' : 'OFF'); ?></td>
            <td><?= $row['status'] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
const tempData   = <?php echo json_encode(array_reverse($temperature_history)); ?>;
const humData    = <?php echo json_encode(array_reverse($humidity_history)); ?>;
const weightData = <?php echo json_encode(array_reverse($weight_history)); ?>;

function create3DChart(id, data, color) {
  new Chart(document.getElementById(id), {
    type:'line',
    data:{
      labels:data.map((_,i)=>i+1),
      datasets:[{
        data:data,
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
      scales:{ x:{ display:false }, y:{ beginAtZero:false } }
    }
  });
}

create3DChart('tempChart',  tempData,   '#D2691E');
create3DChart('humChart',   humData,    '#4B2E1E');
create3DChart('weightChart', weightData,'#4B2E1E');

function setStatusById(id, isGood, goodText, badText) {
  const el = document.getElementById(id);
  if (!el) return;
  el.className = isGood ? 'status-good' : 'status-bad';
  el.innerText = isGood ? goodText : badText;
}

// 🔁 Auto-refresh metrics + devices
async function reloadValues() {
  try {
    // Same API as user dashboard
    const response = await fetch("../user/get_latest.php");
    const data = await response.json();

    // Temperature
    if (data.temperature !== undefined) {
      document.getElementById("temp-value").innerText = data.temperature + " °C";
      setStatusById(
        "temp-status",
        (data.temperature >= 22.30 && data.temperature <= 25.90),
        "Temperature is Good ✔",
        "Temperature is Bad ✖"
      );
    }

    // Humidity
    if (data.humidity !== undefined) {
      document.getElementById("hum-value").innerText = data.humidity + " %";
      setStatusById(
        "hum-status",
        (data.humidity >= 79.20 && data.humidity <= 86.40),
        "Humidity is Good ✔",
        "Humidity is Bad ✖"
      );
    }

    // Weight
    if (data.weight !== undefined) {
      document.getElementById("weight-value").innerText = data.weight + " kg";
      setStatusById(
        "weight-status",
        (data.weight >= 5),
        "The Hive is Heavy!",
        "The Hive is still Light"
      );
    }

    // Exhaust Fan
    if (data.fan_status !== undefined) {
      const fanVal = document.getElementById("fan-value");
      const fanStatusDiv = document.getElementById("fan-status");

      if (fanVal) {
        fanVal.innerText = (data.fan_status == 1 ? "EXHAUST ON" : "EXHAUST OFF");
      }

      if (fanStatusDiv) {
        if (data.fan_status == 1) {
          fanStatusDiv.className = "status-good";
          fanStatusDiv.innerText = "Exhaust Fan is Running ✔";
        } else {
          fanStatusDiv.className = "status-bad";
          fanStatusDiv.innerText = "Exhaust Fan is Off ✖";
        }
      }
    }

    // Heater
    if (data.heater_status !== undefined) {
      const heaterVal = document.getElementById("heater-value");
      const heaterStatus = document.getElementById("heater-status");

      if (heaterVal) {
        heaterVal.innerText = (data.heater_status == 1 ? "ON" : "OFF");
      }

      if (heaterStatus) {
        if (data.heater_status == 1) {
          heaterStatus.className = "status-good";
          heaterStatus.innerText = "Heater is ON ✔";
        } else {
          heaterStatus.className = "status-bad";
          heaterStatus.innerText = "Heater is OFF ✖";
        }
      }
    }

    // Mist
    if (data.mist_status !== undefined) {
      const mistVal = document.getElementById("mist-value");
      const mistStatus = document.getElementById("mist-status");

      if (mistVal) {
        mistVal.innerText = (data.mist_status == 1 ? "ON" : "OFF");
      }

      if (mistStatus) {
        if (data.mist_status == 1) {
          mistStatus.className = "status-good";
          mistStatus.innerText = "Misting is Active ✔";
        } else {
          mistStatus.className = "status-bad";
          mistStatus.innerText = "Misting is Off ✖";
        }
      }
    }

  } catch (err) {
    console.error("Error fetching latest data:", err);
  }
}

// 🔁 Auto-refresh history log
async function reloadHistory() {
  try {
    const res = await fetch("get_history.php");
    const data = await res.json();

    const tbody = document.getElementById("history-body");
    if (!tbody) return;
    tbody.innerHTML = "";

    data.forEach(row => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${row.timestamp}</td>
        <td>${row.temperature} °C</td>
        <td>${row.humidity} %</td>
        <td>${row.weight} kg</td>
        <td>${row.fan_status > 0 ? "ON" : "OFF"}</td>
        <td>${row.status}</td>
      `;
      tbody.appendChild(tr);
    });
  } catch (err) {
    console.error("History fetch error:", err);
  }
}

// Feeding status (Bee Feeding Status card)
function fetchFeedingStatus() {
  fetch('get_feeding_status.php')
    .then(response => response.json())
    .then(data => {
      const now      = new Date();
      const nextFeed = data.next_feed ? new Date(data.next_feed) : null;
      const isHungry = nextFeed ? (nextFeed <= now) : true;

      const cardClass = isHungry ? 'feed-card feed-hungry' : 'feed-card feed-eating';
      const statusText = isHungry
        ? `<p class="text-danger fw-bold">🐝 Bees are hungry! Feed them now!</p>`
        : `<p class="text-success fw-bold">🍯 Bees are eating happily!</p>
           <p>Next feeding in: <span class="countdown"></span></p>`;

      document.getElementById('feeding-status-list').innerHTML = `
        <div class="${cardClass}">
          <h6><i class="bi bi-person-fill"></i> ${data.username || 'Unknown User'}</h6>
          ${statusText}
          <small>
            <i class="bi bi-clock-history"></i> Last fed: ${data.last_fed || 'Not yet fed'}<br>
            <i class="bi bi-calendar-event"></i> Next feed: ${data.next_feed || 'N/A'}
          </small>
        </div>
      `;

      if (!isHungry && data.next_feed) {
        updateCountdown(data.next_feed);
      }
    })
    .catch(err => console.error('Fetch error:', err));
}

let countdownInterval;

function updateCountdown(nextFeedTime) {
  const countdownElem = document.querySelector('.countdown');
  if (!countdownElem) return;

  const targetTime = new Date(nextFeedTime).getTime();
  clearInterval(countdownInterval);

  countdownInterval = setInterval(() => {
    const now = new Date().getTime();
    const diff = targetTime - now;

    if (diff <= 0) {
      clearInterval(countdownInterval);
      countdownElem.textContent = "🐝 Time to feed the bees!";
      return;
    }

    const hrs  = Math.floor(diff / (1000 * 60 * 60));
    const mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const secs = Math.floor((diff % (1000 * 60)) / 1000);

    countdownElem.textContent = `${hrs}h ${mins}m ${secs}s`;
  }, 1000);
}

// Run everything
reloadValues();
reloadHistory();
setInterval(reloadValues, 5000);
setInterval(reloadHistory, 5000);

fetchFeedingStatus();
setInterval(fetchFeedingStatus, 1000);
</script>

</body>
</html>
