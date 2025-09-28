<?php
require_once "config.php";

$search = "";
$filter = isset($_GET['filter']) ? $_GET['filter'] : "";
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit  = 10; // rows per page
$offset = ($page - 1) * $limit;

// Base SQL
$sql = "SELECT * FROM admins";
$countSql = "SELECT COUNT(*) as total FROM admins";

// Apply filters
if (!empty($filter)) {
    if ($filter == "active") {
        $sql = "SELECT * FROM admins WHERE status = 'active'";
        $countSql = "SELECT COUNT(*) as total FROM admins WHERE status = 'active'";
    } elseif ($filter == "disabled") {
        $sql = "SELECT * FROM admins WHERE status = 'disabled'";
        $countSql = "SELECT COUNT(*) as total FROM admins WHERE status = 'disabled'";
    } elseif ($filter == "pending") {
        $sql = "SELECT * FROM admins WHERE status = 'pending'";
        $countSql = "SELECT COUNT(*) as total FROM admins WHERE status = 'pending'";
    } elseif ($filter == "recent") {
        $sql = "SELECT * FROM admins ORDER BY created_at DESC";
        $countSql = "SELECT COUNT(*) as total FROM admins";
    }
} elseif (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = trim($_GET['search']);
    $sql = "SELECT * FROM admins 
            WHERE admin_id LIKE ? 
               OR firstname LIKE ? 
               OR lastname LIKE ? 
               OR username LIKE ? 
               OR email LIKE ?";
    $countSql = "SELECT COUNT(*) as total FROM admins 
                 WHERE admin_id LIKE ? 
                    OR firstname LIKE ? 
                    OR lastname LIKE ? 
                    OR username LIKE ? 
                    OR email LIKE ?";
}

// Handle search queries
if (strpos($sql, '?') !== false) {
    if ($stmt = mysqli_prepare($link, $sql . " LIMIT ? OFFSET ?")) {
        $param = "%" . $search . "%";
        mysqli_stmt_bind_param($stmt, "sssssi", $param, $param, $param, $param, $param, $limit, $offset);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    }

    // Count total rows for pagination
    if ($countStmt = mysqli_prepare($link, $countSql)) {
        mysqli_stmt_bind_param($countStmt, "sssss", $param, $param, $param, $param, $param);
        mysqli_stmt_execute($countStmt);
        $countRes = mysqli_stmt_get_result($countStmt);
        $totalRows = mysqli_fetch_assoc($countRes)['total'];
    }
} else {
    // For non-search queries
    $sql .= " LIMIT $limit OFFSET $offset";
    $result = mysqli_query($link, $sql);

    $countRes = mysqli_query($link, $countSql);
    $totalRows = mysqli_fetch_assoc($countRes)['total'];
}

// Calculate total pages
$totalPages = ceil($totalRows / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background-image: url("https://static.vecteezy.com/system/resources/previews/000/532/210/original/vector-bee-hive-background.jpg");
        background-repeat: no-repeat;
        background-size: cover;
        background-attachment: fixed;
        display: block;  
        min-height: 100vh;
        color: #74512D;
        font-family: Arial, sans-serif;
        padding: 30px 50px;
    }
    .custom-table {
        width: 100%;
        margin: 20px auto;
        border-collapse: collapse;
        text-align: left;
        background: #E9E7D8;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0px 4px 20px rgba(0,0,0,0.1);
        color: #0B0806;
    }
    .custom-table thead { background-color: #74512D; color: #fff; }
    .custom-table th, .custom-table td { padding: 0.9em 1em; }
    .pagination { margin-top: 20px; }
    .pagination a {
        margin: 0 5px;
        padding: 8px 12px;
        border: 1px solid #74512D;
        border-radius: 5px;
        background: #FFF2A3;
        color: #0B0806;
        text-decoration: none;
    }
    .pagination a.active { background: #74512D; color: #fff; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="container-fluid">
        <h2 class="text-center">Admin Account Records</h2>

        <?php
        if ($result && mysqli_num_rows($result) > 0) {
            echo '<table class="custom-table">';
            echo "<thead><tr>
                    <th>ID</th><th>Firstname</th><th>Lastname</th>
                    <th>Username</th><th>Email</th><th>Created At</th><th>Options</th>
                  </tr></thead><tbody>";
            while ($row = mysqli_fetch_array($result)) {
                echo "<tr>";
                echo "<td>" . $row['admin_id'] . "</td>";
                echo "<td>" . $row['firstname'] . "</td>";
                echo "<td>" . $row['lastname'] . "</td>";
                echo "<td>" . $row['username'] . "</td>";
                echo "<td>" . $row['email'] . "</td>";
                echo "<td>" . $row['created_at'] . "</td>";
                echo "<td><a href='readadmin.php?admin_id={$row['admin_id']}' class='btn btn-sm'>View</a></td>";
                echo "</tr>";
            }
            echo "</tbody></table>";

            // Pagination links
            echo "<div class='pagination text-center'>";
            if ($page > 1) {
                echo "<a href='?page=" . ($page-1) . "&filter=$filter&search=$search'>Prev</a>";
            }
            for ($i = 1; $i <= $totalPages; $i++) {
                $active = ($i == $page) ? "active" : "";
                echo "<a class='$active' href='?page=$i&filter=$filter&search=$search'>$i</a>";
            }
            if ($page < $totalPages) {
                echo "<a href='?page=" . ($page+1) . "&filter=$filter&search=$search'>Next</a>";
            }
            echo "</div>";
        } else {
            echo '<div class="alert alert-danger"><em>No admins found.</em></div>';
        }
        mysqli_close($link);
        ?>
    </div>
</div>
</body>
</html>
