<?php
session_start();
require_once "connection.php";

// Only admins or employees may access this page
if (empty($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'employee')) {
    header('Location: login.php');
    exit;
}

$userRole = $_SESSION['role']; // Get the role of the logged-in user
$msg = ''; // Message to display feedback to the user

// Handle edit functionality
if (isset($_POST['edit_incident'])) {
    $report_id = (int)$_POST['report_id'];
    $resident_id = $_POST['resident_id'];
    $official_id = $_POST['official_id'];
    $incident_type = $_POST['incident_type'];
    $incident_details = $_POST['incident_details'];
    $date_reported = $_POST['date_reported'];

    $updateSql = "UPDATE incident_report 
                  SET resident_id = '$resident_id', 
                      official_id = '$official_id', 
                      incident_type = '$incident_type', 
                      incident_details = '$incident_details', 
                      date_reported = '$date_reported' 
                  WHERE report_id = $report_id";

    if ($conn->query($updateSql)) {
        $msg = '<div class="alert alert-success">Incident updated successfully.</div>';
        $conn->query("INSERT INTO lag (user_id, action) VALUES ({$_SESSION['user_id']}, 'Updated incident: $incident_type')");
    } else {
        $msg = '<div class="alert alert-danger">Failed to update incident: ' . $conn->error . '</div>';
    }
}

// Handle add functionality
if (isset($_POST['add_incident'])) {
    $resident_id = $_POST['resident_id'];
    $official_id = $_POST['official_id'];
    $incident_type = $_POST['incident_type'];
    $incident_details = $_POST['incident_details'];
    $date_reported = $_POST['date_reported'];

    $insertSql = "INSERT INTO incident_report (resident_id, official_id, incident_type, incident_details, date_reported) 
                  VALUES ('$resident_id', '$official_id', '$incident_type', '$incident_details', '$date_reported')";

    if ($conn->query($insertSql)) {
        $msg = '<div class="alert alert-success">Incident added successfully.</div>';
        $conn->query("INSERT INTO lag (user_id, action) VALUES ({$_SESSION['user_id']}, 'Added a new incident: $incident_type')");
    } else {
        $msg = '<div class="alert alert-danger">Failed to add incident: ' . $conn->error . '</div>';
    }
}

// Fetch incidents with sorting and filtering
$searchInput04 = $_POST['searchInput04'] ?? '';
$filterPosition = $_POST['filter_position'] ?? '';
$filterOfficial = $_POST['filter_official'] ?? '';
$filterDate = $_POST['filter_date'] ?? '';

$selectsql = "SELECT ir.report_id, 
                     r.resident_id, 
                     r.resident_name, 
                     bo.official_id, 
                     bo.official_name, 
                     bo.position, 
                     ir.incident_type, 
                     ir.incident_details, 
                     ir.date_reported
              FROM incident_report ir
              LEFT JOIN resident r ON ir.resident_id = r.resident_id
              LEFT JOIN barangay_official bo ON ir.official_id = bo.official_id
              WHERE (ir.report_id LIKE '%$searchInput04%' 
                 OR r.resident_name LIKE '%$searchInput04%' 
                 OR bo.official_name LIKE '%$searchInput04%' 
                 OR bo.position LIKE '%$searchInput04%' 
                 OR ir.incident_type LIKE '%$searchInput04%' 
                 OR ir.incident_details LIKE '%$searchInput04%' 
                 OR ir.date_reported LIKE '%$searchInput04%')";

if (!empty($filterPosition)) {
    $selectsql .= " AND bo.position = '$filterPosition'";
}

if (!empty($filterOfficial)) {
    $selectsql .= " AND bo.official_id = '$filterOfficial'";
}

if (!empty($filterDate)) {
    $selectsql .= " AND ir.date_reported = '$filterDate'";
}

$selectsql .= " ORDER BY ir.date_reported DESC"; // Sort by date_reported in descending order
$result = $conn->query($selectsql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incidents</title>
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
    </style>
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
        <!-- Sidebar -->
        <nav id="sidebar" class="bg-dark text-white d-flex flex-column p-3">
            <div class="d-flex align-items-center mb-4">
                <span class="fs-4">CAMAYA</span>
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
                <?php if ($_SESSION['role'] === 'admin'): ?>
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
                <?php endif; ?>
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
                <h1 class="h3 mb-4">Incidents</h1>
                <?= $msg ?>
                <form action="incidents.php" method="post" class="mb-4">
                    <div class="row g-3">
                        <div class="col-auto">
                            <input type="search" name="searchInput04" placeholder="Search Incidents" class="form-control" value="<?= $searchInput04 ?>">
                        </div>
                        <div class="col-auto">
                            <input type="submit" name="btnsearch04" value="Search" class="btn btn-primary">
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Report ID</th>
                                <th>Resident Name</th>
                                <th>Official Name</th>
                                <th>Position</th>
                                <th>Incident Type</th>
                                <th>Incident Details</th>
                                <th>Date Reported</th>
                                <?php if ($userRole === 'admin' || $userRole === 'employee'): ?>
                                    <th>Actions</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $row['report_id'] ?></td>
                                        <td><?= $row['resident_name'] ?? 'Unknown' ?></td>
                                        <td><?= $row['official_name'] ?? 'Unknown' ?></td>
                                        <td><?= $row['position'] ?></td>
                                        <td><?= $row['incident_type'] ?></td>
                                        <td><?= $row['incident_details'] ?></td>
                                        <td><?= date('Y-m-d', strtotime($row['date_reported'])) ?></td>
                                        <?php if ($userRole === 'admin' || $userRole === 'employee'): ?>
                                            <td>
                                                <button
                                                    class="btn btn-warning btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editModal"
                                                    onclick="populateModal(<?= htmlspecialchars(json_encode($row)) ?>)">
                                                    Edit
                                                </button>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">No records found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($userRole === 'admin' || $userRole === 'employee'): ?>
                    <h2 class="h5 mt-4">Add New Incident</h2>
                    <form action="incidents.php" method="post" class="row g-3">
                        <div class="col-md-6">
                            <label for="resident_id" class="form-label">Resident Name</label>
                            <select name="resident_id" class="form-select" required>
                                <option value="" disabled selected>Select Resident</option>
                                <?php
                                $residents = $conn->query("SELECT resident_id, resident_name FROM resident");
                                while ($resident = $residents->fetch_assoc()) {
                                    echo '<option value="' . $resident['resident_id'] . '">' . $resident['resident_name'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="official_id" class="form-label">Official Name</label>
                            <select name="official_id" class="form-select" required>
                                <option value="" disabled selected>Select Official</option>
                                <?php
                                $officials = $conn->query("SELECT official_id, official_name FROM barangay_official");
                                while ($official = $officials->fetch_assoc()) {
                                    echo '<option value="' . $official['official_id'] . '">' . $official['official_name'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="incident_type" class="form-label">Incident Type</label>
                            <input type="text" name="incident_type" class="form-control" placeholder="Incident Type" required>
                        </div>
                        <div class="col-md-6">
                            <label for="incident_details" class="form-label">Incident Details</label>
                            <input type="text" name="incident_details" class="form-control" placeholder="Incident Details" required>
                        </div>
                        <div class="col-md-6">
                            <label for="date_reported" class="form-label">Date Reported</label>
                            <input type="date" name="date_reported" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <button type="submit" name="add_incident" class="btn btn-primary">Add Incident</button>
                            <a href="admin.php" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="incidents.php" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Incident</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="report_id" id="modal-report-id">

                        <!-- Resident Dropdown -->
                        <div class="mb-3">
                            <label for="modal-resident-id" class="form-label">Resident Name</label>
                            <select name="resident_id" id="modal-resident-id" class="form-select" required>
                                <option value="" disabled>Select Resident</option>
                                <?php
                                $residents = $conn->query("SELECT resident_id, resident_name FROM resident");
                                while ($resident = $residents->fetch_assoc()) {
                                    echo '<option value="' . $resident['resident_id'] . '">' . $resident['resident_name'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Official Dropdown -->
                        <div class="mb-3">
                            <label for="modal-official-id" class="form-label">Official Name</label>
                            <select name="official_id" id="modal-official-id" class="form-select" required>
                                <option value="" disabled>Select Official</option>
                                <?php
                                $officials = $conn->query("SELECT official_id, official_name FROM barangay_official");
                                while ($official = $officials->fetch_assoc()) {
                                    echo '<option value="' . $official['official_id'] . '">' . $official['official_name'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Other Fields -->
                        <div class="mb-3">
                            <label for="modal-incident-type" class="form-label">Incident Type</label>
                            <input type="text" name="incident_type" id="modal-incident-type" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="modal-incident-details" class="form-label">Incident Details</label>
                            <textarea name="incident_details" id="modal-incident-details" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="modal-date-reported" class="form-label">Date Reported</label>
                            <input type="date" name="date_reported" id="modal-date-reported" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="edit_incident" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
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

        // Update position dynamically based on selected official
        function updatePosition(selectElement, reportId) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const position = selectedOption.getAttribute('data-position');
            document.getElementById(`position-${reportId}`).value = position;
        }

        // Populate modal with current record data
        function populateModal(data) {
            document.getElementById('modal-report-id').value = data.report_id;

            // Set the selected resident
            const residentDropdown = document.getElementById('modal-resident-id');
            for (let i = 0; i < residentDropdown.options.length; i++) {
                if (residentDropdown.options[i].value == data.resident_id) {
                    residentDropdown.options[i].selected = true;
                    break;
                }
            }

            // Set the selected official
            const officialDropdown = document.getElementById('modal-official-id');
            for (let i = 0; i < officialDropdown.options.length; i++) {
                if (officialDropdown.options[i].value == data.official_id) {
                    officialDropdown.options[i].selected = true;
                    break;
                }
            }

            document.getElementById('modal-incident-type').value = data.incident_type;
            document.getElementById('modal-incident-details').value = data.incident_details;
            document.getElementById('modal-date-reported').value = data.date_reported;
        }
    </script>

    <!-- functionality for full list display after no letters inside box -->
    <script>
        document.querySelector('input[name="searchInput04"]').addEventListener('input', function() {
            if (this.value === '') {
                this.form.submit();
            }
        });
    </script>
</body>

</html>