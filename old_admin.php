<!-- index.php -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet">
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
        rel="stylesheet">
    <style>
        #sidebar {
            min-width: 250px;
            max-width: 250px;
        }

        #wrapper {
            height: 100vh;
            overflow: hidden;
        }

        #page-content {
            overflow-y: auto;
        }

        #sidebar .nav-link {
            transition: .3s;
        }

        #sidebar .nav-link.active,
        #sidebar .nav-link:hover {
            background-color: #0d6efd !important;
            color: #fff !important;
        }
    </style>
</head>

<body>

    <?php
    require_once "connection.php";

    // run a SELECT * on each table then read ->num_rows
    $res1 = $conn->query("SELECT * FROM resident");
    $count_residents = $res1->num_rows;

    $res2 = $conn->query("SELECT * FROM household");
    $count_households = $res2->num_rows;

    $res3 = $conn->query("SELECT * FROM barangay_official");
    $count_officials = $res3->num_rows;

    $res4 = $conn->query("SELECT * FROM incident_report");
    $count_incidents = $res4->num_rows;

    $res5 = $conn->query("SELECT * FROM infrastructure_projects");
    $count_projects = $res5->num_rows;

    $res6 = $conn->query("SELECT * FROM users");
    $count_users = $res1->num_rows;

    $res7 = $conn->query("SELECT * FROM logs");
    $count_logs = $res2->num_rows;
    ?>

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
                <li class="nav-item">
                    <a href="admin.php" class="nav-link text-white">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="resident.php" class="nav-link text-white">
                        <i class="bi bi-people me-2"></i> Residents
                    </a>
                </li>
                <li class="nav-item">
                    <a href="household.php" class="nav-link text-white">
                        <i class="bi bi-house me-2"></i> Households
                    </a>
                </li>
                <li class="nav-item">
                    <a href="officials.php" class="nav-link text-white">
                        <i class="bi bi-person-badge me-2"></i> Officials
                    </a>
                </li>
                <li class="nav-item">
                    <a href="incidents.php" class="nav-link text-white">
                        <i class="bi bi-exclamation-triangle me-2"></i> Incidents
                    </a>
                </li>
                <li class="nav-item">
                    <a href="projects.php" class="nav-link text-white">
                        <i class="bi bi-building me-2"></i> Projects
                    </a>
                </li>
                <li class="nav-item">
                    <a href="users.php" class="nav-link text-white">
                        <i class="bi bi-person me-2"></i> Users
                    </a>
                </li>
                <li class="nav-item">
                    <a href="logs.php" class="nav-link text-white">
                        <i class="bi bi-journal-text me-2"></i> Logs
                    </a>
                </li>

            </ul>
            <div class="mt-auto">
                <button class="btn btn-primary w-100">
                    <a href="logout.php" class="btn btn-primary w-100">
                        <i class="bi bi-box-arrow-right me-1"></i> LOGOUT
                    </a>
                </button>



            </div>
        </nav>

        <div class="flex-grow-1 bg-light" id="page-content">
            <div class="container py-5">
                <h1 class="mb-1">Barangay Camaya</h1>
                <p class="text-secondary mb-4">
                    Welcome, Admin! Use the cards below to jump straight to each section.
                </p>

                <div class="row g-4">
                    <div class="col-sm-6 col-lg-4">
                        <a href="resident.php" class="text-decoration-none">
                            <div class="card shadow-sm">
                                <div class="card-body d-flex align-items-center">
                                    <i class="bi bi-people-fill fs-1 text-primary me-3"></i>
                                    <div>
                                        <h5 class="card-title mb-0">Residents</h5>
                                        <p class="fs-4 mb-0"><?= $count_residents ?></p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-sm-6 col-lg-4">
                        <a href="household.php" class="text-decoration-none">
                            <div class="card shadow-sm">
                                <div class="card-body d-flex align-items-center">
                                    <i class="bi bi-house-fill fs-1 text-success me-3"></i>
                                    <div>
                                        <h5 class="card-title mb-0">Households</h5>
                                        <p class="fs-4 mb-0"><?= $count_households ?></p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-sm-6 col-lg-4">
                        <a href="officials.php" class="text-decoration-none">
                            <div class="card shadow-sm">
                                <div class="card-body d-flex align-items-center">
                                    <i class="bi bi-person-badge-fill fs-1 text-warning me-3"></i>
                                    <div>
                                        <h5 class="card-title mb-0">Officials</h5>
                                        <p class="fs-4 mb-0"><?= $count_officials ?></p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-sm-6 col-lg-4">
                        <a href="incidents.php" class="text-decoration-none">
                            <div class="card shadow-sm">
                                <div class="card-body d-flex align-items-center">
                                    <i class="bi bi-exclamation-triangle-fill fs-1 text-danger me-3"></i>
                                    <div>
                                        <h5 class="card-title mb-0">Incidents</h5>
                                        <p class="fs-4 mb-0"><?= $count_incidents ?></p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-sm-6 col-lg-4">
                        <a href="projects.php" class="text-decoration-none">
                            <div class="card shadow-sm">
                                <div class="card-body d-flex align-items-center">
                                    <i class="bi bi-building-fill fs-1 text-info me-3"></i>
                                    <div>
                                        <h5 class="card-title mb-0">Projects</h5>
                                        <p class="fs-4 mb-0"><?= $count_projects ?></p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-sm-6 col-lg-4">
                        <a href="users.php" class="text-decoration-none">
                            <div class="card shadow-sm">
                                <div class="card-body d-flex align-items-center">
                                    <i class="bi bi-person-fill fs-1 text-info me-3"></i>
                                    <div>
                                        <h5 class="card-title mb-0">Users</h5>
                                        <p class="fs-4 mb-0"><?= $count_users ?></p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-sm-6 col-lg-4">
                        <a href="logs.php" class="text-decoration-none">
                            <div class="card shadow-sm">
                                <div class="card-body d-flex align-items-center">
                                    <i class="bi bi-journal-text fs-1 text-info me-3"></i>
                                    <div>
                                        <h5 class="card-title mb-0">Logs</h5>
                                        <p class="fs-4 mb-0"><?= $count_logs ?></p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
    </script>
    <script>
        document.getElementById('btn-toggle')
            .addEventListener('click', () =>
                document.getElementById('sidebar').classList.toggle('d-none')
            );

        document.querySelectorAll('#sidebar .nav-link').forEach(link => {
            if (link.href === window.location.href) link.classList.add('active');
        });
    </script>
</body>

</html>