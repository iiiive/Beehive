<?php
require_once "../config.php";

// Query 1: Get ALL readings for charts and latest values
$sql_all = "SELECT timestamp, temperature, humidity, weight 
            FROM beehive_readings 
            ORDER BY timestamp ASC";
$result_all = mysqli_query($link, $sql_all);

$timestamps   = [];
$temperatures = [];
$humidities   = [];
$weights      = [];

while ($row = mysqli_fetch_assoc($result_all)) {
    $timestamps[]   = $row['timestamp'];
    $temperatures[] = $row['temperature'];
    $humidities[]   = $row['humidity'];
    $weights[]      = $row['weight'];
}

$latestTemp   = end($temperatures);
$latestHum    = end($humidities);
$latestWeight = end($weights);

// For charts
$temperature_history = $temperatures;
$humidity_history    = $humidities;
$weight_history      = $weights;

// Query 2: Get ONLY the last 5 previous readings (excluding the very latest one)
$sql_last5 = "SELECT timestamp, temperature, humidity, weight, status 
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
<title>HiveCare - Guest Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
* { 
  box-sizing:border-box; 
  margin:0; padding:0; 
  font-family:Raleway,sans-serif; 
}
body {
  min-height: 100vh;
  background: url('https://a-z-animals.com/media/2025/08/shutterstock-2374833763-huge-licensed-scaled.jpg') no-repeat center center/cover;
  position: relative;
  padding: 20px 0;
  color: #212121;
}

body::before {
  content: "";
  position: absolute;
  top:0; 
  left:0; 
  right:0; 
  bottom:0;
  background-color: rgba(0,0,0,0.4); 
  z-index: 0;
}

.container, .dashboard-header, .card {
  position: relative; 
  z-index: 1; 
}
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
  display:flex; 
  align-items:center; 
  gap:15px; 
}
.dashboard-header .title span {
  font-family: 'Cursive', 'Brush Script MT', sans-serif;
  font-size: 2.5rem;
  color: #212121;
}
.dashboard-header img { 
  height:70px; 
  width: 70px; 
}
.logout-btn {
  padding:10px 20px; 
  border-radius:15px; 
  font-weight:700; 
  color:#fff;
  background: #4B2E1E; 
  border:none; 
  text-decoration:none; 
  box-shadow: 0 5px 15px rgba(0,0,0,0.3);
  transition:0.3s;
}
.logout-btn:hover { 
  background:#6B4226; 
  transform: translateY(-2px) scale(1.03); }

.container { 
  max-width:1100px; 
  margin:40px auto; 
  display:flex; 
  flex-wrap:wrap; 
  justify-content:center; gap:20px; }

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
  font-weight:700; 
  font-size:1.5rem; 
  margin-bottom:15px; 
  display:flex; 
  justify-content:center; 
  align-items:center; 
  gap:10px; 
  color: #4b2e1e;; 
  text-shadow:1px 1px 2px rgba(0,0,0,0.3);
}
.card-title i { 
  font-size:1.5rem; 
}
.value { 
  font-size:2rem; 
  font-weight:bold; 
  margin-bottom:10px; 
  color:#4B2E1E; 
  text-shadow:1px 1px 3px rgba(0,0,0,0.3);
}
.status-good, .status-bad {
  border-radius:15px; 
  padding:10px 20px; 
  font-size:1rem; 
  font-weight:700; 
  margin-top:10px; 
  display:inline-block;
  box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}
.status-good { 
  background: #ffd83dd8; 
  color: #4b2e1e; 
}
.status-bad { 
  background: #d2691ed2; 
  color:#FFF; 
}

canvas { 
  margin-top:20px; 
  height:120px !important; 
  }
</style>
</head>
<body>

<div class="dashboard-header">
  <div class="title">
    <img src="images/bee.png" alt="HiveCare Logo">
    <span>HiveCare - Guest Dashboard</span>
  </div>
  <a href="homepage.php" class="logout-btn"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<div class="container">
  <div class="card">
    <h5 class="card-title"><i class="bi bi-thermometer-half" style="color:#D2691E;"></i> Temperature</h5>
    <div class="value"><?php echo $latestTemp; ?> °C</div>
    <div class="<?php echo ($latestTemp>32||$latestTemp<28)?'status-bad':'status-good';?>">
      <?php echo ($latestTemp>32||$latestTemp<28)?'Temperature is Bad ✖':'Temperature is Good ✔';?>
    </div>
    <canvas id="tempChart"></canvas>
  </div>
  <div class="card">
    <h5 class="card-title"><i class="bi bi-droplet" style="color:#4B2E1E;"></i> Humidity</h5>
    <div class="value"><?php echo $latestHum; ?> %</div>
    <div class="<?php echo ($latestHum>=65&&$latestHum<=80)?'status-good':'status-bad';?>">
      <?php echo ($latestHum>=65&& $latestHum<=80)?'Humidity is Good ✔':'Humidity is Bad ✖';?>
    </div>
    <canvas id="humChart"></canvas>
  </div>
  <div class="card">
    <h5 class="card-title"><i class="bi bi-droplet" style="color:#FFD93D;"></i> Weight</h5>
    <div class="value"><?php echo $latestWeight ; ?> kg</div>
    <div class="<?php echo ($latestWeight>=5)?'status-good':'status-bad';?>">
      <?php echo ($latestWeight>=5)?'The Hive is Heavy!':'The Hive is still Light';?>
    </div>
    <canvas id="weightChart"></canvas>
  </div>
</div>

<!-- History Log Section -->
<div class="card p-4 mt-4">
  <h4 class="card-title"><i class="bi bi-clock-history"></i> History Log </h4>
  <div class="table-responsive">
    <table class="table table-bordered table-striped table-hover mt-3">
      <thead class="table-warning">
        <tr>
          <th>Timestamp</th>
          <th>Temperature (°C)</th>
          <th>Humidity (%)</th>
          <th>Weight (kg)</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
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
const tempData = <?php echo json_encode($temperature_history); ?>;
const humData = <?php echo json_encode($humidity_history); ?>;
const weightData = <?php echo json_encode($weight_history); ?>;

function create3DChart(id, data, color) {
  new Chart(document.getElementById(id), {
    type:'line',
    data: {
      labels: data.map((_,i)=>i+1),
      datasets:[{
        data:data,
        borderColor: color,
        backgroundColor: color+'55',
        fill:true,
        tension:0.4,
        pointRadius:4,
        pointBackgroundColor:color,
        pointHoverRadius:6,
        borderWidth:3
      }]
    },
    options: {
      responsive:true,
      maintainAspectRatio:false,
      plugins:{ legend:{ display:false } },
      scales:{ x:{ display:false }, y:{ beginAtZero:false } }
    }
  });
}

create3DChart('tempChart', tempData, '#D2691E');
create3DChart('humChart', humData, '#4B2E1E');
create3DChart('weightChart', weightData, '#4B2E1E'); 
</script>

</body>
</html>
