<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Beehive Monitoring Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <style>
    body {
      background-image: url("https://beeswiki.com/wp-content/uploads/2023/03/Are-there-stingless-bees-1024x683.png");
      background-repeat: no-repeat;
      background-size: cover;
      background-attachment: fixed;
      display: flex;
      min-height: 100vh;
      color: #74512D;
      font-family: Arial, sans-serif;
    }
    .wrapper { width: 95%; margin: auto; }
    h2 {
      font-family: 'Cursive', 'Brush Script MT', sans-serif;
      font-size: 4rem; margin-bottom: 40px; 
      color: #FEDE16;
      text-shadow: 2px 2px 5px rgba(0,0,0,0.6);
    }
    .btn {
      padding: 0.6rem 1.2rem; font-weight: 700; background: #FFF2A3;
      color: #0B0806; border-radius: 0.5rem; border: 2px solid #74512D;
      transition: all 0.3s ease;
    }
    .btn:hover { background: #fae76a; box-shadow: 0px 4px 10px rgba(0,0,0,0.3); }
    .group { display: flex; align-items: center; position: relative; max-width: 220px; margin-right: 10px; }
    .input {
      width: 100%; height: 45px; padding-left: 2.5rem; border-radius: 12px;
      border: 1px solid #74512D; background-color: #E9E7D8; color: #0B0806;
    }
    .search-icon { position: absolute; left: 1rem; fill: #74512D; width: 1rem; height: 1rem; }
    .custom-table { width: 100%; margin: 20px auto; border-collapse: collapse;
      background: #E9E7D8; border-radius: 10px; overflow: hidden;
      box-shadow: 0px 4px 20px rgba(0,0,0,0.1); color: #0B0806;
    }
    .custom-table thead { background-color: #74512D; color: #fff; }
    .custom-table th, .custom-table td { padding: 0.9em 1em; border-bottom: 1px solid #E9E7D8; }
    .custom-table tbody tr:hover { background-color: #fae76a; transition: 0.3s ease; }
    /* Animated View Button */
    .cta {
        position: relative;
        margin: auto;
        padding: 8px 16px;
        border: none;
        background: #FFF2A3; /* vanilla */
        color: #0B0806; /* smoky black */
        font-weight: 700;
        cursor: pointer;
        border-radius: 25px;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .cta:hover {
        background: #74512D; /* yellow hover */
        box-shadow: 0px 4px 10px rgba(0,0,0,0.3);
    }

    .cta svg {
        fill: #74512D; /* coffee icon */
    }
  /* Pagination styling */
  .pagination .page-item .page-link {
    color: #0B0806;
    background-color: #FFF2A3;
    border: 2px solid #74512D;
    font-weight: 600;
    border-radius: 8px;
    margin: 0 3px;
    transition: all 0.3s ease;
  }

  .pagination .page-item .page-link:hover {
    background-color: #fae76a;
    box-shadow: 0px 3px 6px rgba(0,0,0,0.2);
    color: #0B0806;
  }

  .pagination .page-item.active .page-link {
    background-color: #74512D;
    color: #fff;
    border-color: #74512D;
    box-shadow: 0px 3px 8px rgba(0,0,0,0.3);
  }

  .pagination .page-item.disabled .page-link {
    background-color: #E9E7D8;
    color: #999;
    border-color: #ccc;
  }

  /* Make forms and filters wrap nicely */
.d-flex.flex-wrap.gap-2 {
  gap: 10px;
}

/* Responsive tweaks */
@media (max-width: 992px) {
  h2 {
    font-size: 2.5rem;
    text-align: center;
  }
  .btn {
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
  }
}

@media (max-width: 768px) {
  .group {
    max-width: 100%;
    margin-bottom: 10px;
  }
  .input {
    width: 100%;
  }
  .custom-table th, .custom-table td {
    font-size: 0.85rem;
    padding: 0.6em;
  }
}

@media (max-width: 576px) {
  .wrapper {
    width: 100%;
    padding: 10px;
  }
  h2 {
    font-size: 2rem;
  }
  .btn, .dropdown-toggle {
    font-size: 0.8rem;
    padding: 0.4rem 0.8rem;
  }
  .custom-table th, .custom-table td {
    font-size: 0.75rem;
  }
}
/* General button responsiveness */
.btn i, .cta i {
  font-size: 1.2rem;
}

/* On small screens, hide button text and make buttons circular */
@media (max-width: 576px) {
  .btn span, 
  .btn:not(.dropdown-toggle)::after, 
  .cta span {
    display: none !important; /* hide text labels */
  }

  .btn, .cta {
    padding: 8px;
    border-radius: 50%;
    width: 42px;
    height: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }
}
/* Scrollable Pagination Container */
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

/* Keep pagination buttons inline */
.pagination {
  display: inline-flex;
  flex-wrap: nowrap;
  justify-content: flex-start;
  min-width: max-content;
}

/* Style scrollbars (for Chrome, Edge) */
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

  </style>
</head>
<body>
  <div class="wrapper">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">

          <div class="mt-5 mb-3 clearfix d-flex justify-content-between align-items-center">
           <a href="frontend/database.php" class="btn">
  <i class="bi bi-arrow-bar-left"></i> <span>Back</span>
</a>

            <h2>Beehive Monitoring Records</h2>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <form method="get" class="d-flex align-items-center flex-wrap gap-2">
              <div class="group">
                <svg viewBox="0 0 24 24" class="search-icon">
                  <path d="M21.53 20.47l-3.66-3.66C19.195 15.24 20 13.214 20 11c0-4.97-4.03-9-9-9s-9 
                    4.03-9 9 4.03 9 9 9c2.215 0 4.24-.804 5.808-2.13l3.66 3.66c.147.146.34.22.53.22
                    s.385-.073.53-.22c.295-.293.295-.767.002-1.06zM3.5 11c0-4.135 3.365-7.5 
                    7.5-7.5s7.5 3.365 7.5 7.5-3.365 7.5-7.5 7.5-7.5-3.365-7.5-7.5z"></path>
                </svg>
                <input id="query" class="input" type="search" placeholder="Search..." name="search"/>
              </div>
             <button type="submit" class="btn">
  <i class="bi bi-search"></i> <span>Search</span>
</button>

<a href="index.php" class="btn">
  <i class="bi bi-arrow-counterclockwise"></i> <span>Reset</span>
</a>

              <!-- Filter dropdown -->
              <div class="dropdown">
                <button class="btn dropdown-toggle" type="button" id="filterDropdown"
        data-bs-toggle="dropdown" aria-expanded="false">
  <i class="bi bi-funnel"></i> <span>Filters</span>
</button>

                <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                  <li><a class="dropdown-item" href="?filter=statusGood">Status: Good</a></li>
                  <li><a class="dropdown-item" href="?filter=statusBad">Status: Bad</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="?filter=highTemp">High Temperature</a></li>
                  <li><a class="dropdown-item" href="?filter=normalTemp">Normal Temperature</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="?filter=lowHumidity">Low Humidity</a></li>
                  <li><a class="dropdown-item" href="?filter=normalHumidity">Normal Humidity</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="?filter=HighWeight">Heavy Weight</a></li>
                  <li><a class="dropdown-item" href="?filter=LowWeight">Light Weight</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="?filter=fanOn">Fan Status: ON</a></li>
                  <li><a class="dropdown-item" href="?filter=fanOff">Fan Status: OFF</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="?filter=orderAsc">Order: Oldest First</a></li>
                  <li><a class="dropdown-item" href="?filter=orderDesc">Order: Latest First</a></li>
                </ul>
              </div>
              <a href="BeehiveReadingsCSV.php" class="btn">
  <i class="bi bi-file-earmark-arrow-down-fill"></i> <span>Get a Copy</span>
</a>

            </form>
          </div>
<div class = "table-responsive">
          <table class="custom-table">
            <thead>
              <tr>
                <th>Reading ID</th>
                <th>Timestamp</th>
                <th>Temperature (°C)</th>
                <th>Humidity (%)</th>
                <th>Weight (kg)</th>
                <th>Fan Status</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="table-body"></tbody>
          </table>
          
 <!-- Pagination -->
         <!-- Scrollable Pagination -->
<div class="pagination-container mt-3">
  <ul id="pagination" class="pagination mb-0"></ul>
</div>


          </div>

          

        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    async function reloadTable(page = 1) {
  try {
    currentPage = page; // save current page

    const params = new URLSearchParams(window.location.search);
    params.set("page", page); // ensure page param exists
    const res = await fetch("beehivetable.php?" + params.toString());
    const json = await res.json();

    const tbody = document.getElementById("table-body");
    tbody.innerHTML = "";

    if (json.data.length === 0) {
      tbody.innerHTML = `<tr><td colspan="8" class="text-center">No records found</td></tr>`;
    } else {
      json.data.forEach(row => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>${row.reading_id}</td>
          <td>${row.timestamp}</td>
          <td>${row.temperature}</td>
          <td>${row.humidity}</td>
          <td>${row.weight}</td>
          <td>${row.fan_status == 1 ? "ON" : "OFF"}</td>
          <td>${row.status}</td>
          <td><a href="read.php?reading_id=${row.reading_id}" class="cta">
  <i class="bi bi-eye-fill"></i> <span>View</span>
</a>

        </td>
        `;
        tbody.appendChild(tr);
      });
    }

    // Build pagination
    const pagination = document.getElementById("pagination");
    pagination.innerHTML = "";
    const current = json.current_page;
    const total = json.total_pages;

    if (total > 1) {
      // Prev
      const prevLi = document.createElement("li");
      prevLi.className = "page-item" + (current === 1 ? " disabled" : "");
      prevLi.innerHTML = `<a class="page-link" href="#">Previous</a>`;
      prevLi.onclick = (e) => { e.preventDefault(); if (current > 1) reloadTable(current - 1); };
      pagination.appendChild(prevLi);

      // Page numbers
      for (let i = 1; i <= total; i++) {
        const li = document.createElement("li");
        li.className = "page-item" + (i === current ? " active" : "");
        li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
        li.onclick = (e) => { e.preventDefault(); reloadTable(i); };
        pagination.appendChild(li);
      }

      // Next
      const nextLi = document.createElement("li");
      nextLi.className = "page-item" + (current === total ? " disabled" : "");
      nextLi.innerHTML = `<a class="page-link" href="#">Next</a>`;
      nextLi.onclick = (e) => { e.preventDefault(); if (current < total) reloadTable(current + 1); };
      pagination.appendChild(nextLi);
    }

  } catch (err) {
    console.error("Table fetch error:", err);
  }
}


reloadTable(); // initial load

// Reload every 5 seconds on the same page
setInterval(() => {
  reloadTable(currentPage);
}, 5000);
  </script>
</body>
</html>
