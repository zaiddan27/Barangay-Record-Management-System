<?php
session_start();
require_once "connection.php";

// Set timezone to Asia/Manila
date_default_timezone_set('Asia/Manila');

// Only residents may view
if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'resident') {
    header('Location: login.php');
    exit;
}

$residentName = $_SESSION['fullname'];
$residentId   = $_SESSION['user_id'];

// Fetch only this resident’s incidents, newest first
$incRs = $conn->query("
    SELECT incident_type, incident_details, date_reported
      FROM incident_report
     WHERE resident_id = (
         SELECT resident_id FROM resident WHERE user_id = $residentId
     )
     ORDER BY date_reported DESC
");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>View Incidents</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="mystyle.css">
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
                <li class="nav-item">
                    <a href="user_home.php" class="nav-link text-white">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="edit_profile.php" class="nav-link text-white">
                        <i class="bi bi-person-circle me-2"></i> My Profile
                    </a>
                </li>
                <li class="nav-item">
                    <a href="file_incidents.php" class="nav-link text-white">
                        <i class="bi bi-exclamation-triangle me-2"></i> File Incident
                    </a>
                </li>
                <li class="nav-item">
                    <a href="view_projects.php" class="nav-link text-white">
                        <i class="bi bi-building me-2"></i> View Projects
                    </a>
                </li>
                <li class="nav-item">
                    <a href="view_incidents.php" class="nav-link text-white active">
                        <i class="bi bi-eye me-2"></i> View Incidents
                    </a>
                </li>
            </ul>
            <div class="mt-auto">
                <a href="logout.php" class="btn btn-primary w-100">
                    <i class="bi bi-box-arrow-right me-1"></i> LOGOUT
                </a>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="flex-grow-1 bg-light" id="page-content">
            <div class="container p-5">
                <h1 class="h3 mb-4">My Incident Reports</h1>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Type</th>
                                <th>Details</th>
                                <th>Date Reported</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($incRs && $incRs->num_rows > 0): ?>
                                <?php foreach ($incRs as $i): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($i['incident_type']) ?></td>
                                        <td><?= htmlspecialchars($i['incident_details']) ?></td>
                                        <td><?= date('F j, Y, g:i A', strtotime($i['date_reported'])) ?></td>
                                    </tr>
                                <?php endforeach ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4">No incidents found</td>
                                </tr>
                            <?php endif ?>
                        </tbody>
                    </table>
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