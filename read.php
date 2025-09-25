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
body {
    font-family: 'Raleway', sans-serif;
    min-height: 100vh;
    background: #ebeac5ff; /* plain white background */
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
    color: #212121;
}

.wrapper {
    width: 600px;
    background: #e0d356ff; /* soft yellow tone */
    border-radius: 20px;
    border: 2px solid #755536ff; /* light brown border */
    box-shadow: 0px 6px 15px rgba(0,0,0,0.2);
    padding: 30px 40px;
    text-align: center;
}

h1 {
    color: #42372bff; /* brownish header */
    margin-bottom: 25px;
    font-size: 28px;
    font-weight: 700;
}

.form-group {
    margin-bottom: 18px;
    text-align: left;
}

.form-group label {
    display: block;
    font-weight: bold;
    color: #4e3d27ff; /* darker brown for labels */
    margin-bottom: 6px;
}

.form-group p {
    background: #fff3c4; /* soft yellow highlight */
    color: #4B2E1E; /* dark brown text */
    padding: 12px 15px;
    border-radius: 10px;
    font-weight: bold;
}

.btn-cl {
    width: 100%;
    padding: 14px 0;
    border-radius: 12px;
    background: #755536ff; /* brown button */
    color: #fff8dc; /* light text */
    font-weight: bold;
    border: none;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-cl:hover {
    background: #e7cc2fff; /* brighter yellow on hover */
    color: #4B2E1E;
    transform: translateY(-2px) scale(1.02);
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
