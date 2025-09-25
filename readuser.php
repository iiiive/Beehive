<?php
if (isset($_GET["user_id"]) && !empty(trim($_GET["user_id"]))) {
    require_once "config.php";
    
    $sql = "SELECT * FROM users WHERE user_id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $param_user_id);
        $param_user_id = trim($_GET["user_id"]);
        
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                // Assign values
                $firstname       = $row["firstname"];
                $lastname        = $row["lastname"];
                $username        = $row["username"];
                $email           = $row["email"];
                $birthday        = $row["birthday"];
                $address         = $row["address"];
                $contact_number  = $row["contact_number"];
                $created_by      = $row["created_by_admin_id"];
                $created_at      = $row["created_at"];
                $status          = $row["status"];
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
<title>View User Record</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<style>
body {
    font-family: 'Raleway', sans-serif;
    min-height: 100vh;
    background: #ebeac5ff;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
    color: #212121;
}
.wrapper {
    width: 600px;
    background: #e0d356ff;
    border-radius: 20px;
    border: 2px solid #755536ff;
    box-shadow: 0px 6px 15px rgba(0,0,0,0.2);
    padding: 30px 40px;
    text-align: center;
}
h1 {
    color: #42372bff;
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
    color: #4e3d27ff;
    margin-bottom: 6px;
}
.form-group p {
    background: #fff3c4;
    color: #4B2E1E;
    padding: 12px 15px;
    border-radius: 10px;
    font-weight: bold;
}
.btn-cl {
    width: 100%;
    padding: 14px 0;
    border-radius: 12px;
    background: #755536ff;
    color: #fff8dc;
    font-weight: bold;
    border: none;
    text-decoration: none;
    transition: all 0.3s ease;
}
.btn-cl:hover {
    background: #e7cc2fff;
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
                <h1 class="mt-5 mb-3">View User Record</h1>

                <div class="form-group">
                    <label>First Name</label>
                    <p><b><?php echo htmlspecialchars($firstname); ?></b></p>
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <p><b><?php echo htmlspecialchars($lastname); ?></b></p>
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <p><b><?php echo htmlspecialchars($username); ?></b></p>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <p><b><?php echo htmlspecialchars($email); ?></b></p>
                </div>
                <div class="form-group">
                    <label>Birthday</label>
                    <p><b><?php echo htmlspecialchars($birthday); ?></b></p>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <p><b><?php echo htmlspecialchars($address); ?></b></p>
                </div>
                <div class="form-group">
                    <label>Contact Number</label>
                    <p><b><?php echo htmlspecialchars($contact_number); ?></b></p>
                </div>
                <div class="form-group">
                    <label>Created By Admin ID</label>
                    <p><b><?php echo htmlspecialchars($created_by); ?></b></p>
                </div>
                <div class="form-group">
                    <label>Created At</label>
                    <p><b><?php echo htmlspecialchars($created_at); ?></b></p>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <p><b><?php echo htmlspecialchars($status); ?></b></p>
                </div>

                <p><a href="userindex.php" class="btn btn-primary btn-cl">Back</a></p>
            </div>
        </div>        
    </div>
</div>
</body>
</html>
