<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Beehive Monitoring Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-image: url("https://static.vecteezy.com/system/resources/previews/000/532/210/original/vector-bee-hive-background.jpg");
      background-repeat: no-repeat;
      background-size: cover;
      background-attachment: fixed;
      display: flex;
      min-height: 100vh;
      color: #74512D;
      font-family: Arial, sans-serif;
    }
    .wrapper { width: 95%; margin: auto; }
    h2 {
      font-family: 'Cursive', 'Brush Script MT', sans-serif;
      font-size: 4rem; margin-bottom: 40px; color: #0B0806;
      text-shadow: 2px 2px 5px rgba(0,0,0,0.6);
    }
    .btn {
      padding: 0.6rem 1.2rem; font-weight: 700; background: #FFF2A3;
      color: #0B0806; border-radius: 0.5rem; border: 2px solid #74512D;
      transition: all 0.3s ease;
    }
    .btn:hover { background: #fae76a; box-shadow: 0px 4px 10px rgba(0,0,0,0.3); }
    .group { display: flex; align-items: center; position: relative; max-width: 220px; margin-right: 10px; }
    .input {
      width: 100%; height: 45px; padding-left: 2.5rem; border-radius: 12px;
      border: 1px solid #74512D; background-color: #E9E7D8; color: #0B0806;
    }
    .search-icon { position: absolute; left: 1rem; fill: #74512D; width: 1rem; height: 1rem; }
    .custom-table { width: 100%; margin: 20px auto; border-collapse: collapse;
      background: #E9E7D8; border-radius: 10px; overflow: hidden;
      box-shadow: 0px 4px 20px rgba(0,0,0,0.1); color: #0B0806;
    }
    .custom-table thead { background-color: #74512D; color: #fff; }
    .custom-table th, .custom-table td { padding: 0.9em 1em; border-bottom: 1px solid #E9E7D8; }
    .custom-table tbody tr:hover { background-color: #fae76a; transition: 0.3s ease; }
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">

          <div class="mt-5 mb-3 clearfix d-flex justify-content-between align-items-center">
            <a href="frontend/database.php" class="btn"> < Back</a>
            <h2>Beehive Monitoring Records</h2>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <form method="get" class="d-flex align-items-center flex-wrap gap-2">
              <div class="group">
                <svg viewBox="0 0 24 24" class="search-icon">
                  <path d="M21.53 20.47l-3.66-3.66C19.195 15.24 20 13.214 20 11c0-4.97-4.03-9-9-9s-9 
                    4.03-9 9 4.03 9 9 9c2.215 0 4.24-.804 5.808-2.13l3.66 3.66c.147.146.34.22.53.22
                    s.385-.073.53-.22c.295-.293.295-.767.002-1.06zM3.5 11c0-4.135 3.365-7.5 
                    7.5-7.5s7.5 3.365 7.5 7.5-3.365 7.5-7.5 7.5-7.5-3.365-7.5-7.5z"></path>
                </svg>
                <input id="query" class="input" type="search" placeholder="Search..." name="search"/>
              </div>
              <button type="submit" class="btn">Search</button>
              <a href="index.php" class="btn">Reset</a>

              <!-- Filter dropdown -->
              <div class="dropdown">
                <button class="btn dropdown-toggle" type="button" id="filterDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                  Filters
                </button>
                <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                  <li><a class="dropdown-item" href="?filter=statusGood">Status: Good</a></li>
                  <li><a class="dropdown-item" href="?filter=statusBad">Status: Bad</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="?filter=highTemp">High Temperature</a></li>
                  <li><a class="dropdown-item" href="?filter=normalTemp">Normal Temperature</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="?filter=lowHumidity">Low Humidity</a></li>
                  <li><a class="dropdown-item" href="?filter=normalHumidity">Normal Humidity</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="?filter=fanOn">Fan Status: ON</a></li>
                  <li><a class="dropdown-item" href="?filter=fanOff">Fan Status: OFF</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="?filter=orderAsc">Order: Oldest First</a></li>
                  <li><a class="dropdown-item" href="?filter=orderDesc">Order: Latest First</a></li>
                </ul>
              </div>
              <a href="BeehiveReadingsCSV.php" class="btn">Get a Copy</a>

            </form>
          </div>

          <table class="custom-table">
            <thead>
              <tr>
                <th>Reading ID</th>
                <th>Timestamp</th>
                <th>Temperature (°C)</th>
                <th>Humidity (%)</th>
                <th>Weight (kg)</th>
                <th>Fan Status</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="table-body"></tbody>
          </table>

        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    async function reloadTable() {
      try {
        const params = new URLSearchParams(window.location.search);
        const res = await fetch("beehivetable.php?" + params.toString());
        const data = await res.json();

        const tbody = document.getElementById("table-body");
        tbody.innerHTML = "";

        if (data.length === 0) {
          tbody.innerHTML = `<tr><td colspan="8" class="text-center">No records found</td></tr>`;
          return;
        }

        data.forEach(row => {
          const tr = document.createElement("tr");
          tr.innerHTML = `
            <td>${row.reading_id}</td>
            <td>${row.timestamp}</td>
            <td>${row.temperature}</td>
            <td>${row.humidity}</td>
            <td>${row.weight}</td>
            <td>${row.fan_status == 1 ? "ON" : "OFF"}</td>
            <td>${row.status}</td>
            <td><a href="read.php?reading_id=${row.reading_id}" class="btn btn-sm">View</a></td>
          `;
          tbody.appendChild(tr);
        });
      } catch (err) {
        console.error("Table fetch error:", err);
      }
    }

    reloadTable();
    setInterval(reloadTable, 5000);
  </script>
</body>
</html>
