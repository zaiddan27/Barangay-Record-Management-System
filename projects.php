<!-- projects.php -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Projects</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="mystyle.css">
</head>

<body>

    <!-- Mobile top navbar -->
    <nav class="navbar navbar-dark bg-dark d-md-none">
        <div class="container-fluid">
            <button class="btn btn-dark" id="btn-toggle">
                <i class="bi bi-list fs-3"></i>
            </button>
            <span class="navbar-brand mb-0 ms-2">LOGO</span>
        </div>
    </nav>


    <div class="d-flex" id="wrapper">
        <nav id="sidebar" class="bg-dark text-white d-flex flex-column p-3">
            <div class="d-flex align-items-center mb-4">
                <span class="fs-4">LOGO</span>
            </div>

            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item"><a href="admin.php" class="nav-link text-white"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                <li class="nav-item"><a href="resident.php" class="nav-link text-white"><i class="bi bi-people me-2"></i> Residents</a></li>
                <li class="nav-item"><a href="household.php" class="nav-link text-white"><i class="bi bi-house me-2"></i> Households</a></li>
                <li class="nav-item"><a href="officials.php" class="nav-link text-white"><i class="bi bi-person-badge me-2"></i> Officials</a></li>
                <li class="nav-item"><a href="incidents.php" class="nav-link text-white"><i class="bi bi-exclamation-triangle me-2"></i> Incidents</a></li>
                <li class="nav-item"><a href="projects.php" class="nav-link text-white"><i class="bi bi-building me-2"></i> Projects</a></li>
            </ul>
            <div class="mt-auto">
                <button class="btn btn-primary w-100"><i class="bi bi-box-arrow-right me-1"></i> LOGOUT</button>
            </div>
        </nav>

        <!-- --------PAGE CONTENT -------------------->
        <div class="flex-grow-1 bg-light" id="page-content">
            <div class="container p-5">

                <!-- ----------- FORM ----------------- -->
                <form action="projects.php" method="post">
                    <div class="row g-3 d-flex mb-4">
                        <div class="col-auto">
                            <h1 class="h3 page-title">Projects</h1>
                        </div>
                        <div class="col-auto">
                            <input
                                type="search"
                                name="searchInput01"
                                placeholder="Search Projects"
                                class="form-control">
                        </div>
                        <div class="col-auto">
                            <input
                                type="submit"
                                name="btnsearch01"
                                value="Search"
                                class="btn btn-primary">
                        </div>
                    </div>
                </form>

                <?php
                require_once "connection.php";

                // search functionality
                if (isset($_POST['btnsearch01'])) {
                    $searchInput01 = $_POST['searchInput01'];
                    // professor‑style concatenation, no escaping
                    $selectsql = "SELECT * FROM infrastructure_projects WHERE "
                        . "project_id    LIKE '%" . $searchInput01 . "%' OR "
                        . "project_name  LIKE '%" . $searchInput01 . "%' OR "
                        . "project_description LIKE '%" . $searchInput01 . "%' OR "
                        . "start_date    LIKE '%" . $searchInput01 . "%' OR "
                        . "end_date      LIKE '%" . $searchInput01 . "%' OR "
                        . "project_status LIKE '%" . $searchInput01 . "%'";
                } else {
                    // if search btn is not clicked, display all
                    $selectsql = "SELECT * FROM infrastructure_projects";
                }

                $result = $conn->query($selectsql);
                ?>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="projectTable">
                        <!-- HEAD OF TABLE -->
                        <thead class="table-dark">
                            <tr>
                                <th>Project ID</th>
                                <th>Project Name</th>
                                <th>Project Description</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Project Status</th>
                            </tr>
                        </thead>

                        <!-- BODY TABLE -->
                        <tbody>
                            <?php
                            // for each to use foreach & show no‑records row
                            if ($result && $result->num_rows > 0) {
                                foreach ($result as $field) {
                                    echo '<tr>';
                                    echo '<td>' . $field['project_id']          . '</td>';
                                    echo '<td>' . $field['project_name']        . '</td>';
                                    echo '<td>' . $field['project_description'] . '</td>';
                                    echo '<td>' . $field['start_date']          . '</td>';
                                    echo '<td>' . $field['end_date']            . '</td>';
                                    echo '<td>' . $field['project_status']      . '</td>';
                                    echo '</tr>';
                                }
                            } else {
                                // Single row spanning all 6 columns
                                echo '<tr><td colspan="6" class="text-center py-4">No records found</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- for nav toggle -->
    <script>
        document.getElementById('btn-toggle').addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('d-none');
        });
        document.querySelectorAll('#sidebar .nav-link').forEach(link => {
            if (link.href === window.location.href) link.classList.add('active');
        });
    </script>

    <!-- <<< live‑search script here >>> -->
    <!--
  <script>
    const searchBox   = document.querySelector('input[name="searchInput01"]');
    const projectRows = document.querySelectorAll('#projectTable tbody tr');
    searchBox.addEventListener('input', () => {
      const q = searchBox.value.trim().toLowerCase();
      projectRows.forEach(row => {
        row.style.display = (!q || row.textContent.toLowerCase().includes(q))
                           ? '' : 'none';
      });
    });
  </script>
  -->

</body>

</html>