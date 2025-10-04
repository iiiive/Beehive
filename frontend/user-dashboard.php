<?php
require_once "../config.php";

// === Hive readings ===
$sql_all = "SELECT timestamp, temperature, humidity, weight, fan_status 
            FROM beehive_readings 
            ORDER BY timestamp ASC";
$result_all = mysqli_query($link, $sql_all);

$timestamps   = [];
$temperatures = [];
$humidities   = [];
$weights      = [];
$fans         = [];

while ($row = mysqli_fetch_assoc($result_all)) {
    $timestamps[]   = $row['timestamp'];
    $temperatures[] = $row['temperature'];
    $humidities[]   = $row['humidity'];
    $weights[]      = $row['weight'];
    $fans[]         = $row['fan_status'];
}

$latestTemp   = end($temperatures);
$latestHum    = end($humidities);
$latestWeight = end($weights);
$latestFan    = end($fans);

$temperature_history = $temperatures;
$humidity_history    = $humidities;
$weight_history      = $weights;

// Get last 5 readings
$sql_last5 = "SELECT timestamp, temperature, humidity, weight, fan_status, status
              FROM beehive_readings 
              ORDER BY timestamp DESC 
              LIMIT 6";
$result_last5 = mysqli_query($link, $sql_last5);

$history_rows = [];
while ($row = mysqli_fetch_assoc($result_last5)) {
    $history_rows[] = $row;
}
array_shift($history_rows);


// fetch user feeding schedule
$user_id = $_SESSION['user_id'] ?? 1;
$sql_feed = "SELECT * FROM bee_feeding_schedule WHERE user_id=$user_id LIMIT 1";
$result_feed = mysqli_query($link, $sql_feed);
$feeding = mysqli_fetch_assoc($result_feed);

$now = new DateTime();
$next_feed = $feeding ? new DateTime($feeding['next_feed']) : null;
$time_diff = $next_feed ? $next_feed->getTimestamp() - $now->getTimestamp() : null;
$needs_feeding = ($time_diff <= 0);

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
z-index: 1; }

/* Header */
.dashboard-header {
  width:100%; 
  display:flex; 
  justify-content:space-between; 
  align-items:center;
  background: linear-gradient(145deg, #eef104ff, #D4A373);
  border-radius:0 0 20px 20px;
  box-shadow: 6px 6px 20px rgba(0,0,0,0.35);
}
.dashboard-header .title {
  display:flex; 
  align-items:center; 
  gap:15px;
}
.dashboard-header .title span {
  font-family: 'Cursive','Brush Script MT',sans-serif;
  font-size: 2.5rem; color:#212121;
}
.dashboard-header img { 
  height:70px; 
  width:70px; }
/* Group buttons to the right */
.header-actions {
  display: flex;
  align-items: center;
  gap: 10px; /* space between edit & logout */
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
    border-right: 2px solid #4B2E1E; /* coffee tone for vertical lines */

  
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
  .header-actions {
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
    flex: 1 1 45%; /* 2 cards per row */
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
  .header-actions {
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

  /* Make history table scrollable */
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
/* Feeding Scheduler Custom Design */
.feeding-card {
  background: linear-gradient(145deg, #FFF8DC, #f7d36c);
  border-radius: 25px;
  padding: 30px;
  text-align: center;
  box-shadow: 8px 8px 20px rgba(0,0,0,0.3), -5px -5px 15px rgba(255,255,255,0.5);
  position: relative;
  overflow: hidden;
}

/* Honey drip accent on top */
.feeding-card::before {
  content: "";
  position: absolute;
  top: -20px;
  left: 0;
  width: 100%;
  height: 40px;
  background: radial-gradient(circle at 10% 30%, #ffd93d, transparent 50%),
              radial-gradient(circle at 30% 10%, #ffcc00, transparent 50%),
              radial-gradient(circle at 60% 30%, #ffb703, transparent 50%),
              radial-gradient(circle at 90% 20%, #fcbf49, transparent 50%);
  opacity: 0.7;
  animation: honeyMove 5s infinite linear;
}

@keyframes honeyMove {
  from { background-position: 0 0, 20px 0, 40px 0, 60px 0; }
  to { background-position: 60px 0, 80px 0, 100px 0, 120px 0; }
}

/* Countdown text styling */
.countdown-container {
  margin-top: 10px;
}

.countdown-text {
  font-size: 2rem;
  font-weight: 800;
  color: #4B2E1E;
  text-shadow: 1px 1px 3px rgba(0,0,0,0.2);
  display: block;
  margin-top: 10px;
}

/* Feed Done Button */
.feed-btn {
  background: linear-gradient(145deg, #FFD93D, #E8C547);
  border: none;
  color: #4B2E1E;
  padding: 12px 25px;
  font-weight: 700;
  font-size: 1rem;
  border-radius: 15px;
  margin-top: 15px;
  box-shadow: 0 5px 10px rgba(0,0,0,0.2);
  transition: all 0.3s ease;
}

.feed-btn:hover {
  background: linear-gradient(145deg, #E8C547, #FFD93D);
  transform: translateY(-3px) scale(1.03);
  box-shadow: 0 8px 15px rgba(0,0,0,0.3);
}

/* Status bubble */
#feeding-status {
  display: inline-block;
  padding: 10px 20px;
  border-radius: 20px;
  font-weight: bold;
  font-size: 1.1rem;
  box-shadow: 0 4px 10px rgba(0,0,0,0.2);
  transition: all 0.3s ease;
}


</style>
</head>
<body>

<div class="dashboard-header">
  <div class="title">
    <img src="images/bee.png" alt="HiveCare Logo">
    <span>HiveCare - User Dashboard</span>
  </div>

  <!-- Actions aligned to the right -->

  <div class="header-actions">
    <a href="set_feeding_time.php" class="logout-btn">
      <i class="bi bi-calendar-event"></i> Set Feeding Time
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
    <div id="temp-status" class="<?php echo ($latestTemp>32||$latestTemp<20)?'status-bad':'status-good';?>">
  <?php echo ($latestTemp>32||$latestTemp<20)?'Temperature is Bad ✖':'Temperature is Good ✔';?>
</div>
    <canvas id="tempChart"></canvas>
  </div>

  <!-- Humidity -->
  <div class="card">
    <h5 class="card-title"><i class="bi bi-droplet" style="color:#4B2E1E;"></i> Humidity</h5>
    <div id="hum-value" class="value"><?php echo $latestHum; ?> %</div>
    <div id="hum-status" class="<?php echo ($latestHum>=65&&$latestHum<=80)?'status-good':'status-bad';?>">
  <?php echo ($latestHum>=65&&$latestHum<=80)?'Humidity is Good ✔':'Humidity is Bad ✖';?>
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

  <!-- Fan -->
  <div class="card">
    <h5 class="card-title"><i class="bi bi-lightning-charge-fill" style="color:#FFD93D;"></i> Fan Status</h5>
    <div id="fan-value" class="value"><?= ($latestFan==1)?"ON":"OFF" ?></div>
    <!-- Fan -->
<div id="fan-status" class="<?= ($latestFan==1)?'status-good':'status-bad' ?>">
  <?= ($latestFan==1)?'The Fan is Running ✔':'The Fan is Off ✖' ?>
</div>
  </div>

  <!-- Feeding Scheduler Card -->
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
          <th>Fan</th>
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
            <td><?= $row['fan_status'] > 0 ? "ON" : "OFF" ?></td>
            <td><?= $row['status'] ?></td>

          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>












<script>

  
const tempData = <?php echo json_encode($temperature_history); ?>;
const humData = <?php echo json_encode($humidity_history); ?>;
const weightData = <?php echo json_encode($weight_history); ?>;

function create3DChart(id, data, color) {
  new Chart(document.getElementById(id), {
    type:'line',
    data:{ labels:data.map((_,i)=>i+1),
      datasets:[{ data, borderColor:color, backgroundColor:color+'55',
        fill:true, tension:0.4, pointRadius:4, pointBackgroundColor:color,
        pointHoverRadius:6, borderWidth:3 }] },
    options:{ responsive:true, maintainAspectRatio:false,
      plugins:{ legend:{ display:false } },
      scales:{ x:{ display:false }, y:{ beginAtZero:false } } }
  });
}

create3DChart('tempChart', tempData, '#D2691E');
create3DChart('humChart', humData, '#4B2E1E');
create3DChart('weightChart', weightData, '#4B2E1E');

function controlFan(action, btn) {
  document.querySelectorAll('.fan-btn').forEach(b => {
    b.style.transform = 'scale(1)'; b.style.boxShadow = '';
  });
  btn.style.transform = 'scale(1.05)';
  btn.style.boxShadow = '0 6px 18px rgba(0,0,0,0.2)';
  document.getElementById('fan-status').innerText =
    'Fan mode: ' + (action==='auto'?'Automatic':(action==='on'?'On':'Off'));
  console.log('Fan set to:', action);
}

async function reloadValues() {
  try {
    const response = await fetch("get_latest.php"); 
    const data = await response.json();

    // Update numbers
    document.getElementById("temp-value").innerText   = data.temperature + " °C";
    document.getElementById("hum-value").innerText    = data.humidity + " %";
    document.getElementById("weight-value").innerText = data.weight + " kg";
    document.getElementById("fan-value").innerText    = (data.fan_status == 1 ? "ON" : "OFF");

    // Update statuses dynamically
    updateStatus("temp-status",
      (data.temperature >= 28 && data.temperature <= 32) ?
      {text:"Temperature is Good ✔", cls:"status-good"} :
      {text:"Temperature is Bad ✖", cls:"status-bad"}
    );

    updateStatus("hum-status",
      (data.humidity >= 65 && data.humidity <= 80) ?
      {text:"Humidity is Good ✔", cls:"status-good"} :
      {text:"Humidity is Bad ✖", cls:"status-bad"}
    );

    updateStatus("weight-status",
      (data.weight >= 5) ?
      {text:"The Hive is Heavy!", cls:"status-good"} :
      {text:"The Hive is still Light", cls:"status-bad"}
    );

    updateStatus("fan-status",
      (data.fan_status == 1) ?
      {text:"The Fan is Running ✔", cls:"status-good"} :
      {text:"The Fan is Off ✖", cls:"status-bad"}
    );

  } catch (err) {
    console.error("Error fetching latest data:", err);
  }
}

// Helper function
function updateStatus(id, obj) {
  const el = document.getElementById(id);
  el.className = obj.cls;
  el.innerText = obj.text;
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

// Run immediately + every 5s
reloadHistory();
setInterval(reloadHistory, 5000);

async function reloadFan() {
  try {
    const res = await fetch("get_fan.php");
    const data = await res.json();
    const fanStatusEl = document.getElementById("fan-status");

    if (data.fan_status === 1) {
      fanStatusEl.innerHTML = '<span style="color:green; font-weight:bold;">Fan is ON ✔</span>';
    } else {
      fanStatusEl.innerHTML = '<span style="color:red; font-weight:bold;">Fan is OFF ✖</span>';
    }
  } catch (err) {
    console.error("Fan fetch error:", err);
  }
}


const feedingStatusEl = document.getElementById("feeding-status");
const countdownEl = document.getElementById("countdown");
const feedDoneBtn = document.getElementById("feed-done-btn");

let timerInterval;
let nextFeedTime = null;
let lastFetchedFeed = null;

// Function to fetch current feeding schedule every 1 second
async function fetchFeedingData() {
  const res = await fetch("get_next_feed.php");
  const data = await res.json();

  // Only update countdown if data changed
  if (JSON.stringify(data) !== JSON.stringify(lastFetchedFeed)) {
    lastFetchedFeed = data;
    nextFeedTime = data.next_feed ? new Date(data.next_feed).getTime() : null;
    updateDisplay(data);
  }
}

// Function to update display
function updateDisplay(data) {
  clearInterval(timerInterval);
  
  const now = new Date().getTime();
  const distance = data.next_feed ? new Date(data.next_feed).getTime() - now : 0;

  if (distance <= 0) {
    feedingStatusEl.innerText = "🐝 Bees are hungry! Feed them now.";
    feedingStatusEl.className = "status-bad";
    feedDoneBtn.style.display = "inline-block";
    countdownEl.innerText = "";
    return;
  }

  feedDoneBtn.style.display = "none"; // hide button when countdown active
  feedingStatusEl.innerText = "🍯 Bees are eating";

  // Start countdown timer
  function updateCountdown() {
    const now = new Date().getTime();
    const dist = new Date(data.next_feed).getTime() - now;

    if (dist <= 0) {
      clearInterval(timerInterval);
      feedingStatusEl.innerText = "🐝 Bees are hungry! Feed them now.";
      feedDoneBtn.style.display = "inline-block";
      countdownEl.innerText = "";

        alert("The bees are hungry! Time to feed them 🍯");
        fetch("check_feeding_status.php");

        
      return;
    }

    const days = Math.floor(dist / (1000 * 60 * 60 * 24));
    const hours = Math.floor((dist % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((dist % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((dist % (1000 * 60)) / 1000);

    countdownEl.innerText = `${days}d ${hours}h ${minutes}m ${seconds}s`;
  }

  updateCountdown();
  timerInterval = setInterval(updateCountdown, 1000);
}

// Feed Done button
feedDoneBtn.addEventListener("click", async () => {
  feedDoneBtn.disabled = true;
  await fetch("feed_done.php", { method: "POST" });
  await fetchFeedingData(); // immediately refresh data
  feedDoneBtn.disabled = false;
});

// Poll every 1 second to sync between users
setInterval(fetchFeedingData, 1000);

// Initial load
fetchFeedingData();










</script>

</body>
</html>