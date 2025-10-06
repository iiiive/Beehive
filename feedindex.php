<?php
require_once "config.php";

// === Initialize variables ===
$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$filter = isset($_GET['filter']) ? $_GET['filter'] : "";
$page   = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$limit  = 10;
$offset = ($page - 1) * $limit;

// === Base setup ===
$whereClauses = [];
$params = [];
$types = "";

// === FILTER HANDLING ===
if (!empty($filter)) {
    if ($filter == "fed") {
        $whereClauses[] = "fed_at IS NOT NULL";
    } elseif ($filter == "notfed") {
        $whereClauses[] = "fed_at IS NULL";
    }
}

// === SEARCH HANDLING ===
if (!empty($search)) {
    $whereClauses[] = "(id LIKE ? OR user_id LIKE ?)";
    $params[] = "%" . $search . "%";
    $params[] = "%" . $search . "%";
    $types .= "ss";
}

// === Combine WHERE conditions ===
$whereSQL = "";
if (!empty($whereClauses)) {
    $whereSQL = "WHERE " . implode(" AND ", $whereClauses);
}

// === Sorting and Pagination ===
$orderSQL = ($filter == "recent") ? "ORDER BY created_at DESC" : "";
$limitSQL = "LIMIT ? OFFSET ?";
$typesForMain = $types . "ii";
$paramsForMain = array_merge($params, [$limit, $offset]);

// === Main query & Count query ===
$sql = "SELECT * FROM bee_feeding_schedule $whereSQL $orderSQL $limitSQL";
$countSql = "SELECT COUNT(*) AS total FROM bee_feeding_schedule $whereSQL";

// === Count query ===
if (!empty($search)) {
    $countStmt = mysqli_prepare($link, $countSql);
    mysqli_stmt_bind_param($countStmt, $types, ...$params);
    mysqli_stmt_execute($countStmt);
    $countRes = mysqli_stmt_get_result($countStmt);
    $totalRows = mysqli_fetch_assoc($countRes)['total'] ?? 0;
    mysqli_stmt_close($countStmt);
} else {
    $countRes = mysqli_query($link, $countSql);
    $totalRows = mysqli_fetch_assoc($countRes)['total'] ?? 0;
}

// === Fetch paginated data ===
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, $typesForMain, ...$paramsForMain);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// === Pagination Calculation ===
$totalPages = ($totalRows > 0) ? ceil($totalRows / $limit) : 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Bee Feeding Schedule</title>
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
.btn:hover {
  background: #fae76a;
  color: #0B0806;
  box-shadow: 0px 4px 10px rgba(0,0,0,0.3);
}
.group {
  display: flex;
  align-items: center;
  position: relative;
  max-width: 220px;
  margin-right: 10px;
}
.input {
  width: 100%;
  height: 45px;
  padding-left: 2.5rem;
  border-radius: 12px;
  border: 1px solid #74512D;
  background-color: #E9E7D8;
  color: #0B0806;
}
.input:focus { border-color: #FEDE16; box-shadow: 0 0 5px #FEDE16; }
.search-icon {
  position: absolute;
  left: 1rem;
  fill: #74512D;
  width: 1rem;
  height: 1rem;
  pointer-events: none;
}
.custom-table {
  width: 100%;
  margin: 20px auto;
  border-collapse: collapse;
  background: #E9E7D8;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0px 4px 20px rgba(0,0,0,0.1);
  color: #0B0806;
}
.custom-table thead { background-color: #74512D; color: #fff; }
.custom-table th, .custom-table td {
  padding: 0.9em 1em;
  border-bottom: 1px solid #E9E7D8;
}
.custom-table tbody tr:hover { background-color: #fae76a; transition: 0.3s ease; }
.cta {
  padding: 8px 16px;
  background: #FFF2A3;
  color: #0B0806;
  font-weight: 700;
  border-radius: 25px;
  transition: all 0.3s ease;
  text-decoration: none;
}
.cta:hover { background: #74512D; color: #fff; box-shadow: 0px 4px 10px rgba(0,0,0,0.3); }

/* PAGINATION DESIGN */
.pagination-container {
  display: block;
  overflow-x: auto;
  white-space: nowrap;
  background-color: rgba(255, 242, 163, 0.9);
  border-radius: 10px;
  padding: 8px;
  scrollbar-color: #74512D #E9E7D8;
  scrollbar-width: thin;
}
.pagination {
  display: inline-flex;
  flex-wrap: nowrap;
  justify-content: flex-start;
  min-width: max-content;
}
.pagination .page-item .page-link {
  color: #0B0806 !important;
  background-color: #FFF2A3 !important;
  border: 2px solid #74512D !important;
  font-weight: 600;
  border-radius: 8px;
  margin: 0 3px;
  transition: all 0.3s ease;
}
.pagination .page-item .page-link:hover {
  background-color: #fae76a !important;
  box-shadow: 0px 3px 6px rgba(0,0,0,0.2);
  color: #0B0806;
}
.pagination .page-item.active .page-link {
  background-color: #74512D !important;
  color: #fff !important;
  border-color: #74512D !important;
  box-shadow: 0px 3px 8px rgba(0,0,0,0.3);
}
.pagination .page-item.disabled .page-link {
  background-color: #E9E7D8;
  color: #999;
  border-color: #ccc;
}
.pagination-container::-webkit-scrollbar {
  height: 8px;
}
.pagination-container::-webkit-scrollbar-thumb {
  background-color: #74512D;
  border-radius: 5px;
}
.pagination-container::-webkit-scrollbar-track {
  background-color: #E9E7D8;
}
</style>
</head>

<body>
<div class="wrapper">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">

        <div class="mt-5 mb-3 d-flex align-items-center position-relative">
          <a href="frontend/database.php" class="btn"><i class="bi bi-arrow-bar-left"></i> Back</a>
          <h2 class="mx-auto text-center">Bee Feeding Schedule</h2>
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
              <input class="input" type="search" placeholder="Search..." name="search" value="<?php echo htmlspecialchars($search); ?>"/>
            </div>
            <button type="submit" class="btn"><i class="bi bi-search"></i> Search</button>
            <a href="<?php echo basename($_SERVER['PHP_SELF']); ?>" class="btn"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
            <a href="FeedingCSV.php" class="btn"><i class="bi bi-file-earmark-arrow-down-fill"></i> Get a Copy</a>
          </form>
        </div>

<?php
if ($result && mysqli_num_rows($result) > 0) {
    echo '<table class="custom-table">';
    echo "<thead><tr>
    <th>ID</th>
    <th>User ID</th>
    <th>Interval (min)</th>
    <th>Next Feed</th>
    <th>Last Fed</th>
    <th>Fed By User</th>
    <th>Fed At</th>
    <th>Options</th>
    </tr></thead><tbody>";

    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['user_id']}</td>";
        echo "<td>{$row['interval_minutes']}</td>";
        echo "<td>{$row['next_feed']}</td>";
        echo "<td>{$row['last_fed']}</td>";
        echo "<td>{$row['fed_by_user_id']}</td>";
        echo "<td>{$row['fed_at']}</td>";

        $btnText = $row['fed_at'] ? "Already Fed" : "Mark as Fed";
        $btnClass = $row['fed_at'] ? "btn disabled" : "cta";
        echo "<td><a href='markfed.php?id={$row['id']}' class='$btnClass'>$btnText</a></td>";
        echo "</tr>";
    }
    echo "</tbody></table>";
} else {
    echo '<div class="alert alert-danger"><em>No feeding records found.</em></div>';
}

// === Pagination ===
if ($totalPages > 1) {
    echo '<div class="pagination-container mt-3"><ul class="pagination mb-0 justify-content-center">';
    for ($i = 1; $i <= $totalPages; $i++) {
        $active = ($i == $page) ? "active" : "";
        $url = basename($_SERVER['PHP_SELF'])."?page=$i&filter=".urlencode($filter)."&search=".urlencode($search);
        echo "<li class='page-item $active'><a class='page-link' href='$url'>$i</a></li>";
    }
    echo '</ul></div>';
}

mysqli_stmt_close($stmt);
mysqli_close($link);
?>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
