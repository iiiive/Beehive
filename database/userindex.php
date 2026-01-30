<?php
require_once "config.php";

$search = "";
$filter = isset($_GET['filter']) ? $_GET['filter'] : "";

// Pagination setup
$limit = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Base SQL
$where = "";
$order = " ORDER BY created_at DESC";

// Build filters
if (!empty($filter)) {
    if ($filter == "active") $where = " WHERE status = 'active'";
    elseif ($filter == "inactive") $where = " WHERE status = 'inactive'";
    elseif ($filter == "pending") $where = " WHERE status = 'pending'";
}

// Build search
$params = [];
$types = "";
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = trim($_GET['search']);
    $where = " WHERE user_id LIKE ? OR firstname LIKE ? OR lastname LIKE ? OR username LIKE ? OR email LIKE ? OR contact_number LIKE ?";
    $params = array_fill(0, 6, "%" . $search . "%");
    $types = "ssssss";
}

// Count total rows
$count_sql = "SELECT COUNT(*) as total FROM users $where";
if (!empty($params)) {
    $stmt = mysqli_prepare($link, $count_sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $count_res = mysqli_stmt_get_result($stmt);
    $total_rows = mysqli_fetch_assoc($count_res)['total'];
    mysqli_stmt_close($stmt);
} else {
    $count_res = mysqli_query($link, $count_sql);
    $total_rows = mysqli_fetch_assoc($count_res)['total'];
}

// Get data with pagination
$sql = "SELECT * FROM users $where $order LIMIT ? OFFSET ?";
if (!empty($params)) {
    $stmt = mysqli_prepare($link, $sql);
    $params[] = $limit;
    $params[] = $offset;
    $types .= "ii";
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
}

$total_pages = ceil($total_rows / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Management Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
body {
  background-image: url("https://beeswiki.com/wp-content/uploads/2023/03/Are-there-stingless-bees-1024x683.png");
  background-repeat: no-repeat;
  background-size: cover;
  background-attachment: fixed;
  min-height: 100vh;
  color: #74512D;
  font-family: Arial, sans-serif;
  padding: 20px 30px;
}
.wrapper { width: 100%; margin: auto; }
h2 {
  font-family: 'Cursive', 'Brush Script MT', sans-serif;
  font-size: clamp(2rem, 5vw, 4rem);
  margin: 20px 0;
  color: #FEDE16;
  text-shadow: 2px 2px 5px rgba(0,0,0,0.6);
  text-align: center;
}
.btn, .cta {
  padding: 0.6rem 1.2rem;
  font-weight: 700;
  background: #FFF2A3;
  color: #0B0806;
  border-radius: 0.5rem;
  border: 2px solid #74512D;
  transition: all 0.3s ease;
  text-decoration: none;
}
.btn:hover, .cta:hover { background: #74512D; color: #fff; box-shadow: 0px 4px 10px rgba(0,0,0,0.3); }

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
.search-icon { position: absolute; left: 1rem; fill: #74512D; width: 1rem; height: 1rem; pointer-events: none; }

.custom-table {
  width: 100%;
  margin: 20px 0;
  border-collapse: collapse;
  background: #E9E7D8;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0px 4px 20px rgba(0,0,0,0.1);
  color: #0B0806;
}
.custom-table thead { background-color: #74512D; color: #fff; }
.custom-table th, .custom-table td { padding: 0.8em 1em; border-bottom: 1px solid #E9E7D8; }
.custom-table tbody tr:hover { background-color: #fae76a; transition: 0.3s ease; }
.table-scroll {
  overflow-x: auto;
  width: 100%;
}

/* Optional: smoother mobile scrolling */
.table-scroll::-webkit-scrollbar {
  height: 8px;
}

.table-scroll::-webkit-scrollbar-thumb {
  background-color: #74512D;
  border-radius: 10px;
}

.pagination-container {
  display: block;
  overflow-x: auto;
  white-space: nowrap;
  background-color: rgba(255, 242, 163, 0.9);
  border-radius: 10px;
  padding: 8px;
}
.pagination { display: inline-flex; justify-content: flex-start; min-width: max-content; }
.pagination .page-item .page-link {
  color: #0B0806 !important;
  background-color: #FFF2A3 !important;
  border: 2px solid #74512D !important;
  font-weight: 600;
  border-radius: 8px;
  margin: 0 3px;
  transition: all 0.3s ease;
}
.pagination .page-item.active .page-link { background-color: #74512D !important; color: #fff !important; }

@media (max-width: 768px) {
  .group { max-width: 100%; margin-bottom: 10px; }
  .input { width: 100%; }
  .custom-table th, .custom-table td { font-size: 0.85rem; padding: 0.6em; }
}
@media (max-width: 576px) {
  h2 { font-size: 2rem; }
  .btn, .cta { padding: 0.4rem 0.8rem; font-size: 0.8rem; }
  .custom-table th, .custom-table td { font-size: 0.75rem; }
}

</style>
</head>
<body>
<div class="wrapper">
<div class="container-fluid">
<div class="row">
<div class="col-12">

<div class="d-flex justify-content-between align-items-center mt-3 mb-3 flex-wrap">
  <a href="../admin/database.php" class="btn"><i class="bi bi-arrow-bar-left"></i> Back</a>
  <h2>User Account Records</h2>
</div>

<div class="d-flex flex-wrap gap-2 mb-3 align-items-center">
  <form method="get" class="d-flex flex-wrap gap-2 align-items-center">
    <div class="group">
      <svg viewBox="0 0 24 24" class="search-icon">
        <path d="M21.53 20.47l-3.66-3.66C19.195 15.24 20 13.214 20 11c0-4.97-4.03-9-9-9s-9 
        4.03-9 9 4.03 9 9 9c2.215 0 4.24-.804 5.808-2.13l3.66 3.66c.295-.293.295-.767.002-1.06z"></path>
      </svg>
      <input class="input" type="search" placeholder="Search..." name="search" value="<?php echo htmlspecialchars($search); ?>" />
    </div>
    <button type="submit" class="btn"><i class="bi bi-search"></i> Search</button>
    <a href="userindex.php" class="btn"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>

    <div class="dropdown">
      <button class="btn dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-funnel"></i> Filters
      </button>
      <ul class="dropdown-menu" aria-labelledby="filterDropdown">
        <li><a class="dropdown-item" href="?filter=active">Status: Active</a></li>
        <li><a class="dropdown-item" href="?filter=inactive">Status: Inactive</a></li>
        <li><a class="dropdown-item" href="?filter=pending">Status: Pending</a></li>
      </ul>
    </div>

    <a href="userinfoCSV.php" class="btn"><i class="bi bi-file-earmark-arrow-down-fill"></i> Get a Copy</a>
  </form>
</div>
<!-- TABLE ONLY (SCROLLS) -->
<div class="table-scroll">
  <table class="custom-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Username</th>
        <th>Email</th>
        <th>Status</th>
        <th>Address</th>
        <th>Birthday</th>
        <th>Contact</th>
        <th>Created At</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if ($result && mysqli_num_rows($result) > 0) {
          while ($row = mysqli_fetch_assoc($result)) {
              echo "<tr>";
              echo "<td>{$row['user_id']}</td>";
              echo "<td>{$row['firstname']}</td>";
              echo "<td>{$row['lastname']}</td>";
              echo "<td>{$row['username']}</td>";
              echo "<td>{$row['email']}</td>";
              echo "<td>{$row['status']}</td>";
              echo "<td>{$row['address']}</td>";
              echo "<td>{$row['birthday']}</td>";
              echo "<td>{$row['contact_number']}</td>";
              echo "<td>{$row['created_at']}</td>";
              echo "<td><a href='readuser.php?user_id={$row['user_id']}' class='cta'><i class='bi bi-eye-fill'></i></a></td>";
              echo "</tr>";
          }
      } else {
          echo "<tr><td colspan='11' class='text-center'>No records found</td></tr>";
      }
      ?>
    </tbody>
  </table>
</div>

<!-- PAGINATION (DOES NOT SCROLL) -->
<div class="pagination-container mt-3">
  <ul class="pagination justify-content-center mb-0">
    <?php if ($page > 1): ?>
      <li class="page-item">
        <a class="page-link" href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>&filter=<?php echo urlencode($filter); ?>">
          Previous
        </a>
      </li>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
      <li class="page-item <?php if($i==$page) echo 'active'; ?>">
        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&filter=<?php echo urlencode($filter); ?>">
          <?php echo $i; ?>
        </a>
      </li>
    <?php endfor; ?>

    <?php if ($page < $total_pages): ?>
      <li class="page-item">
        <a class="page-link" href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>&filter=<?php echo urlencode($filter); ?>">
          Next
        </a>
      </li>
    <?php endif; ?>
  </ul>
</div>


</div></div></div></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
