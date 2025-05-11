<?php
session_start();
require_once "connection.php";

// Set timezone to Asia/Manila
date_default_timezone_set('Asia/Manila');

// Only residents may file
if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'resident') {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id']; // Get user_id from session
$msg = ''; // Message to display feedback to the user

// Handle form submission
if (isset($_POST['submit'])) {
    // Get form data
    $official_id      = (int)$_POST['official_id'];
    $incident_type    = htmlspecialchars($_POST['incident_type']); // Basic sanitization
    $incident_details = htmlspecialchars($_POST['incident_details']); // Basic sanitization
    $date_reported    = date('Y-m-d H:i:s'); // Current date and time

    // Validate input
    if (empty($official_id) || empty($incident_type) || empty($incident_details)) {
        $msg = '<div class="alert alert-danger">All fields are required.</div>';
    } else {
        // Insert the incident report into the database
        $sql = "INSERT INTO incident_report (resident_id, official_id, incident_type, incident_details, date_reported)
                SELECT resident_id, $official_id, '$incident_type', '$incident_details', '$date_reported'
                FROM resident
                WHERE user_id = $userId";

        if ($conn->query($sql)) {
            $msg = '<div class="alert alert-success">Incident reported successfully.</div>';

            // Log the action in the lag table
            $logQuery = "INSERT INTO lag (user_id, action, DateTime) VALUES ($userId, 'Filed a new incident: $incident_type', NOW())";
            if (!$conn->query($logQuery)) {
                $msg .= '<div class="alert alert-danger">Failed to log action: ' . $conn->error . '</div>';
            }
        } else {
            $msg = '<div class="alert alert-danger">Failed to file incident report: ' . $conn->error . '</div>';
        }
    }
}

// Load barangay officials
$offRs = $conn->query("SELECT * FROM barangay_official");
$officials = [];
if ($offRs && $offRs->num_rows > 0) {
    while ($row = $offRs->fetch_assoc()) {
        $officials[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>File Incident</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 -->
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

    <!-- Mobile navbar -->
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
                    <a href="file_incidents.php" class="nav-link text-white active">
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

        <!-- Page Content -->
        <div class="flex-grow-1 bg-light" id="page-content">
            <div class="container p-5">
                <h1 class="h3 mb-4">File New Incident</h1>
                <?= $msg ?>
                <form id="fileIncidentForm" method="post">
                    <div class="mb-3">
                        <label class="form-label">Assign To Official</label>
                        <select name="official_id" class="form-select" required>
                            <option value="" disabled selected>Select an official</option>
                            <?php foreach ($officials as $official): ?>
                                <option value="<?= $official['official_id'] ?>">
                                    <?= $official['official_name'] ?> (<?= $official['position'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Incident Type</label>
                        <input name="incident_type" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Details</label>
                        <textarea name="incident_details" class="form-control" rows="4" required></textarea>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary">Submit Report</button>
                    <a href="user_home.php" class="btn btn-secondary ms-2 bi bi-arrow-left">Back</a>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('btn-toggle')
            .addEventListener('click', () =>
                document.getElementById('sidebar').classList.toggle('d-none')
            );
    </script>
</body>

</html>