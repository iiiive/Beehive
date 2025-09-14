<?php
require_once "../config.php"; // make sure this defines $link as your mysqli connection

// Query data from your table (change table name/columns if different)
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

// pass arrays to JS
$temperature_history = $temperatures;
$humidity_history    = $humidities;
$weight_history      = $weights;

mysqli_close($link);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>HiveCare - User Dashboard</title>

<!-- Bootstrap & Chart.js -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<style>
  /* page background */
  body{
    margin:0;
    font-family: "Raleway", Arial, sans-serif;
    background:#808080;
    color:#212121;
  }
  .header {
    background: linear-gradient(90deg,#ffeb3b,#d4a373);
    padding:14px 22px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    box-shadow:0 4px 10px rgba(0,0,0,0.15);
  }
  .header .title { font-size:1.6rem; font-weight:700; }
  .header .logout { background:#6d4c41; color:#fff; padding:8px 12px; border-radius:8px; text-decoration:none; }

  /* container */
  .container-main {
    max-width:1200px;
    margin:28px auto;
    padding:0 12px;
  }

  /* layout for cards (flex) */
  .dashboard-cards {
    display:flex;
    gap:20px;
    flex-wrap:wrap;
    justify-content:center;
    align-items:flex-start;
  }

  /* card look */
  .metric-card {
    background: linear-gradient(145deg,#FFF8DC,#f3e4b0);
    border-radius:16px;
    box-shadow: 6px 6px 18px rgba(0,0,0,0.18);
    padding:18px;
    width: 320px;          /* fixed width to keep consistent */
    min-height: 260px;
    display:flex;
    flex-direction:column;
    align-items:center;
  }

  .metric-card h5 { margin-bottom:6px; color:#4b2e1e; }
  .metric-value { font-size:2rem; font-weight:700; color:#4B2E1E; margin-bottom:8px; }
  .status-good { background:#ffd83dd8; color:#4b2e1e; padding:8px 14px; border-radius:12px; font-weight:700; }
  .status-bad  { background:#d2691ed2; color:#fff; padding:8px 14px; border-radius:12px; font-weight:700; }

  /* chart container constrains the canvas size */
  .chart-container {
    width: 100%;
    height: 140px;            /* fixed height so charts remain compact */
    margin-top: 12px;
    position: relative;
  }
  .chart-container canvas {
    width: 100% !important;
    height: 100% !important;
  }

  /* fan control card */
  .fan-card { width: 660px; max-width: calc(100% - 40px); padding:20px; border-radius:16px;
    background: linear-gradient(145deg,#FFF8DC,#f3e4b0);
    box-shadow:6px 6px 18px rgba(0,0,0,0.18);
  }
  .fan-btn { padding:8px 14px; border-radius:10px; border:none; margin-right:8px; font-weight:700; cursor:pointer; }
  .fan-auto { background:#FFD93D; color:#4B2E1E; }
  .fan-on   { background:#4B2E1E; color:#FFD93D; }
  .fan-off  { background:#D2691E; color:#fff; }

  /* responsiveness */
  @media (max-width:1000px){
    .metric-card { width: 300px; }
    .fan-card { width:100%; }
  }
  @media (max-width:700px){
    .metric-card { width: 100%; max-width: 420px; }
    .dashboard-cards { gap:14px; }
  }
</style>
</head>
<body>

<header class="header">
  <div class="title">🐝 HiveCare - User Dashboard</div>
  <a class="logout" href="frontindex.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
</header>

<div class="container-main">
  <div class="dashboard-cards">
    <!-- Temperature card -->
    <div class="metric-card">
      <h5><i class="bi bi-thermometer-half"></i> Temperature</h5>
      <div class="metric-value"><?php echo htmlspecialchars($latestTemp); ?> °C</div>
      <div class="<?php echo ($latestTemp>32 || $latestTemp<20) ? 'status-bad' : 'status-good'; ?>">
        <?php echo ($latestTemp>32 || $latestTemp<20) ? 'Temperature is Bad ✖' : 'Temperature is Good ✔'; ?>
      </div>

      <div class="chart-container">
        <canvas id="tempChart"></canvas>
      </div>
    </div>

    <!-- Humidity card -->
    <div class="metric-card">
      <h5><i class="bi bi-droplet"></i> Humidity</h5>
      <div class="metric-value"><?php echo htmlspecialchars($latestHum); ?> %</div>
      <div class="<?php echo ($latestHum>=40 && $latestHum<=70) ? 'status-good' : 'status-bad'; ?>">
        <?php echo ($latestHum>=40 && $latestHum<=70) ? 'Humidity is Good ✔' : 'Humidity is Bad ✖'; ?>
      </div>

      <div class="chart-container">
        <canvas id="humChart"></canvas>
      </div>
    </div>

    <!-- Weight card -->
    <div class="metric-card">
      <h5><i class="bi bi-box-seam"></i> Weight</h5>
      <div class="metric-value"><?php echo htmlspecialchars($latestWeight); ?> kg</div>
      <div class="<?php echo ($latestWeight>=20) ? 'status-good' : 'status-bad'; ?>">
        <?php echo ($latestWeight>=20) ? 'The hive is Full ✔' : 'The hive is Low ✖'; ?>
      </div>

      <div class="chart-container">
        <canvas id="weightChart"></canvas>
      </div>
    </div>
  </div>

  <div style="height:24px;"></div>

  <!-- Fan control (wide card) -->
  <div style="display:flex; justify-content:center; align-items:center;">
    <div class="fan-card">
      <h5><i class="bi bi-lightning-charge" style="color:#FFD93D;"></i> Fan Control</h5>
      <div style="margin-top:10px;">
        <button class="fan-btn fan-auto" onclick="controlFan('auto', this)"><i class="bi bi-gear-fill"></i> Automatic</button>
        <button class="fan-btn fan-on" onclick="controlFan('on', this)"><i class="bi bi-toggle-on"></i> Turn On</button>
        <button class="fan-btn fan-off" onclick="controlFan('off', this)"><i class="bi bi-toggle-off"></i> Turn Off</button>
      </div>
      <div id="fan-status" style="margin-top:14px; font-weight:700;">Fan mode: Automatic</div>
    </div>
  </div>
</div>

<script>
  // Data from PHP
  const tempData  = <?php echo json_encode($temperature_history); ?>;
  const humData   = <?php echo json_encode($humidity_history); ?>;
  const weightData= <?php echo json_encode($weight_history); ?>;

  // Small helper to create a compact line chart inside fixed container
  function createCompactLineChart(canvasId, data, color){
    const ctx = document.getElementById(canvasId).getContext('2d');
    return new Chart(ctx, {
      type: 'line',
      data: {
        labels: data.map((_,i) => i+1),
        datasets: [{
          data: data,
          borderColor: color,
          backgroundColor: color + '55',
          fill: true,
          tension: 0.35,
          pointRadius: 3,
          pointBackgroundColor: color,
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false, // allow css to control height
        plugins: {
          legend: { display: false }
        },
        scales: {
          x: { display: false },
          y: {
            beginAtZero: false,
            ticks: { maxTicksLimit: 4 }
          }
        },
        elements: {
          line: { capBezierPoints: true }
        }
      }
    });
  }

  // create charts
  createCompactLineChart('tempChart', tempData, '#D2691E');
  createCompactLineChart('humChart', humData, '#4B2E1E');
  createCompactLineChart('weightChart', weightData, '#4B2E1E');

  // fan control UI
  function controlFan(action, btn) {
    // visual toggling
    document.querySelectorAll('.fan-btn').forEach(b => {
      b.style.transform = 'scale(1)';
      b.style.boxShadow = '';
    });
    btn.style.transform = 'scale(1.05)';
    btn.style.boxShadow = '0 6px 18px rgba(0,0,0,0.2)';

    const status = document.getElementById('fan-status');
    status.innerText = 'Fan mode: ' + (action === 'auto' ? 'Automatic' : (action === 'on' ? 'On' : 'Off'));

    console.log('Fan set to:', action);

    // TODO: send action to server via fetch() if you have an API endpoint to actually toggle hardware
    // e.g.
    // fetch('/api/fan', { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({mode:action}) });
  }
</script>

</body>
</html>
