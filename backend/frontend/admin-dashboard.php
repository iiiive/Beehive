<?php
require_once "../config.php";



// Query data from your table
$sql = "SELECT timestamp, temperature, humidity, weight 
        FROM beehive_readings 
        ORDER BY timestamp ASC";
$result = mysqli_query($link, $sql);

$timestamps   = [];
$temperatures = [];
$humidities   = [];
$weights      = [];

while ($row = mysqli_fetch_assoc($result)) {
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
    background: url('honey.jpeg') no-repeat center/cover;
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

table {
    width: 100%;
    margin-top: 15px;
}

th, td { padding: 10px; text-align: center; }
th { background: #FFD93D; color: #4B2E1E; }

.sticky-sidebar { position: sticky; top: 20px; }
</style>
</head>
<body>

<div class="dashboard-header">
  <div class="title">
    <img src="bee.png" alt="HiveCare Logo"> 
    <span>HiveCare - Admin Dashboard</span>
  </div>
  <div>
    <a href="../index.php" class="settings-btn"><i class="bi bi-database"></i> Database</a>
    <a href="manage_users.php" class="settings-btn"><i class="bi bi-gear-fill"></i> Manage Users</a>
    <a href="frontindex.php" class="logout-btn"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </div>
</div>

<div class="container mt-4">
  <div class="row g-4"> 
    <div class="col-lg-3 col-md-6">
      <div class="card p-3">
        <h5 class="card-title"><i class="bi bi-thermometer-half"></i> Temperature</h5>
        <div class="value"><?php echo $latestTemp; ?> °C</div>
        <div class="<?php echo ($latestTemp>32||$latestTemp<20)?'status-bad':'status-good';?>">
          <?php echo ($latestTemp>32||$latestTemp<20)?'Temperature is Bad ✖':'Temperature is Good ✔';?>
        </div>  
        <canvas id="tempChart"></canvas>
      </div>
    </div>

    <div class="col-lg-3 col-md-6">
      <div class="card p-3">
        <h5 class="card-title"><i class="bi bi-droplet"></i> Humidity</h5>
        <div class="value"><?php echo $latestHum; ?> %</div>
        <div class="<?php echo ($latestHum>=40&&$latestHum<=70)?'status-good':'status-bad';?>">
          <?php echo ($latestHum>=40&&$latestHum<=70)?'Humidity is Good ✔':'Humidity is Bad ✖';?>
        </div>
        <canvas id="humChart"></canvas>
      </div>
    </div>

    <div class="col-lg-3 col-md-6">
      <div class="card p-3">
        <h5 class="card-title"><i class="bi bi-box-seam"></i> Weight</h5>
        <div class="value"><?php echo $latestWeight; ?> kg</div>
        <div class="<?php echo ($latestWeight>=3)?'status-good':'status-bad';?>">
          <?php echo ($latestWeight>=3)?'The hive is Full ✔':'The hive is Light ✖';?>
        </div>
        <canvas id="weightChart"></canvas>
      </div>
    </div>


    <div class="col-lg-3 col-md-6">
      <div class="card p-3">
        <h5 class="card-title"><i class="bi bi-lightning-charge"></i> Fan Control</h5>
        <button class="fan-btn fan-auto" onclick="controlFan('auto')">Automatic</button>
        <button class="fan-btn fan-on" onclick="controlFan('on')">Turn On</button>
        <button class="fan-btn fan-off" onclick="controlFan('off')">Turn Off</button>
        <div id="fan-status">Fan mode: Automatic</div>
      </div>
    </div>
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
</script>
</body>
</html>
