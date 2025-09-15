<?php
require_once "config.php";

$search = "";
$filter = isset($_GET['filter']) ? $_GET['filter'] : "";

$sql = "SELECT * FROM beehive_readings";

if (!empty($filter)) {
    if ($filter == "highTemp") {
        $sql = "SELECT * FROM beehive_readings WHERE temperature >= 35";
    } elseif ($filter == "lowHumidity") {
        $sql = "SELECT * FROM beehive_readings WHERE humidity <= 50";
    } elseif ($filter == "orderWeight") {
        $sql = "SELECT * FROM beehive_readings ORDER BY weight DESC, timestamp ASC";
    } elseif ($filter == "fanOn") {
        $sql = "SELECT * FROM beehive_readings WHERE fan_status = 'ON'";
    }
} elseif (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = trim($_GET['search']);
    $sql = "SELECT * FROM beehive_readings 
            WHERE reading_id LIKE ? 
               OR timestamp LIKE ? 
               OR temperature LIKE ? 
               OR humidity LIKE ? 
               OR weight LIKE ? 
               OR fan_status LIKE ?";
}

if (strpos($sql, '?') !== false) {
    if ($stmt = mysqli_prepare($link, $sql)) {
        $param = "%" . $search . "%";
        mysqli_stmt_bind_param($stmt, "ssssss", $param, $param, $param, $param, $param, $param);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    }
} else {
    $result = mysqli_query($link, $sql);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Beehive Monitoring Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url("https://t3.ftcdn.net/jpg/06/31/48/06/360_F_631480602_mStNuYekDgq1eU9qbAKCtk0V6LxBZxBw.jpg");
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed;
            display: flex;
            min-height: 100vh;
            color: #4F200D;
        }
        .wrapper {
            width: 95%;
            margin: auto;
        }
        h2 {
            font-family: 'Cursive', 'Brush Script MT', sans-serif;
            font-size: 3rem;
            margin-bottom: 40px;
            color: #1f1111ff;
            text-shadow: 2px 2px 5px rgba(0,0,0,0.6);
        }
        /* Fancy Button */
        .btn {
          padding: 0.6rem 1.2rem;
          font-weight: 700;
          background: rgba(44, 40, 21, 1);
          color: white;
          cursor: pointer;
          border-radius: 0.5rem;
          border-bottom: 2px solid white;
          border-right: 2px solid brown;
          border-top: 2px solid white;
          border-left: 2px solid white;
          transition-duration: 1s;
          transition-property: border-top, border-left, border-bottom, border-right,
            box-shadow;
        }
        .btn:hover {
          border-top: 2px solid brown;
          border-left: 2px solid brown;
          border-bottom: 2px solid rgba(238, 224, 103, 1);
          border-right: 2px solid rgba(224, 238, 103, 1);
          box-shadow: rgba(240, 221, 46, 0.4) 5px 5px,
                      rgba(240, 237, 46, 0.3) 10px 10px,
                      rgba(240, 188, 46, 0.2) 15px 15px;
        }

        /* Clean Table Style */
        .custom-table {
            width: 100%;
            margin: 20px auto;
            border-collapse: collapse;
            text-align: left;
            background: rgba(255,255,255,0.95);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0px 4px 20px rgba(0,0,0,0.1);
        }
        .custom-table thead {
            background-color: #745d1eff;
            color: #fff;
        }
        .custom-table th, 
        .custom-table td {
            padding: 0.9em 1em;
            border-bottom: 1px solid #ddd;
        }
        .custom-table tbody tr:hover {
            background-color: #fceac1;
            transition: 0.3s ease;
        }
        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 1rem;
            font-weight: bold;
            color: #fff;
            background: #74512d;
            border-radius: 20px;
            text-decoration: none;
            box-shadow: 4px 4px 10px rgba(0,0,0,0.3);
            transition: background 0.3s ease, transform 0.2s ease;
        }
        .back-btn:hover {
            background: #feba17;
            color: #333;
            transform: scale(1.05);
        }

        /* Animated View Button */
        .cta {
          position: relative;
          margin: auto;
          padding: 8px 16px;
          transition: all 0.2s ease;
          border: none;
          background: none;
          cursor: pointer;
          text-decoration: none;
        }
        .cta:before {
          content: "";
          position: absolute;
          top: 0;
          left: 0;
          display: block;
          border-radius: 50px;
          background: #c7bd2eff;
          width: 45px;
          height: 45px;
          transition: all 0.3s ease;
        }
        .cta span {
          position: relative;
          font-size: 16px;
          font-weight: 700;
          letter-spacing: 0.05em;
          color: #000000ff;
        }
        .cta svg {
          position: relative;
          top: 2px;
          margin-left: 10px;
          fill: none;
          stroke-linecap: round;
          stroke-linejoin: round;
          stroke: #e7c943ff;
          stroke-width: 2;
          transform: translateX(-5px);
          transition: all 0.3s ease;
        }
        .cta:hover:before {
          width: 100%;
          background: #c0a14dff;
        }
        .cta:hover svg {
          transform: translateX(0);
        }
        .cta:active {
          transform: scale(0.95);
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="mt-5 mb-3 clearfix d-flex justify-content-between align-items-center">
                        <a href="frontend/database.php" class="back-btn"> < Back</a>
                        <h2>Beehive Monitoring Records</h2>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <form method="get" class="d-flex align-items-center flex-wrap gap-2">
                            <input type="text" name="search" 
                                   class="form-control form-control-sm me-2" 
                                   placeholder="Search..."
                                   value="<?php echo htmlspecialchars($search); ?>">

                            <button type="submit" class="btn">Search</button>
                            <a href="index.php" class="btn">Reset</a>

                            <div class="dropdown">
                                <button class="btn dropdown-toggle" type="button" id="filterDropdown"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    Filters
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                                    <li><a class="dropdown-item" href="index.php">Show All</a></li>
                                    <li><a class="dropdown-item" href="index.php?filter=highTemp">Temperature ≥ 35°C</a></li>
                                    <li><a class="dropdown-item" href="index.php?filter=lowHumidity">Humidity ≤ 50%</a></li>
                                    <li><a class="dropdown-item" href="index.php?filter=orderWeight">Order by Weight</a></li>
                                    <li><a class="dropdown-item" href="index.php?filter=fanOn">Fan Status = ON</a></li>
                                </ul>
                            </div>
                        </form>
                    </div>

                    <?php
                    if ($result && mysqli_num_rows($result) > 0) {
                        echo '<table class="custom-table">';
                            echo "<thead>";
                                echo "<tr>";
                                    echo "<th>Reading ID</th>";
                                    echo "<th>Timestamp</th>";
                                    echo "<th>Temperature (°C)</th>";
                                    echo "<th>Humidity (%)</th>";
                                    echo "<th>Weight (kg)</th>";
                                    echo "<th>Fan Status</th>";
                                    echo "<th>Status</th>";
                                    echo "<th>Action</th>";
                                echo "</tr>";
                            echo "</thead>";
                            echo "<tbody>";
                            while ($row = mysqli_fetch_array($result)) {
                                echo "<tr>";
                                    echo "<td>" . $row['reading_id'] . "</td>";
                                    echo "<td>" . $row['timestamp'] . "</td>";
                                    echo "<td>" . $row['temperature'] . "</td>";
                                    echo "<td>" . $row['humidity'] . "</td>";
                                    echo "<td>" . $row['weight'] . "</td>";
                                    echo "<td>" . $row['fan_status'] . "</td>";
                                    echo "<td>" . $row['status'] . "</td>";
                                    echo "<td>";
                                    echo '<a href="read.php?reading_id='. $row['reading_id'] .'" class="cta"><span>View</span>
                                            <svg width="15px" height="10px" viewBox="0 0 13 10">
                                                <path d="M1,5 L11,5"></path>
                                                <polyline points="8 1 12 5 8 9"></polyline>
                                            </svg>
                                          </a>';
                                    echo "</td>";
                                echo "</tr>";
                            }
                            echo "</tbody>";                            
                        echo "</table>";
                        mysqli_free_result($result);
                    } else {
                        echo '<div class="alert alert-danger"><em>No records were found.</em></div>';
                    }

                    mysqli_close($link);
                    ?>
                </div>
            </div>        
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
