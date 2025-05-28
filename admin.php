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
    <link rel="stylesheet" href="mystyle.css">
    <style>
        body {
            position: relative;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            background: linear-gradient(rgba(255, 255, 255, 0.7), rgba(255, 255, 255, 0.7)), url('images/portalbgp.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        /* Retain sidebar background */
        #sidebar {
            min-width: 250px;
            max-width: 250px;
            background: rgba(9, 41, 34, 0.95) !important;
        }

        /* Portal theme colors */
        .navbar-custom {
            background-color: #092922 !important;
            padding: 1.5rem 0;
        }

        .navbar-brand,
        .navbar-nav .nav-link {
            color: #fff !important;
            font-weight: 500;
            letter-spacing: 1px;
        }

        #sidebar .nav-link.active,
        #sidebar .nav-link:hover {
            background-color: #1abc9c !important;
            color: #fff !important;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .card:hover {
            transform: scale(1.03);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        .btn-primary,
        .btn-primary:focus,
        .btn-primary:active {
            background-color: #1abc9c !important;
            border: none !important;
        }

        .footer {
            background-color: #092922;
            width: 100%;
            text-align: center;
            color: #fff;
            font-size: 0.9rem;
            position: fixed;
            left: 0;
            bottom: 0;
            padding: 1rem 0;
            margin: 0;
            z-index: 1030;
        }
    </style>
</head>


<?php
session_start();
require_once "connection.php";

if (empty($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'employee')) {
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'employee') {
        header("Location: employee_dashboard.php");
        exit();
    } else {
        header("Location: login.php");
        exit();
    }
}

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

$res7 = $conn->query("SELECT * FROM lags");
$count_lags = $res2->num_rows;
?>


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
                <span class="fs-4">CAMAYA</span>
            </div>

            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="<?= ($_SESSION['role'] === 'admin') ? 'admin.php' : 'employee_dashboard.php'; ?>" class="nav-link text-white">
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
                    <a href="lags.php" class="nav-link text-white">
                        <i class="bi bi-journal-text me-2"></i> Logs
                    </a>
                </li>

            </ul>
            <div class="mt-auto">
                <a href="logout.php" class="btn btn-primary w-100">
                    <i class="bi bi-box-arrow-right me-1"></i> LOGOUT
                </a>
            </div>
        </nav>

        <div class="flex-grow-1" id="page-content">
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
                        <a href="lags.php" class="text-decoration-none">
                            <div class="card shadow-sm">
                                <div class="card-body d-flex align-items-center">
                                    <i class="bi bi-journal-text fs-1 text-info me-3"></i>
                                    <div>
                                        <h5 class="card-title mb-0">Lags</h5>
                                        <p class="fs-4 mb-0"><?= $count_lags ?></p>
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