<?php
require_once "../config.php";

// Query 1: Get ALL readings for charts and latest values
$sql_all = "SELECT timestamp, temperature, humidity, weight, fan_status, status
            FROM beehive_readings 
            ORDER BY timestamp ASC";
$result_all = mysqli_query($link, $sql_all);

$timestamps   = [];
$temperatures = [];
$humidities   = [];
$weights      = [];
$fan_statuses = [];
$statuses     = [];

while ($row = mysqli_fetch_assoc($result_all)) {
    $timestamps[]   = $row['timestamp'];
    $temperatures[] = $row['temperature'];
    $humidities[]   = $row['humidity'];
    $weights[]      = $row['weight'];
    $fan_statuses[] = $row['fan_status'];
    $statuses[]     = $row['status'];
}

$latestTemp   = end($temperatures);
$latestHum    = end($humidities);
$latestWeight = end($weights);
$latestFan    = end($fan_statuses);

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
array_shift($history_rows);

mysqli_close($link);
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HiveCare - Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: Raleway, sans-serif;
}

body {
    min-height: 100vh;
    background: url('https://a-z-animals.com/media/2025/08/shutterstock-2374833763-huge-licensed-scaled.jpg') no-repeat center/cover;
    position: relative;
    color: #212121;
}

body::before {
    content: "";
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: rgba(0,0,0,0.4);
    z-index: 0;
}

.container, .dashboard-header, .card {
    position: relative;
    z-index: 1;
}

.container {
    max-width: 1200px;
    margin: 40px auto;
}

.card {
    background: linear-gradient(145deg, #FFF8DC, #9b8c51ff);
    border-radius: 25px;
    border: none;
    padding: 25px;
    text-align: center;
    box-shadow: 8px 8px 20px rgba(0,0,0,0.3), -5px -5px 15px rgba(255,255,255,0.5);
    margin-bottom: 20px;
}

.dashboard-header {
    width: 100%;
    padding: 15px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(145deg, #eef104ff, #D4A373);
    border-radius: 0 0 20px 20px;
    box-shadow: 6px 6px 20px rgba(0,0,0,0.35);
}

.dashboard-header .title {
    display: flex;
    align-items: center;
    gap: 15px;
}

.dashboard-header .title span {
    font-family: 'Cursive', 'Brush Script MT', sans-serif;
    font-size: 2.5rem;
    color: #212121;
}

.dashboard-header img {
    height: 70px;
    width: 70px;
}

.logout-btn, .settings-btn {
    padding: 10px 20px;
    border-radius: 15px;
    font-weight: 700;
    color: #fff;
    background: #4B2E1E;
    border: none;
    text-decoration: none;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    transition: 0.3s;
    margin-left: 5px;
}

.logout-btn:hover, .settings-btn:hover {
    background: #6B4226;
    transform: translateY(-2px) scale(1.03);
}

.card-title {
    font-weight: 700;
    font-size: 1.5rem;
    margin-bottom: 15px;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    color: #4b2e1e;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
}

.value {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 10px;
    color: #4B2E1E;
    text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
}

.status-good, .status-bad {
    border-radius: 15px;
    padding: 10px 20px;
    font-size: 1rem;
    font-weight: 700;
    margin-top: 10px;
    display: inline-block;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}

.status-good { background: #ffd83dd8; color: #4b2e1e; }
.status-bad { background: #d2691ed2; color: #FFF; }

canvas {
    margin-top: 20px;
    height: 120px !important;
}

.fan-btn {
    padding: 10px 20px;
    border-radius: 15px;
    font-weight: 700;
    margin: 5px;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    transition: 0.3s;
}

.fan-auto { background: #FFD93D; color: #4B2E1E; }
.fan-on { background: #4B2E1E; color: #FFD93D; }
.fan-off { background: #D2691E; color: #fff; }

.fan-btn:hover { transform: translateY(-2px) scale(1.05); }

#fan-status {
    margin-top: 15px;
    font-weight: 700;
    font-size: 1.2rem;
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
    <span>HiveCare - Admin Dashboard</span>
  </div>
  <div>
    <a href="database_access.php" class="settings-btn"><i class="bi bi-database"></i> Database</a>
    <a href="manage_users.php" class="settings-btn"><i class="bi bi-person-lines-fill"></i> Add Users</a>
    <a href="admin-profile.php" class="settings-btn"><i class="bi bi-person-fill"></i> Edit Profile</a>

    <a href="homepage.php" class="logout-btn"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </div>
</div>

<div class="container mt-4">
  <div class="row g-4"> 
    <div class="col-lg-3 col-md-6">
      <div class="card p-3">
        <h5 class="card-title"><i class="bi bi-thermometer-half"></i> Temperature</h5>
        <div id="temp-value" class="value"><?php echo $latestTemp; ?> °C</div>
        <div class="<?php echo ($latestTemp>32||$latestTemp<28)?'status-bad':'status-good';?>">
          <?php echo ($latestTemp>32||$latestTemp<28)?'Temperature is Bad ✖':'Temperature is Good ✔';?>
        </div>  
        <canvas id="tempChart"></canvas>
      </div>
    </div>

    <div class="col-lg-3 col-md-6">
      <div class="card p-3">
        <h5 class="card-title"><i class="bi bi-droplet"></i> Humidity</h5>
        <div id="hum-value" class="value"><?php echo $latestHum; ?> %</div>
        <div class="<?php echo ($latestHum>=65&&$latestHum<=80)?'status-good':'status-bad';?>">
          <?php echo ($latestHum>=65&&$latestHum<=80)?'Humidity is Good ✔':'Humidity is Bad ✖';?>
        </div>
        <canvas id="humChart"></canvas>
      </div>
    </div>

    <div class="col-lg-3 col-md-6">
      <div class="card p-3">
        <h5 class="card-title"><i class="bi bi-box-seam"></i> Weight</h5>
        <div id="weight-value" class="value"><?php echo $latestWeight; ?> kg</div>
        <div class="<?php echo ($latestWeight>=5)?'status-good':'status-bad';?>">
          <?php echo ($latestWeight>=5)?'The Hive is Heavy!':'The Hive is still Light ✖';?>
        </div>
        <canvas id="weightChart"></canvas>
      </div>
    </div>

    <div class="card">
    <h5 class="card-title"><i class="bi bi-lightning-charge-fill" style="color:#FFD93D;"></i> Fan Status</h5>
    <div id="fan-value" class="value"><?= ($latestFan==1)?"ON":"OFF" ?></div>
    <div id="fan-status" class="<?= ($latestFan==1)?'status-good':'status-bad' ?>">
      <?= ($latestFan==1)?'The Fan is Running ✔':'The Fan is Off ✖' ?>
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
            <td><?= $row['status'] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>



<script>
const tempData = <?php echo json_encode(array_reverse($temperature_history)); ?>;
const humData = <?php echo json_encode(array_reverse($humidity_history)); ?>;
const weightData = <?php echo json_encode(array_reverse($weight_history)); ?>;

function create3DChart(id,data,color){
  new Chart(document.getElementById(id),{
    type:'line',
    data:{labels:data.map((_,i)=>i+1),datasets:[{data:data,borderColor:color,backgroundColor:color+'55',fill:true,tension:0.4,pointRadius:4,pointBackgroundColor:color,pointHoverRadius:6,borderWidth:3}]},
    options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{x:{display:false},y:{beginAtZero:false}}}
  });
}
create3DChart('tempChart',tempData,'#D2691E');
create3DChart('humChart',humData,'#4B2E1E');
create3DChart('weightChart',weightData,'#4B2E1E');

function controlFan(action){
  document.getElementById('fan-status').innerText = "Fan mode: "+(action==='auto'?'Automatic':action==='on'?'On':'Off');
  console.log("Fan set to:", action);
}

// ✅ Auto-refresh latest values
// ✅ Auto-refresh latest values
async function reloadValues() {
  try {
    const response = await fetch("get_latest.php");
    const data = await response.json();

    // Update main values
    document.getElementById("temp-value").innerText   = data.temperature + " °C";
    document.getElementById("hum-value").innerText    = data.humidity + " %";
    document.getElementById("weight-value").innerText = data.weight + " kg";

    // ✅ Fan real-time update
    document.getElementById("fan-value").innerText = (data.fan_status == 1) ? "ON" : "OFF";
    const fanStatus = document.getElementById("fan-status");
    if (data.fan_status == 1) {
      fanStatus.className = "status-good";
      fanStatus.innerText = "The Fan is Running ✔";
    } else {
      fanStatus.className = "status-bad";
      fanStatus.innerText = "The Fan is Off ✖";
    }

    // Update status conditions
    updateStatus("temp-value", data.temperature >= 28 && data.temperature <= 32, "Temperature is Good ✔", "Temperature is Bad ✖");
    updateStatus("hum-value", data.humidity >= 65 && data.humidity <= 80, "Humidity is Good ✔", "Humidity is Bad ✖");
    updateStatus("weight-value", data.weight >= 5, "The Hive is Heavy!", "The Hive is still Light ✖");

  } catch (err) {
    console.error("Error fetching latest data:", err);
  }
}


function updateStatus(id, condition, goodText, badText) {
  const el = document.getElementById(id).nextElementSibling; // status div is right after value div
  if (condition) {
    el.className = "status-good";
    el.innerText = goodText;
  } else {
    el.className = "status-bad";
    el.innerText = badText;
  }
}

// ✅ Auto-refresh history log
async function reloadHistory() {
  try {
    const res = await fetch("get_history.php");
    const data = await res.json();

    const tbody = document.getElementById("history-body");
    tbody.innerHTML = "";

    data.forEach(row => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${row.timestamp}</td>
        <td>${row.temperature} °C</td>
        <td>${row.humidity} %</td>
        <td>${row.weight} kg</td>
        <td>${row.status}</td>
      `;
      tbody.appendChild(tr);
    });
  } catch (err) {
    console.error("History fetch error:", err);
  }
}

// Run both immediately + every 5s
reloadValues();
reloadHistory();
setInterval(reloadValues, 5000);
setInterval(reloadHistory, 5000);
</script>

</body>
</html>
