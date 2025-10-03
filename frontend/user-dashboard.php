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

$sql_last5 = "SELECT timestamp, temperature, humidity, weight, status
              FROM beehive_readings 
              ORDER BY timestamp DESC 
              LIMIT 6";
$result_last5 = mysqli_query($link, $sql_last5);

$history_rows = [];
while ($row = mysqli_fetch_assoc($result_last5)) {
    $history_rows[] = $row;
}
array_shift($history_rows);

mysqli_close($link);

// === Rainy season check ===
$month = date('n'); // numeric month 1-12
$isRainy = ($month >= 6 && $month <= 11);

// === Scheduler file ===
$filename = 'next_feeding.json';
$nextFeeding = null;

// Read existing next feeding date
if (file_exists($filename)) {
    $data = json_decode(file_get_contents($filename), true);
    if ($data && isset($data['next_feeding'])) {
        $nextFeeding = $data['next_feeding'];
    }
}

// Handle "Mark Feeding Done" button
$message = "";
if (isset($_POST['done'])) {
    $nextDate = new DateTime();
    $nextDate->modify('+3 days'); // next feeding in 3 days
    $nextFeeding = $nextDate->format('Y-m-d');

    // Save to JSON
    file_put_contents($filename, json_encode(['next_feeding' => $nextFeeding]));
    $message = "Feeding marked done! Next feeding scheduled in 3 days.";
}
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
  padding: 20px 0;
  color: #212121;
}
body::before {
  content: "";
  position: absolute; inset: 0;
  background-color: rgba(0,0,0,0.4);
  z-index: 0;
}
.container, .dashboard-header, .card, .fan-card { 
position: relative; 
z-index: 1; }

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
.card, .fan-card {
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

/* Fan card */
/* Fan card */
.fan-card {
  flex: 1 1 100%;
  text-align: center;
}

.fan-controls {
  display: flex;
  justify-content: center;
  gap: 15px;
  margin-top: 15px;
  flex-wrap: wrap;
}

.fan-btn {
  padding: 10px 18px;
  border-radius: 12px;
  border: none;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.25s ease;
}

.fan-btn:hover {
  transform: translateY(-2px) scale(1.05);
  box-shadow: 0 6px 15px rgba(0, 0, 0, 0.25);
}

.fan-btn.active {
  border: 3px solid #fff;
  box-shadow: 0 0 12px rgba(0,0,0,0.3);
}

/* Colors */
.fan-auto { background:#FFD93D; color:#4B2E1E; }
.fan-on   { background:#4B2E1E; color:#FFD93D; }
.fan-off  { background:#D2691E; color:#fff; }

.fan-status {
  margin-top: 18px;
  font-weight: 700;
  font-size: 1.1rem;
  color: #4B2E1E;
}
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
      <i class="bi bi-box-arrow-right"></i> Set Feeding Time
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



<div class="row mb-2">
  <div class="col">
    <input type="number" id="feed-days" class="form-control" placeholder="Days" min="0" value="3">
  </div>
  <div class="col">
    <input type="number" id="feed-hours" class="form-control" placeholder="Hours" min="0" max="23" value="0">
  </div>
  <div class="col">
    <input type="number" id="feed-minutes" class="form-control" placeholder="Minutes" min="0" max="59" value="0">
  </div>
</div>


<div class="card">
  <h5 class="card-title"><i class="bi bi-calendar-event"></i> Bee Feeding Scheduler</h5>
  <div id="scheduler-body">
    <?php if (!$isRainy): ?>
      <p>Scheduler inactive (not rainy season)</p>
    <?php else: ?>
      <?php if ($nextFeeding): ?>
        <?php
          $today = new DateTime();
          $feedingDate = new DateTime($nextFeeding);
          $diff = $today->diff($feedingDate)->days;
          $statusText = ($feedingDate <= $today) ? "Feeding is due today!" : "Next feeding in $diff day(s)";
        ?>
        <p id="scheduler-status" style="color:<?= ($feedingDate <= $today)?'red':'green' ?>;">
          <?= $statusText ?>
        </p>
      <?php else: ?>
        <p id="scheduler-status">Next feeding: Today</p>
      <?php endif; ?>

      <form method="post" id="feeding-form">
        <button type="submit" name="done" id="feeding-btn" class="btn btn-warning mt-2">Mark Feeding Done</button>
      </form>

      <?php if (!empty($message)) echo "<p style='color:green;'>$message</p>"; ?>
    <?php endif; ?>
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

// Fetch saved feeding time from JSON
let nextFeedingDate = new Date();
fetch('next_feeding.json')
  .then(res => res.json())
  .then(data => {
      let days = parseInt(data.days || 3);
      let hours = parseInt(data.hours || 0);
      let minutes = parseInt(data.minutes || 0);

      nextFeedingDate.setTime(
          nextFeedingDate.getTime() 
          + days*24*60*60*1000 
          + hours*60*60*1000 
          + minutes*60*1000
      );

      startCountdown(nextFeedingDate);
  });

// Countdown function
function startCountdown(targetDate) {
    const statusEl = document.getElementById("scheduler-status");
    const btn = document.getElementById("feeding-btn");

    function updateCountdown() {
        const now = new Date();
        const diff = targetDate - now;

        if (diff <= 0) {
            statusEl.innerText = "Feeding is due now!";
            statusEl.style.color = "red";
            clearInterval(timer);
            btn.style.display = "inline-block";
            alert("Feeding is due now!");
            return;
        }

        const d = Math.floor(diff / (1000*60*60*24));
        const h = Math.floor((diff % (1000*60*60*24)) / (1000*60*60));
        const m = Math.floor((diff % (1000*60*60)) / (1000*60));
        const s = Math.floor((diff % (1000*60)) / 1000);

        statusEl.innerText = `Next feeding in ${d}d ${h}h ${m}m ${s}s`;
    }

    updateCountdown();
    const timer = setInterval(updateCountdown, 1000);
}










</script>

</body>
</html>
