<?php
// resident_home.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once "connection.php";

$residentName = $_SESSION['fullname'];
$residentId   = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF‑8">
    <meta name="viewport" content="width=device‑width,initial-scale=1.0">
    <title>Resident Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <span class="navbar-brand">Barangay Camaya</span>
            <span class="text-white">Welcome, <?= $residentName ?>!</span>
            <a href="logout.php" class="btn btn-outline-light">Logout</a>
        </div>
    </nav>

    <div class="container py-4">
        <p class="lead">Use the buttons below to update your info or file an incident report.</p>
        <div class="mb-5">
            <a href="user_input.php?type=profile" class="btn btn-primary me-2">
                <i class="bi bi-person-circle"></i> Update My Info
            </a>
            <a href="user_input.php?type=incident" class="btn btn-warning">
                <i class="bi bi-exclamation-triangle"></i> Report an Incident
            </a>
        </div>

        <!-- Projects -->
        <h4>Latest Projects</h4>
        <div class="row g-3 mb-5">
            <?php
            $projRs = $conn->query(
                "SELECT * FROM infrastructure_projects ORDER BY start_date DESC"
            );
            if ($projRs->num_rows > 0) {
                foreach ($projRs as $p) {
                    echo "<div class='col-sm-6 col-lg-4'>",
                    "<div class='card'>",
                    "<div class='card-body'>",
                    "<h5>" . $p['project_name'] . "</h5>",
                    "<p>" . $p['project_description'] . "</p>",
                    "<small>Start: {$p['start_date']}<br>End: {$p['end_date']}<br>Status: {$p['project_status']}</small>",
                    "</div></div></div>";
                }
            } else {
                echo "<p>No projects to show.</p>";
            }
            ?>
        </div>

        <!-- Incidents -->
        <h4>My Incidents</h4>
        <div class="row g-3">
            <?php
            $incRs = $conn->query(
                "SELECT * FROM incident_report 
         WHERE resident_id = $residentId
         ORDER BY date_reported DESC"
            );
            if ($incRs->num_rows > 0) {
                foreach ($incRs as $i) {
                    echo "<div class='col-sm-6 col-lg-4'>",
                    "<div class='card border-danger'>",
                    "<div class='card-body'>",
                    "<h6>" . $i['incident_type'] . "</h6>",
                    "<p>" . $i['incident_details'] . "</p>",
                    "<small>Reported: {$i['date_reported']}</small>",
                    "</div></div></div>";
                }
            } else {
                echo "<p>No incidents reported yet.</p>";
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>