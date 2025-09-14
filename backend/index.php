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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .wrapper {
            width: 1100px;
            margin: 30px auto;
        }
        .table-custom thead th {
            background-color: #4F200D !important;
            color: #ffffff !important;
            text-align: center;
        }
        .table-custom tbody tr {
            background-color: #F6F1E9 !important;
        }
        body {
            background-image: url("https://t3.ftcdn.net/jpg/06/31/48/06/360_F_631480602_mStNuYekDgq1eU9qbAKCtk0V6LxBZxBw.jpg");
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed;
        }
        .btn-cl {
            background-color: #FF9A00;
            border-color: #FF9A00;
        }
        .btn-cl:hover {
            opacity: 0.7;
            background-color: #FF9A00;
            border-color: #FF9A00;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="mt-5 mb-3 clearfix d-flex justify-content-between align-items-center">

                    <a href="\admin-dashboard.php" class="settings-btn"><i class="bi bi-database"></i>back</a>
                        <h2 class="pull-left">Beehive Monitoring Records</h2>


                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <form method="get" class="d-flex align-items-center">
                            <input type="text" name="search" 
                                   class="form-control form-control-sm me-2" 
                                   placeholder="Search..."
                                   value="<?php echo htmlspecialchars($search); ?>">

                            <button type="submit" class="btn btn-primary btn-sm btn-cl me-2">
                                Search
                            </button>

                            <a href="index.php" class="btn btn-secondary btn-sm btn-cl me-2">
                                Reset
                            </a>

                            <div class="dropdown">
                                <button class="btn btn-secondary btn-sm dropdown-toggle btn-cl" type="button" id="filterDropdown"
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
                        echo '<table class="table table-sm table-hover table-striped table-custom">';
                            echo "<thead>";
                                echo "<tr>";
                                    echo "<th>Reading ID</th>";
                                    echo "<th>Timestamp</th>";
                                    echo "<th>Temperature (°C)</th>";
                                    echo "<th>Humidity (%)</th>";
                                    echo "<th>Weight (kg)</th>";
                                    echo "<th>Fan Status</th>";
                                    echo "<th>Status</th>";
                                    echo "<th>Options</th>";
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
                                        echo '<a href="read.php?reading_id='. $row['reading_id'] .'" class="btn btn-info btn-sm mr-1" title="Read"><i class="fa fa-eye"></i></a>';
                                        echo '<a href="update.php?reading_id='. $row['reading_id'] .'" class="btn btn-warning btn-sm mr-1" title="Update"><i class="fa fa-pencil"></i></a>';
                                        echo '<a href="delete.php?reading_id='. $row['reading_id'] .'" class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash"></i></a>';
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
