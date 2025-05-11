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
if (isset($_POST['edit_official'])) {
    $official_id = (int)$_POST['official_id'];
    $official_name = $_POST['official_name'];
    $position = $_POST['position'];
    $contact_information = $_POST['contact_information'];

    $updateSql = "UPDATE barangay_official 
                  SET official_name = '$official_name', 
                      position = '$position', 
                      contact_information = '$contact_information' 
                  WHERE official_id = $official_id";

    if ($conn->query($updateSql)) {
        $msg = '<div class="alert alert-success">Official updated successfully.</div>';
    } else {
        $msg = '<div class="alert alert-danger">Failed to update official: ' . $conn->error . '</div>';
    }
}

// Handle add functionality
if (isset($_POST['add_official'])) {
    $official_name = $_POST['official_name'];
    $position = $_POST['position'];
    $contact_information = $_POST['contact_information'];

    $insertSql = "INSERT INTO barangay_official (official_name, position, contact_information) 
                  VALUES ('$official_name', '$position', '$contact_information')";

    if ($conn->query($insertSql)) {
        $msg = '<div class="alert alert-success">Official added successfully.</div>';
        $conn->query("INSERT INTO lag (user_id, action) VALUES ({$_SESSION['user_id']}, 'Added a new official: $official_name')");
    } else {
        $msg = '<div class="alert alert-danger">Failed to add official: ' . $conn->error . '</div>';
    }
}

// Fetch officials
$searchInput03 = $_POST['searchInput03'] ?? '';
$filterRole = $_POST['filter_role'] ?? '';

$selectsql = "SELECT * FROM barangay_official 
              WHERE (official_id LIKE '%$searchInput03%' 
                 OR official_name LIKE '%$searchInput03%' 
                 OR position LIKE '%$searchInput03%' 
                 OR contact_information LIKE '%$searchInput03%')";

if (!empty($filterRole)) {
    $selectsql .= " AND position = '$filterRole'";
}

$selectsql .= " ORDER BY official_id ASC"; // Sort by official_id
$result = $conn->query($selectsql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Barangay Officials</title>
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
        <!-- Sidebar -->
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
                <h1 class="h3 mb-4">Barangay Officials</h1>
                <?= $msg ?>
                <form action="officials.php" method="post" class="mb-4">
                    <div class="row g-3">
                        <div class="col-auto">
                            <input type="search" name="searchInput03" placeholder="Search Officials" class="form-control" value="<?= htmlspecialchars($searchInput03) ?>">
                        </div>
                        <div class="col-auto">
                            <select name="filter_role" class="form-select">
                                <option value="">All Roles</option>
                                <option value="Barangay Captain" <?= isset($_POST['filter_role']) && $_POST['filter_role'] === 'Barangay Captain' ? 'selected' : '' ?>>Barangay Captain</option>
                                <option value="Kagawad" <?= isset($_POST['filter_role']) && $_POST['filter_role'] === 'Kagawad' ? 'selected' : '' ?>>Kagawad</option>
                                <option value="Secretary" <?= isset($_POST['filter_role']) && $_POST['filter_role'] === 'Secretary' ? 'selected' : '' ?>>Secretary</option>
                                <option value="Treasurer" <?= isset($_POST['filter_role']) && $_POST['filter_role'] === 'Treasurer' ? 'selected' : '' ?>>Treasurer</option>
                                <option value="Barangay Health Worker" <?= isset($_POST['filter_role']) && $_POST['filter_role'] === 'Barangay Health Worker' ? 'selected' : '' ?>>Barangay Health Worker</option>
                                <option value="Barangay Tanod" <?= isset($_POST['filter_role']) && $_POST['filter_role'] === 'Barangay Tanod' ? 'selected' : '' ?>>Barangay Tanod</option>
                                <option value="Barangay Nutrition Scholar" <?= isset($_POST['filter_role']) && $_POST['filter_role'] === 'Barangay Nutrition Scholar' ? 'selected' : '' ?>>Barangay Nutrition Scholar</option>
                                <option value="Barangay Day Care Worker" <?= isset($_POST['filter_role']) && $_POST['filter_role'] === 'Barangay Day Care Worker' ? 'selected' : '' ?>>Barangay Day Care Worker</option>
                                <option value="Barangay Clerk" <?= isset($_POST['filter_role']) && $_POST['filter_role'] === 'Barangay Clerk' ? 'selected' : '' ?>>Barangay Clerk</option>
                                <option value="Barangay Maintenance Worker" <?= isset($_POST['filter_role']) && $_POST['filter_role'] === 'Barangay Maintenance Worker' ? 'selected' : '' ?>>Barangay Maintenance Worker</option>
                                <option value="Barangay Disaster Risk Reduction Officer" <?= isset($_POST['filter_role']) && $_POST['filter_role'] === 'Barangay Disaster Risk Reduction Officer' ? 'selected' : '' ?>>Barangay Disaster Risk Reduction Officer</option>
                                <option value="Barangay Environmental Officer" <?= isset($_POST['filter_role']) && $_POST['filter_role'] === 'Barangay Environmental Officer' ? 'selected' : '' ?>>Barangay Environmental Officer</option>
                                <option value="Barangay IT Officer" <?= isset($_POST['filter_role']) && $_POST['filter_role'] === 'Barangay IT Officer' ? 'selected' : '' ?>>Barangay IT Officer</option>
                                <option value="Barangay Youth Leader" <?= isset($_POST['filter_role']) && $_POST['filter_role'] === 'Barangay Youth Leader' ? 'selected' : '' ?>>Barangay Youth Leader</option>
                                <option value="Barangay Senior Citizen Representative" <?= isset($_POST['filter_role']) && $_POST['filter_role'] === 'Barangay Senior Citizen Representative' ? 'selected' : '' ?>>Barangay Senior Citizen Representative</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <input type="submit" name="btnsearch03" value="Search" class="btn btn-primary">
                        </div>
                    </div>
                </form>

                <!-- Officials Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Official ID</th>
                                <th>Official Name</th>
                                <th>Position</th>
                                <th>Contact Information</th>
                                <?php if ($userRole === 'admin' || $userRole === 'employee'): ?>
                                    <th>Actions</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $row['official_id'] ?></td>
                                        <td><?= $row['official_name'] ?></td>
                                        <td><?= $row['position'] ?></td>
                                        <td><?= $row['contact_information'] ?></td>
                                        <?php if ($userRole === 'admin' || $userRole === 'employee'): ?>
                                            <td>
                                                <button
                                                    class="btn btn-warning btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editModal"
                                                    onclick="populateModal(<?= json_encode($row) ?>)">
                                                    Edit
                                                </button>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No records found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="officials.php" method="post">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel">Edit Official</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="official_id" id="modal-official-id">
                                    <div class="mb-3">
                                        <label for="modal-official-name" class="form-label">Official Name</label>
                                        <input type="text" name="official_name" id="modal-official-name" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="modal-position" class="form-label">Position</label>
                                        <select name="position" id="modal-position" class="form-select" required>
                                            <option value="Barangay Captain">Barangay Captain</option>
                                            <option value="Kagawad">Kagawad</option>
                                            <option value="Secretary">Secretary</option>
                                            <option value="Treasurer">Treasurer</option>
                                            <option value="Barangay Health Worker">Barangay Health Worker</option>
                                            <option value="Barangay Tanod">Barangay Tanod</option>
                                            <option value="Barangay Nutrition Scholar">Barangay Nutrition Scholar</option>
                                            <option value="Barangay Day Care Worker">Barangay Day Care Worker</option>
                                            <option value="Barangay Clerk">Barangay Clerk</option>
                                            <option value="Barangay Maintenance Worker">Barangay Maintenance Worker</option>
                                            <option value="Barangay Disaster Risk Reduction Officer">Barangay Disaster Risk Reduction Officer</option>
                                            <option value="Barangay Environmental Officer">Barangay Environmental Officer</option>
                                            <option value="Barangay IT Officer">Barangay IT Officer</option>
                                            <option value="Barangay Youth Leader">Barangay Youth Leader</option>
                                            <option value="Barangay Senior Citizen Representative">Barangay Senior Citizen Representative</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="modal-contact-information" class="form-label">Contact Information</label>
                                        <input type="text" name="contact_information" id="modal-contact-information" class="form-control" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" name="edit_official" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <script>
                    // Populate modal with current record data
                    function populateModal(data) {
                        // Assign values to modal fields
                        document.getElementById('modal-official-id').value = data.official_id;
                        document.getElementById('modal-official-name').value = data.official_name;
                        document.getElementById('modal-position').value = data.position;
                        document.getElementById('modal-contact-information').value = data.contact_information;
                    }
                </script>

                <!-- Add New Official Section -->
                <?php if ($userRole === 'admin' || $userRole === 'employee'): ?>
                    <h2 class="h5 mt-4">Add New Official</h2>
                    <form action="officials.php" method="post" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="official_name" class="form-control" placeholder="Official Name" required>
                        </div>
                        <div class="col-md-4">
                            <select name="position" class="form-select" required>
                                <option value="" disabled selected>Select Position</option>
                                <option value="Barangay Captain">Barangay Captain</option>
                                <option value="Kagawad">Kagawad</option>
                                <option value="Secretary">Secretary</option>
                                <option value="Treasurer">Treasurer</option>
                                <option value="Barangay Health Worker">Barangay Health Worker</option>
                                <option value="Barangay Tanod">Barangay Tanod</option>
                                <option value="Barangay Nutrition Scholar">Barangay Nutrition Scholar</option>
                                <option value="Barangay Day Care Worker">Barangay Day Care Worker</option>
                                <option value="Barangay Clerk">Barangay Clerk</option>
                                <option value="Barangay Maintenance Worker">Barangay Maintenance Worker</option>
                                <option value="Barangay Disaster Risk Reduction Officer">Barangay Disaster Risk Reduction Officer</option>
                                <option value="Barangay Environmental Officer">Barangay Environmental Officer</option>
                                <option value="Barangay IT Officer">Barangay IT Officer</option>
                                <option value="Barangay Youth Leader">Barangay Youth Leader</option>
                                <option value="Barangay Senior Citizen Representative">Barangay Senior Citizen Representative</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="contact_information" class="form-control" placeholder="Contact Information" required>
                        </div>
                        <div class="col-12">
                            <button type="submit" name="add_official" class="btn btn-primary">Add Official</button>
                            <a href="admin.php" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- for nav -->
    <script>
        document.getElementById('btn-toggle').addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('d-none');
        });
        document.querySelectorAll('#sidebar .nav-link').forEach(link => {
            if (link.href === window.location.href) link.classList.add('active');
        });
    </script>
</body>

</html>