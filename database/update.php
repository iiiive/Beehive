<?php
require_once "config.php";

$temperature = $humidity = $weight = $fan_status = "";
$temperature_err = $humidity_err = $weight_err = $fan_status_err = "";

if (isset($_POST["reading_id"]) && !empty($_POST["reading_id"])) {
    $reading_id = $_POST["reading_id"];
    
    // Validate Temperature
    $input_temperature = trim($_POST["temperature"]);
    if ($input_temperature === "") {
        $temperature_err = "Please enter temperature.";
    } elseif (!is_numeric($input_temperature)) {
        $temperature_err = "Please enter a valid number.";
    } else {
        $temperature = $input_temperature;
    }
    
    // Validate Humidity
    $input_humidity = trim($_POST["humidity"]);
    if ($input_humidity === "") {
        $humidity_err = "Please enter humidity.";
    } elseif (!is_numeric($input_humidity)) {
        $humidity_err = "Please enter a valid number.";
    } else {
        $humidity = $input_humidity;
    }
    
    // Validate Weight
    $input_weight = trim($_POST["weight"]);
    if ($input_weight === "") {
        $weight_err = "Please enter weight.";
    } elseif (!is_numeric($input_weight)) {
        $weight_err = "Please enter a valid number.";
    } else {
        $weight = $input_weight;
    }

    // Validate Fan Status (0 or 1)
    $input_fan_status = trim($_POST["fan_status"]);
    if ($input_fan_status === "") {
        $fan_status_err = "Please enter fan status.";
    } elseif (!in_array($input_fan_status, ["0", "1"])) {
        $fan_status_err = "Fan status must be 0 (off) or 1 (on).";
    } else {
        $fan_status = $input_fan_status;
    }

    // Update if no errors
    if (empty($temperature_err) && empty($humidity_err) && empty($weight_err) && empty($fan_status_err)) {
        $sql = "UPDATE beehive_readings SET temperature=?, humidity=?, weight=?, fan_status=? WHERE reading_id=?";
         
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "dddii", $param_temperature, $param_humidity, $param_weight, $param_fan_status, $param_reading_id);
            
            $param_temperature = $temperature;
            $param_humidity = $humidity;
            $param_weight = $weight;
            $param_fan_status = $fan_status;
            $param_reading_id = $reading_id;
            
            if (mysqli_stmt_execute($stmt)) {
                header("location: index.php");
                exit();
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
} else {
    if (isset($_GET["reading_id"]) && !empty(trim($_GET["reading_id"]))) {
        $reading_id = trim($_GET["reading_id"]);
        
        $sql = "SELECT * FROM beehive_readings WHERE reading_id = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $param_reading_id);
            $param_reading_id = $reading_id;
            
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
    
                if (mysqli_num_rows($result) == 1) {
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    $temperature = $row["temperature"];
                    $humidity = $row["humidity"];
                    $weight = $row["weight"];
                    $fan_status = $row["fan_status"];
                } else {
                    header("location: error.php");
                    exit();
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    } else {
        header("location: error.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Beehive Reading</title>
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
            background-attachment: fixed;
        }
        .btn-cl {
            background-color: #134074;
            border-color: #134074;
        }
        .btn-cl:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <h2 class="mt-5">Update Beehive Reading</h2>
            <p>Edit the values and submit to update the beehive record.</p>
            <form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="post">
                <div class="form-group">
                    <label>Temperature</label>
                    <input type="text" name="temperature" class="form-control <?php echo (!empty($temperature_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $temperature; ?>">
                    <span class="invalid-feedback"><?php echo $temperature_err;?></span>
                </div>
                <div class="form-group">
                    <label>Humidity</label>
                    <input type="text" name="humidity" class="form-control <?php echo (!empty($humidity_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $humidity; ?>">
                    <span class="invalid-feedback"><?php echo $humidity_err;?></span>
                </div>
                <div class="form-group">
                    <label>Weight</label>
                    <input type="text" name="weight" class="form-control <?php echo (!empty($weight_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $weight; ?>">
                    <span class="invalid-feedback"><?php echo $weight_err;?></span>
                </div>
                <div class="form-group">
                    <label>Fan Status (0 = off, 1 = on)</label>
                    <input type="text" name="fan_status" class="form-control <?php echo (!empty($fan_status_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $fan_status; ?>">
                    <span class="invalid-feedback"><?php echo $fan_status_err;?></span>
                </div>
                <input type="hidden" name="reading_id" value="<?php echo $reading_id; ?>"/>
                <input type="submit" class="btn btn-primary btn-cl" value="Submit">
                <a href="index.php" class="btn btn-secondary ml-2">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>
