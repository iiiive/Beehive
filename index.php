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
    background-image: url("https://static.vecteezy.com/system/resources/previews/000/532/210/original/vector-bee-hive-background.jpg");
        background-repeat: no-repeat;
        background-size: cover;
        background-attachment: fixed;
        display: flex;
        min-height: 100vh;
        color: #74512D; /* coffee for text readability */
        font-family: Arial, sans-serif;
    }

    .wrapper {
        width: 95%;
        margin: auto;
    }

    h2 {
        font-family: 'Cursive', 'Brush Script MT', sans-serif;
        font-size: 4rem;
        margin-bottom: 40px;
        color: #0B0806; /* bright yellow for headings */
        text-shadow: 2px 2px 5px rgba(0,0,0,0.6);
    }

    /* Fancy Button */
    .btn {
        padding: 0.6rem 1.2rem;
        font-weight: 700;
        background: #FFF2A3; /* vanilla */
        color: #0B0806; /* smoky black for contrast */
        cursor: pointer;
        border-radius: 0.5rem;
        border: 2px solid #74512D; /* coffee */
        transition: all 0.3s ease;
    }

    .btn:hover {
        background: #fae76aff; /* school-bus yellow on hover */
        color: #0B0806;
        box-shadow: 0px 4px 10px rgba(0,0,0,0.3);
    }

    /* Search Bar */
    .group {
        display: flex;
        align-items: center;
        position: relative;
        max-width: 220px;
        margin-right: 10px;
    }

    .input {
        font-family: "Montserrat", sans-serif;
        width: 100%;
        height: 45px;
        padding-left: 2.5rem;
        border-radius: 12px;
        border: 1px solid #74512D; /* coffee */
        background-color: #E9E7D8; /* eggshell */
        color: #0B0806; /* smoky black */
        outline: none;
        transition: all 0.25s ease;
    }

    .input::placeholder {
        color: #74512D; /* coffee placeholder */
    }

    .input:focus {
        border-color: #FEDE16; /* highlight focus with yellow */
        box-shadow: 0 0 5px #FEDE16;
    }

    .search-icon {
        position: absolute;
        left: 1rem;
        fill: #74512D; /* coffee */
        width: 1rem;
        height: 1rem;
        pointer-events: none;
    }

    /* Clean Table */
    .custom-table {
        width: 100%;
        margin: 20px auto;
        border-collapse: collapse;
        text-align: left;
        background: #E9E7D8; /* vanilla */
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0px 4px 20px rgba(0,0,0,0.1);
        color: #0B0806; /* smoky black for readability */
    }

    .custom-table thead {
        background-color: #74512D; /* coffee */
        color: #ffffffff; /* school-bus yellow */
    }

    .custom-table th, .custom-table td {
        padding: 0.9em 1em;
        border-bottom: 1px solid #E9E7D8; /* eggshell border */
    }

    .custom-table tbody tr:hover {
        background-color: #fae76aff; /* bright yellow hover */
        color: #0B0806;
        transition: 0.3s ease;
    }

    /* Animated View Button */
    .cta {
        position: relative;
        margin: auto;
        padding: 8px 16px;
        border: none;
        background: #FFF2A3; /* vanilla */
        color: #0B0806; /* smoky black */
        font-weight: 700;
        cursor: pointer;
        border-radius: 25px;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .cta:hover {
        background: #74512D; /* yellow hover */
        box-shadow: 0px 4px 10px rgba(0,0,0,0.3);
    }

    .cta svg {
        fill: #74512D; /* coffee icon */
    }
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

                            <!-- New Search Bar -->
                            <div class="group">
                              <svg viewBox="0 0 24 24" aria-hidden="true" class="search-icon">
                                <g>
                                  <path
                                    d="M21.53 20.47l-3.66-3.66C19.195 15.24 20 13.214 20 11c0-4.97-4.03-9-9-9s-9 4.03-9 9 4.03 9 9 9c2.215 0 4.24-.804 5.808-2.13l3.66 3.66c.147.146.34.22.53.22s.385-.073.53-.22c.295-.293.295-.767.002-1.06zM3.5 11c0-4.135 3.365-7.5 7.5-7.5s7.5 3.365 7.5 7.5-3.365 7.5-7.5 7.5-7.5-3.365-7.5-7.5z"
                                  ></path>
                                </g>
                              </svg>
                              <input
                                id="query"
                                class="input"
                                type="search"
                                placeholder="Search..."
                                name="search"
                                value="<?php echo htmlspecialchars($search); ?>"
                              />
                            </div>

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
