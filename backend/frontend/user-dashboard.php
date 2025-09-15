<?php
require_once "../config.php"; // mysqli connection in $link

// Fetch data
$sql = "SELECT `timestamp`, `temperature`, `humidity`, `weight`
        FROM beehive_readings
        ORDER BY `timestamp` ASC";
$result = mysqli_query($link, $sql);

$timestamps = [];
$temperatures = [];
$humidities = [];
$weights = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $timestamps[] = $row['timestamp'];
        $temperatures[] = floatval($row['temperature']);
        $humidities[] = floatval($row['humidity']);
        $weights[] = floatval($row['weight']);
    }
}

$latestTemp   = round(end($temperatures), 1);
$latestHum    = round(end($humidities), 1);
$latestWeight = round(end($weights), 2);

$temperature_history = $temperatures;
$humidity_history    = $humidities;
$weight_history      = $weights;

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
  background: url('honey.jpeg') no-repeat center center/cover;
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
.container, .dashboard-header, .card, .fan-card { position: relative; z-index: 1; }

/* Header */
.dashboard-header {
  width:100%; padding:15px 25px;
  display:flex; justify-content:space-between; align-items:center;
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
.dashboard-header img { height:70px; width:70px; }
.logout-btn {
  padding:10px 20px; border-radius:15px; font-weight:700;
  color:#fff; background:#4B2E1E; border:none;
  text-decoration:none; box-shadow:0 5px 15px rgba(0,0,0,0.3);
  transition:0.3s;
}
.logout-btn:hover { background:#6B4226; transform: translateY(-2px) scale(1.03); }

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
</style>
</head>
<body>

<div class="dashboard-header">
  <div class="title">
    <img src="bee.png" alt="HiveCare Logo">
    <span>HiveCare - User Dashboard</span>
  </div>
  <a href="homepage.php" class="logout-btn"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<div class="container">
  <!-- Temperature -->
  <div class="card">
    <h5 class="card-title"><i class="bi bi-thermometer-half" style="color:#D2691E;"></i> Temperature</h5>
    <div class="value"><?php echo $latestTemp; ?> °C</div>
    <div class="<?php echo ($latestTemp>32||$latestTemp<20)?'status-bad':'status-good';?>">
      <?php echo ($latestTemp>32||$latestTemp<20)?'Temperature is Bad ✖':'Temperature is Good ✔';?>
    </div>
    <canvas id="tempChart"></canvas>
  </div>

  <!-- Humidity -->
  <div class="card">
    <h5 class="card-title"><i class="bi bi-droplet" style="color:#4B2E1E;"></i> Humidity</h5>
    <div class="value"><?php echo $latestHum; ?> %</div>
    <div class="<?php echo ($latestHum>=40&&$latestHum<=70)?'status-good':'status-bad';?>">
      <?php echo ($latestHum>=40&&$latestHum<=70)?'Humidity is Good ✔':'Humidity is Bad ✖';?>
    </div>
    <canvas id="humChart"></canvas>
  </div>

  <!-- Weight -->
  <div class="card">
    <h5 class="card-title"><i class="bi bi-box-seam" style="color:#FFD93D;"></i> Weight</h5>
    <div class="value"><?php echo $latestWeight; ?> kg</div>
    <div class="<?php echo ($latestWeight>=20)?'status-good':'status-bad';?>">
      <?php echo ($latestWeight>=20)?'Hive is Full ✔':'Hive is Low ✖';?>
    </div>
    <canvas id="weightChart"></canvas>
  </div>

<!-- Fan Control -->
<div class="fan-card">
  <h5 class="card-title">
    <i class="bi bi-lightning-charge" style="color:#FFD93D;"></i> Fan Control
  </h5>
  <div class="fan-controls">
    <button class="fan-btn fan-auto active" onclick="controlFan('auto', this)">
      <i class="bi bi-gear-fill"></i> Automatic
    </button>
    <button class="fan-btn fan-on" onclick="controlFan('on', this)">
      <i class="bi bi-toggle-on"></i> Turn On
    </button>
    <button class="fan-btn fan-off" onclick="controlFan('off', this)">
      <i class="bi bi-toggle-off"></i> Turn Off
    </button>
  </div>
  <div id="fan-status" class="fan-status">Fan mode: Automatic</div>
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
</script>

</body>
</html>
