<?php
session_start();
require_once "connection.php";

// if not logged in, redirect to login
if (empty($_SESSION['resident_id'])) {
    header('Location: login.php');
    exit;
}
$residentName = $_SESSION['resident_name'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Resident Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <span class="navbar-brand">Barangay Camaya</span>
            <span class="text-white">Welcome, <?= htmlspecialchars($residentName) ?>!</span>
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

        <!-- Infrastructure Projects -->
        <h4>Latest Projects</h4>
        <div class="table-responsive mb-5">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Project Name</th>
                        <th>Description</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $projRs = $conn->query("SELECT project_name, project_description, start_date, end_date, project_status FROM infrastructure_projects ORDER BY start_date DESC");
                    if ($projRs && $projRs->num_rows) {
                        foreach ($projRs as $p) {
                            echo "<tr>",
                            "<td>" . htmlspecialchars($p['project_name']) . "</td>",
                            "<td>" . htmlspecialchars($p['project_description']) . "</td>",
                            "<td>{$p['start_date']}</td>",
                            "<td>{$p['end_date']}</td>",
                            "<td>" . htmlspecialchars($p['project_status']) . "</td>",
                            "</tr>";
                        }
                    } else {
                        echo '<tr><td colspan="5" class="text-center">No projects to show</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Incident Reports -->
        <h4>Recent Incidents</h4>
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
                    <?php
                    // show only *their* own incidents, or all? adjust WHERE resident_id=... if needed
                    $incRs = $conn->query(
                        "SELECT incident_type, incident_details, date_reported 
           FROM incident_report 
           WHERE resident_id = {$_SESSION['resident_id']}
           ORDER BY date_reported DESC"
                    );
                    if ($incRs && $incRs->num_rows) {
                        foreach ($incRs as $i) {
                            echo "<tr>",
                            "<td>" . htmlspecialchars($i['incident_type']) . "</td>",
                            "<td>" . htmlspecialchars($i['incident_details']) . "</td>",
                            "<td>{$i['date_reported']}</td>",
                            "</tr>";
                        }
                    } else {
                        echo '<tr><td colspan="3" class="text-center">No incidents reported yet</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>