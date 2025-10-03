<?php
require_once "config.php";

$search = "";
$filter = isset($_GET['filter']) ? $_GET['filter'] : "";

// Pagination setup
$limit = 10; // rows per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$result = null;
$totalRows = 0;

// Base query
$sql = "SELECT * FROM admins";
$countSql = "SELECT COUNT(*) AS total FROM admins";

// FILTER HANDLING
if (!empty($filter)) {
    if ($filter == "active") {
        $sql = "SELECT * FROM admins WHERE status = 'active'";
        $countSql = "SELECT COUNT(*) AS total FROM admins WHERE status = 'active'";
    } elseif ($filter == "disabled") {
        $sql = "SELECT * FROM admins WHERE status = 'disabled'";
        $countSql = "SELECT COUNT(*) AS total FROM admins WHERE status = 'disabled'";
    } elseif ($filter == "pending") {
        $sql = "SELECT * FROM admins WHERE status = 'pending'";
        $countSql = "SELECT COUNT(*) AS total FROM admins WHERE status = 'pending'";
    } elseif ($filter == "recent") {
        $sql = "SELECT * FROM admins ORDER BY created_at DESC";
        $countSql = "SELECT COUNT(*) AS total FROM admins";
    }
}

// SEARCH HANDLING
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = trim($_GET['search']);
    $sql = "SELECT * FROM admins 
            WHERE admin_id LIKE ? 
               OR firstname LIKE ? 
               OR lastname LIKE ? 
               OR username LIKE ? 
               OR email LIKE ? 
            LIMIT $limit OFFSET $offset";

    $countSql = "SELECT COUNT(*) AS total FROM admins 
            WHERE admin_id LIKE ? 
               OR firstname LIKE ? 
               OR lastname LIKE ? 
               OR username LIKE ? 
               OR email LIKE ?";

    if ($stmt = mysqli_prepare($link, $sql)) {
        $param = "%" . $search . "%";
        mysqli_stmt_bind_param($stmt, "sssss", $param, $param, $param, $param, $param);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    }

    if ($countStmt = mysqli_prepare($link, $countSql)) {
        mysqli_stmt_bind_param($countStmt, "sssss", $param, $param, $param, $param, $param);
        mysqli_stmt_execute($countStmt);
        $countRes = mysqli_stmt_get_result($countStmt);
        $totalRows = mysqli_fetch_assoc($countRes)['total'];
    }
} else {
    // Non-search queries (with filters or no filter)
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background-image: url("https://beeswiki.com/wp-content/uploads/2023/03/Are-there-stingless-bees-1024x683.png");
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed;
            min-height: 100vh;
            color: #74512D;
            font-family: Arial, sans-serif;
            padding: 30px 50px;
        }
        .wrapper { width: 100%; margin: 0 auto; }
        h2 {
            font-family: 'Cursive', 'Brush Script MT', sans-serif;
            font-size: 4rem;
            margin-top: 10px;
            color: #FEDE16;
            text-shadow: 2px 2px 5px rgba(0,0,0,0.6);
        }
        .btn {
            padding: 0.6rem 1.2rem;
            font-weight: 700;
            background: #FFF2A3;
            color: #0B0806;
            border-radius: 0.5rem;
            border: 2px solid #74512D;
            transition: all 0.3s ease;
        }
        .btn:hover { background: #fae76a; color: #0B0806; box-shadow: 0px 4px 10px rgba(0,0,0,0.3); }
        .group { display: flex; align-items: center; position: relative; max-width: 220px; margin-right: 10px; }
        .input {
            width: 100%; height: 45px; padding-left: 2.5rem;
            border-radius: 12px; border: 1px solid #74512D;
            background-color: #E9E7D8; color: #0B0806;
        }
        .input:focus { border-color: #FEDE16; box-shadow: 0 0 5px #FEDE16; }
        .search-icon { position: absolute; left: 1rem; fill: #74512D; width: 1rem; height: 1rem; pointer-events: none; }
        .custom-table {
            width: 100%; margin: 20px auto; border-collapse: collapse;
            background: #E9E7D8; border-radius: 10px; overflow: hidden;
            box-shadow: 0px 4px 20px rgba(0,0,0,0.1); color: #0B0806;
        }
        .custom-table thead { background-color: #74512D; color: #fff; }
        .custom-table th, .custom-table td { padding: 0.9em 1em; border-bottom: 1px solid #E9E7D8; }
        .custom-table tbody tr:hover { background-color: #fae76a; transition: 0.3s ease; }
        .cta {
            padding: 8px 16px; background: #FFF2A3; color: #0B0806; font-weight: 700;
            border-radius: 25px; transition: all 0.3s ease; text-decoration: none;
        }
        .cta:hover { background: #74512D; color: #fff; box-shadow: 0px 4px 10px rgba(0,0,0,0.3); }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="mt-5 mb-3 d-flex align-items-center position-relative">
<a href="frontend/database.php" class="btn">
  <i class="bi bi-arrow-bar-left"></i> <span>Back</span>
</a>                    <h2 class="mx-auto text-center">Admin Account Records</h2>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <form method="get" class="d-flex align-items-center flex-wrap gap-2">
                        <div class="group">
                          <svg viewBox="0 0 24 24" aria-hidden="true" class="search-icon">
                            <path d="M21.53 20.47l-3.66-3.66C19.195 15.24 20 13.214 20 11
                            c0-4.97-4.03-9-9-9s-9 4.03-9 9
                            4.03 9 9 9c2.215 0 4.24-.804 
                            5.808-2.13l3.66 3.66c.295-.293.295-.767.002-1.06z"></path>
                          </svg>
                          <input
                            class="input"
                            type="search"
                            placeholder="Search..."
                            name="search"
                            value="<?php echo htmlspecialchars($search); ?>"
                          />
                        </div>
                            <button type="submit" class="btn">
                             <i class="bi bi-search"></i> <span>Search</span>
                            </button>                        
                            
<a href="adminindex.php" class="btn">
  <i class="bi bi-arrow-counterclockwise"></i> <span>Reset</span>
</a>
                        
<a href="admininfoCSV.php" class="btn">
  <i class="bi bi-file-earmark-arrow-down-fill"></i> <span>Get a Copy</span>
</a>                    </form>
                </div>

                <?php
                if ($result && mysqli_num_rows($result) > 0) {
                    echo '<table class="custom-table">';
                    echo "<thead><tr>
                            <th>ID</th>
                            <th>Firstname</th>
                            <th>Lastname</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Created At</th>
                            <th>Options</th>
                          </tr></thead><tbody>";
                    while ($row = mysqli_fetch_array($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['admin_id'] . "</td>";
                        echo "<td>" . $row['firstname'] . "</td>";
                        echo "<td>" . $row['lastname'] . "</td>";
                        echo "<td>" . $row['username'] . "</td>";
                        echo "<td>" . $row['email'] . "</td>";
                        echo "<td>" . $row['created_at'] . "</td>";
                        echo "<td><a href='readadmin.php?admin_id=" . $row['admin_id'] . "' class='cta'>
        <i class='bi bi-eye-fill'></i> View</a></td>";
echo "</tr>";
                    }
                    echo "</tbody></table>";
                } else {
                    echo '<div class="alert alert-danger"><em>No admins found.</em></div>';
                }

                // PAGINATION
                if ($totalPages > 1) {
                    echo '<nav class="mt-3"><ul class="pagination justify-content-center">';
                    for ($i = 1; $i <= $totalPages; $i++) {
                        $active = ($i == $page) ? "active" : "";
                        $url = "adminindex.php?page=$i&filter=" . urlencode($filter) . "&search=" . urlencode($search);
                        echo "<li class='page-item $active'><a class='page-link' href='$url'>$i</a></li>";
                    }
                    echo '</ul></nav>';
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
