<?php
require_once "config.php"; 

$temperature = $humidity = $weight = $fan_status = "";
$temperature_err = $humidity_err = $weight_err = $fan_status_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate temperature
    $input_temperature = trim($_POST["temperature"]);
    if (empty($input_temperature)) {
        $temperature_err = "Please enter the temperature.";     
    } elseif (!is_numeric($input_temperature)) {
        $temperature_err = "Please enter a valid temperature.";
    } else {
        $temperature = $input_temperature;
    }

    // Validate humidity
    $input_humidity = trim($_POST["humidity"]);
    if (empty($input_humidity)) {
        $humidity_err = "Please enter the humidity.";     
    } elseif (!is_numeric($input_humidity)) {
        $humidity_err = "Please enter a valid humidity value.";
    } else {
        $humidity = $input_humidity;
    }

    // Validate weight
    $input_weight = trim($_POST["weight"]);
    if (empty($input_weight)) {
        $weight_err = "Please enter the hive weight.";     
    } elseif (!is_numeric($input_weight)) {
        $weight_err = "Please enter a valid weight.";
    } else {
        $weight = $input_weight;
    }

    // Validate fan status
    $input_fan_status = trim($_POST["fan_status"]);
    if ($input_fan_status === "" || !in_array($input_fan_status, ["0","1"])) {
        $fan_status_err = "Please enter fan status (0 for OFF, 1 for ON).";
    } else {
        $fan_status = $input_fan_status;
    }

    // Insert into DB if no errors
    if (empty($temperature_err) && empty($humidity_err) && empty($weight_err) && empty($fan_status_err)) {
        $sql = "INSERT INTO beehive_readings (temperature, humidity, weight, fan_status) VALUES (?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "dddi", $param_temperature, $param_humidity, $param_weight, $param_fan_status);
            
            $param_temperature = $temperature;
            $param_humidity = $humidity;
            $param_weight = $weight;
            $param_fan_status = $fan_status;

            if (mysqli_stmt_execute($stmt)) {
                header("location: index.php");
                exit();
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Beehive Reading</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .wrapper { width: 600px; margin: 0 auto; }
        body {
            background-image: url("https://png.pngtree.com/background/20210716/original/pngtree-white-abstract-vector-web-background-design-picture-image_1354906.jpg");
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .btn-cl { background-color: #134074; border-color: #134074; }
        .btn-cl:hover { opacity: 0.9; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5">Add Beehive Reading</h2>
                    <p>Fill out this form to add a new beehive reading.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group">
                            <label>Temperature (°C)</label>
                            <input type="text" name="temperature" class="form-control <?php echo (!empty($temperature_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $temperature; ?>">
                            <span class="invalid-feedback"><?php echo $temperature_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Humidity (%)</label>
                            <input type="text" name="humidity" class="form-control <?php echo (!empty($humidity_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $humidity; ?>">
                            <span class="invalid-feedback"><?php echo $humidity_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Weight (kg)</label>
                            <input type="text" name="weight" class="form-control <?php echo (!empty($weight_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $weight; ?>">
                            <span class="invalid-feedback"><?php echo $weight_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Fan Status</label>
                            <select name="fan_status" class="form-control <?php echo (!empty($fan_status_err)) ? 'is-invalid' : ''; ?>">
                                <option value="">--Select--</option>
                                <option value="1" <?php echo ($fan_status=="1") ? "selected" : ""; ?>>ON</option>
                                <option value="0" <?php echo ($fan_status=="0") ? "selected" : ""; ?>>OFF</option>
                            </select>
                            <span class="invalid-feedback"><?php echo $fan_status_err;?></span>
                        </div>
                        <input type="submit" class="btn btn-primary btn-cl" value="Submit">
                        <a href="index.php" class="btn btn-secondary btn-cl ml-2">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
