<?php
session_start();
if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'resident') {
    header('Location: login.php');
    exit;
}
require_once "connection.php";

$residentName   = $_SESSION['fullname'];

// fetch all projects, newest first
$projRs = $conn->query("
    SELECT project_name, project_description, start_date, end_date, project_status
      FROM infrastructure_projects
     ORDER BY start_date DESC
");

function getRandomPastelColor()
{
    $r = rand(200, 255);
    $g = rand(200, 255);
    $b = rand(200, 255);
    return "rgb($r, $g, $b)";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>View Projects</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
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

        #sidebar {
            min-width: 250px;
            max-width: 250px;
            background: rgba(9, 41, 34, 0.95) !important;
        }

        .btn-primary,
        .btn-primary:focus,
        .btn-primary:active {
            background-color: #1abc9c !important;
            border: none !important;
        }

        .btn-secondary,
        .btn-secondary:focus,
        .btn-secondary:active {
            background-color: #092922 !important;
            border: none !important;
            color: #fff !important;
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
            transform: scale(1.02);
        }

        #page-content {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body>

    <!-- Mobile nav -->
    <nav class="navbar navbar-dark bg-dark d-md-none">
        <div class="container-fluid">
            <button class="btn btn-dark" id="btn-toggle">
                <i class="bi bi-list fs-3"></i>
            </button>
            <span class="navbar-brand mb-0 ms-2">Barangay Camaya</span>
        </div>
    </nav>

    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="bg-dark text-white d-flex flex-column p-3">
            <div class="mb-4"><span class="fs-4">Barangay Camaya</span></div>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item"><a href="user_home.php" class="nav-link text-white"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                <li class="nav-item"><a href="edit_profile.php" class="nav-link text-white"><i class="bi bi-person-circle me-2"></i> My Profile</a></li>
                <li class="nav-item"><a href="file_incidents.php" class="nav-link text-white"><i class="bi bi-exclamation-triangle me-2"></i> File Incident</a></li>
                <li class="nav-item"><a href="view_projects.php" class="nav-link text-white active"><i class="bi bi-building me-2"></i> View Projects</a></li>
                <li class="nav-item"><a href="view_incidents.php" class="nav-link text-white"><i class="bi bi-eye me-2"></i> View Incidents</a></li>
            </ul>
            <div class="mt-auto">
                <a href="logout.php" class="btn btn-primary w-100"><i class="bi bi-box-arrow-right me-1"></i> LOGOUT</a>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="flex-grow-1 bg-light" id="page-content">
            <div class="container p-5">
                <h1 class="h3 mb-4">Latest Infrastructure Projects</h1>
                <div class="row g-4">
                    <?php if ($projRs && $projRs->num_rows): ?>
                        <?php foreach ($projRs as $p): ?>
                            <div class="col-sm-6 col-lg-4">
                                <div class="card shadow-sm h-100" style="background-color: <?= getRandomPastelColor() ?>;">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= $p['project_name'] ?></h5>
                                        <p class="card-text"><?= $p['project_description'] ?></p>
                                        <p class="small mb-1">Start: <?= $p['start_date'] ?></p>
                                        <p class="small mb-1">End: <?= $p['end_date'] ?></p>
                                        <p class="small">Status: <?= $p['project_status'] ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach ?>
                    <?php else: ?>
                        <p class="text-center">No projects available.</p>
                    <?php endif ?>
                </div>

                <!-- Back Button -->
                <a href="user_home.php" class="btn btn-secondary mt-4">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('btn-toggle')
            .addEventListener('click', () =>
                document.getElementById('sidebar').classList.toggle('d-none')
            );
    </script>
</body>

</html>