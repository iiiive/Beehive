<?php
if (isset($_GET["reading_id"]) && !empty(trim($_GET["reading_id"]))) {
    require_once "config.php";
    
    $sql = "SELECT * FROM beehive_readings WHERE reading_id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $param_reading_id);
        $param_reading_id = trim($_GET["reading_id"]);
        
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                // Assign values
                $timestamp   = $row["timestamp"];
                $temperature = $row["temperature"];
                $humidity    = $row["humidity"];
                $weight      = $row["weight"];
                $fan_status  = $row["fan_status"];
            } else {
                header("location: error.php");
                exit();
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
    mysqli_stmt_close($stmt);
    mysqli_close($link);
} else {
    header("location: error.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Beehive Record</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .wrapper {
            width: 600px;
            margin: 0 auto;
        }
        body {
            background-image: url("https://png.pngtree.com/background/20210716/original/pngtree-white-abstract-vector-web-background-design-picture-image_1354906.jpg");
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .btn-cl {
            background-color: #134074;
            border-color: #134074;
        }
        .btn-cl:hover {
            background-color: #134074;
            border-color: #134074;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="mt-5 mb-3">View Beehive Record</h1>

                    <div class="form-group">
                        <label>Timestamp</label>
                        <p><b><?php echo $timestamp; ?></b></p>
                    </div>
                    <div class="form-group">
                        <label>Temperature (°C)</label>
                        <p><b><?php echo $temperature; ?></b></p>
                    </div>
                    <div class="form-group">
                        <label>Humidity (%)</label>
                        <p><b><?php echo $humidity; ?></b></p>
                    </div>
                    <div class="form-group">
                        <label>Weight (kg)</label>
                        <p><b><?php echo $weight; ?></b></p>
                    </div>
                    <div class="form-group">
                        <label>Fan Status</label>
                        <p><b><?php echo $fan_status; ?></b></p>
                    </div>

                    <p><a href="index.php" class="btn btn-primary btn-cl">Back</a></p>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
