<?php
require_once "config.php";

$search = "";
$filter = isset($_GET['filter']) ? $_GET['filter'] : "";

$sql = "SELECT * FROM admins";

if (!empty($filter)) {
    if ($filter == "active") {
        $sql = "SELECT * FROM admins WHERE status = 'active'";
    } elseif ($filter == "disabled") {
        $sql = "SELECT * FROM admins WHERE status = 'disabled'";
    } elseif ($filter == "pending") {
        $sql = "SELECT * FROM admins WHERE status = 'pending'";
    } elseif ($filter == "recent") {
        $sql = "SELECT * FROM admins ORDER BY created_at DESC";
    }
} elseif (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = trim($_GET['search']);
    $sql = "SELECT * FROM admins 
            WHERE admin_id LIKE ? 
               OR firstname LIKE ? 
               OR lastname LIKE ? 
               OR username LIKE ? 
               OR email LIKE ?";
}

if (strpos($sql, '?') !== false) {
    if ($stmt = mysqli_prepare($link, $sql)) {
        $param = "%" . $search . "%";
        mysqli_stmt_bind_param($stmt, "sssss", $param, $param, $param, $param, $param);
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
    <title>Admin Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .wrapper { width: 1100px; margin: 30px auto; }
        .table-custom thead th {
            background-color: #3A4D39 !important;
            color: #ffffff !important;
            text-align: center;
        }
        .table-custom tbody tr { background-color: #ECE3CE !important; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="mt-5 mb-3 clearfix d-flex justify-content-between align-items-center">
                    <a href="frontend/database.php" class="btn btn-secondary btn-sm">Back</a>
                    <h2 class="pull-left">Admin Accounts</h2>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <form method="get" class="d-flex align-items-center">
                        <input type="text" name="search" 
                               class="form-control form-control-sm me-2" 
                               placeholder="Search..."
                               value="<?php echo htmlspecialchars($search); ?>">

                        <button type="submit" class="btn btn-primary btn-sm me-2">Search</button>
                        <a href="admins.php" class="btn btn-secondary btn-sm me-2">Reset</a>

                        <div class="dropdown">
                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Filters
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="admins.php">Show All</a></li>
                                <li><a class="dropdown-item" href="admins.php?filter=active">Active</a></li>
                                <li><a class="dropdown-item" href="admins.php?filter=disabled">Disabled</a></li>
                                <li><a class="dropdown-item" href="admins.php?filter=pending">Pending</a></li>
                                <li><a class="dropdown-item" href="admins.php?filter=recent">Most Recent</a></li>
                            </ul>
                        </div>
                    </form>
                </div>

                <?php
                if ($result && mysqli_num_rows($result) > 0) {
                    echo '<table class="table table-sm table-hover table-striped table-custom">';
                        echo "<thead>";
                            echo "<tr>";
                                echo "<th>ID</th>";
                                echo "<th>Firstname</th>";
                                echo "<th>Lastname</th>";
                                echo "<th>Username</th>";
                                echo "<th>Email</th>";
                                echo "<th>Role</th>";
                                echo "<th>Status</th>";
                                echo "<th>Created At</th>";
                                echo "<th>Options</th>";
                            echo "</tr>";
                        echo "</thead>";
                        echo "<tbody>";
                        while ($row = mysqli_fetch_array($result)) {
                            echo "<tr>";
                                echo "<td>" . $row['admin_id'] . "</td>";
                                echo "<td>" . $row['firstname'] . "</td>";
                                echo "<td>" . $row['lastname'] . "</td>";
                                echo "<td>" . $row['username'] . "</td>";
                                echo "<td>" . $row['email'] . "</td>";
                                echo "<td>" . $row['role'] . "</td>";
                                echo "<td>" . $row['status'] . "</td>";
                                echo "<td>" . $row['created_at'] . "</td>";
                                echo "<td>";
                                    echo '<a href="read_admin.php?admin_id='. $row['admin_id'] .'" class="btn btn-info btn-sm mr-1">Read</a>';
                                    echo '<a href="update_admin.php?admin_id='. $row['admin_id'] .'" class="btn btn-warning btn-sm mr-1">Update</a>';
                                    echo '<a href="delete_admin.php?admin_id='. $row['admin_id'] .'" class="btn btn-danger btn-sm">Delete</a>';
                                echo "</td>";
                            echo "</tr>";
                        }
                        echo "</tbody>";                            
                    echo "</table>";
                    mysqli_free_result($result);
                } else {
                    echo '<div class="alert alert-danger"><em>No admins found.</em></div>';
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
