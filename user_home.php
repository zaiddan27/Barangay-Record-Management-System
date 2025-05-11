<?php
session_start();
if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'resident') {
    header('Location: login.php');
    exit;
}

require_once "connection.php";

$user_id = $_SESSION['user_id']; // Use user_id from the session
$resident_name = $_SESSION['fullname'];
$resident_role = $_SESSION['role'];

// Fetch the resident_id for the logged-in user
$residentQuery = "SELECT resident_id FROM resident WHERE user_id = $user_id";
$residentResult = $conn->query($residentQuery);

if ($residentResult->num_rows == 1) {
    $resident = $residentResult->fetch_assoc();
    $resident_id = $resident['resident_id'];
} else {
    $resident_id = null; // Fallback if no resident_id is found
}

// Fetch the address for this resident using user_id as the foreign key
$addressQuery = "SELECT address FROM resident WHERE user_id = $user_id";
$addressResult = $conn->query($addressQuery);

if ($addressResult->num_rows == 1) {
    $resident = $addressResult->fetch_assoc();
    $resident_address = $resident['address'];
} else {
    $resident_address = "Address not found"; // Fallback if no address is found
}

// Fetch latest projects
$projRs = $conn->query("
    SELECT * 
      FROM infrastructure_projects 
     ORDER BY start_date DESC
");

// Fetch this resident’s incidents
if ($resident_id) {
    $incRs = $conn->query("
        SELECT * 
          FROM incident_report 
         WHERE resident_id = $resident_id
         ORDER BY date_reported DESC
    ");
} else {
    $incRs = false; // No incidents if resident_id is not found
}

// Function to generate random pastel colors
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
    <title>Resident Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="mystyle.css">
    <style>
        body {
            background: url('images/portalbgp.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .card:hover {
            transform: scale(1.02);
        }

        .section-title {
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
        }

        .card-title {
            font-weight: bold;
            color: #007bff;
        }

        .card-text {
            color: #555;
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

    <!-- mobile navbar -->
    <nav class="navbar navbar-dark bg-dark d-md-none">
        <div class="container-fluid">
            <button class="btn btn-dark" id="btn-toggle">
                <i class="bi bi-list fs-3"></i>
            </button>
            <span class="navbar-brand mb-0 ms-2">Barangay Camaya</span>
        </div>
    </nav>

    <div class="d-flex" id="wrapper">

        <!-- sidebar -->
        <nav id="sidebar" class="bg-dark text-white d-flex flex-column p-3">
            <div class="mb-4"><span class="fs-4">Barangay Camaya</span></div>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="user_home.php" class="nav-link text-white active">
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
                    <a href="view_incidents.php" class="nav-link text-white">
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

        <!-- page content -->
        <div class="flex-grow-1" id="page-content">
            <div class="container p-5">

                <h1 class="h3 mb-3">Dashboard</h1>

                <!-- profile info card -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title"><?= $resident_name ?></h5>
                        <p class="card-text mb-1"><strong>Address:</strong> <?= $resident_address ?></p>
                        <p class="card-text"><strong>Role:</strong> <?= ucfirst($resident_role) ?></p>
                    </div>
                </div>

                <!-- action buttons -->
                <div class="mb-5">
                    <a href="edit_profile.php" class="btn btn-primary me-2">
                        <i class="bi bi-person-circle"></i> Update My Info
                    </a>
                    <a href="file_incidents.php" class="btn btn-warning">
                        <i class="bi bi-exclamation-triangle"></i> Report an Incident
                    </a>
                </div>

                <!-- Barangay Mission and Vision -->
                <div class="mission-vision">
                    <h4 class="section-title">Barangay Camaya Mission and Vision</h4>
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Mission</h5>
                            <p class="card-text">
                                To provide quality services and programs that promote the welfare, safety, and development of the residents of Barangay Camaya.
                            </p>
                            <h5 class="card-title mt-4">Vision</h5>
                            <p class="card-text">
                                To be a progressive and united barangay that fosters a safe, sustainable, and inclusive community for all residents.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Latest Projects cards -->
                <h4 class="section-title">Latest Projects of Barangay Camaya</h4>
                <div class="row g-4 mb-5">
                    <?php if ($projRs && $projRs->num_rows > 0): ?>
                        <?php foreach ($projRs as $p): ?>
                            <div class="col-sm-6 col-lg-4">
                                <div class="card shadow-sm h-100" style="background-color: <?= getRandomPastelColor() ?>;">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= $p['project_name'] ?></h5>
                                        <p class="card-text"><?= $p['project_description'] ?></p>
                                        <p class="mb-1"><small><strong>Start:</strong> <?= $p['start_date'] ?></small></p>
                                        <p class="mb-1"><small><strong>End:</strong> <?= $p['end_date'] ?></small></p>
                                        <p><small><strong>Status:</strong> <?= $p['project_status'] ?></small></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center">No projects to show</div>
                    <?php endif; ?>
                </div>

                <!-- My Incidents cards -->
                <h4 class="section-title">My Incident Reports</h4>
                <div class="row g-4">
                    <?php if ($incRs && $incRs->num_rows > 0): ?>
                        <?php foreach ($incRs as $i): ?>
                            <div class="col-sm-6 col-lg-4">
                                <div class="card border-danger h-100" style="background-color: <?= getRandomPastelColor() ?>;">
                                    <div class="card-body">
                                        <h5 class="card-title text-danger"><?= $i['incident_type'] ?></h5>
                                        <p class="card-text"><?= $i['incident_details'] ?></p>
                                        <p class="text-muted"><small>Reported: <?= $i['date_reported'] ?></small></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center">No incidents reported yet</div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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