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
if (isset($_POST['edit_project'])) {
    $project_id = (int)$_POST['project_id'];
    $project_name = $_POST['project_name'];
    $project_description = $_POST['project_description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $project_status = $_POST['project_status'];

    $updateSql = "UPDATE infrastructure_projects 
                  SET project_name = '$project_name', 
                      project_description = '$project_description', 
                      start_date = '$start_date', 
                      end_date = '$end_date', 
                      project_status = '$project_status' 
                  WHERE project_id = $project_id";

    if ($conn->query($updateSql)) {
        $msg = '<div class="alert alert-success">Project updated successfully.</div>';
        $conn->query("INSERT INTO lag (user_id, action) VALUES ({$_SESSION['user_id']}, 'Updated project: $project_name')");
    } else {
        $msg = '<div class="alert alert-danger">Failed to update project: ' . $conn->error . '</div>';
    }
}

// Handle add functionality
if (isset($_POST['add_project'])) {
    $project_name = $_POST['project_name'];
    $project_description = $_POST['project_description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $project_status = $_POST['project_status'];

    $insertSql = "INSERT INTO infrastructure_projects (project_name, project_description, start_date, end_date, project_status) 
                  VALUES ('$project_name', '$project_description', '$start_date', '$end_date', '$project_status')";

    if ($conn->query($insertSql)) {
        $msg = '<div class="alert alert-success">Project added successfully.</div>';
        $conn->query("INSERT INTO lag (user_id, action) VALUES ({$_SESSION['user_id']}, 'Added a new project: $project_name')");
    } else {
        $msg = '<div class="alert alert-danger">Failed to add project: ' . $conn->error . '</div>';
    }
}

// Fetch projects
$searchInput01 = $_POST['searchInput01'] ?? '';
$filterStatus = $_POST['filter_status'] ?? '';

$selectsql = "SELECT * FROM infrastructure_projects 
              WHERE (project_id LIKE '%$searchInput01%' 
                 OR project_name LIKE '%$searchInput01%' 
                 OR project_description LIKE '%$searchInput01%' 
                 OR start_date LIKE '%$searchInput01%' 
                 OR end_date LIKE '%$searchInput01%' 
                 OR project_status LIKE '%$searchInput01%')";

if (!empty($filterStatus)) {
    $selectsql .= " AND project_status = '$filterStatus'";
}

$selectsql .= " ORDER BY start_date DESC"; // Sort by start_date in descending order

$result = $conn->query($selectsql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects</title>
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
                <h1 class="h3 mb-4">Projects</h1>
                <?= $msg ?>
                <form action="projects.php" method="post" class="mb-4">
                    <div class="row g-3">
                        <div class="col-auto">
                            <input type="search" name="searchInput01" placeholder="Search Projects" class="form-control" value="<?= htmlspecialchars($searchInput01) ?>">
                        </div>
                        <div class="col-auto">
                            <select name="filter_status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="Planned" <?= $filterStatus === 'Planned' ? 'selected' : '' ?>>Planned</option>
                                <option value="Ongoing" <?= $filterStatus === 'Ongoing' ? 'selected' : '' ?>>Ongoing</option>
                                <option value="Completed" <?= $filterStatus === 'Completed' ? 'selected' : '' ?>>Completed</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <input type="submit" name="btnsearch01" value="Search" class="btn btn-primary">
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Project ID</th>
                                <th>Project Name</th>
                                <th>Project Description</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Project Status</th>
                                <?php if ($userRole === 'admin' || $userRole === 'employee'): ?>
                                    <th>Actions</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $row['project_id'] ?></td>
                                        <td><?= htmlspecialchars($row['project_name']) ?></td>
                                        <td><?= htmlspecialchars($row['project_description']) ?></td>
                                        <td><?= date('Y-m-d', strtotime($row['start_date'])) ?></td>
                                        <td><?= date('Y-m-d', strtotime($row['end_date'])) ?></td>
                                        <td><?= htmlspecialchars($row['project_status']) ?></td>
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
                                    <td colspan="7" class="text-center">No records found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($userRole === 'admin' || $userRole === 'employee'): ?>
                    <h2 class="h5 mt-4">Add New Project</h2>
                    <form action="projects.php" method="post" class="row g-3">
                        <div class="col-md-6">
                            <input type="text" name="project_name" class="form-control" placeholder="Project Name" required>
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="project_description" class="form-control" placeholder="Project Description" required>
                        </div>
                        <div class="col-md-6">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="project_status" class="form-label">Project Status</label>
                            <select name="project_status" class="form-select" required>
                                <option value="Planned">Planned</option>
                                <option value="Ongoing">Ongoing</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" name="add_project" class="btn btn-primary">Add Project</button>
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
                <form action="projects.php" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Project</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="project_id" id="modal-project-id">
                        <div class="mb-3">
                            <label for="modal-project-name" class="form-label">Project Name</label>
                            <input type="text" name="project_name" id="modal-project-name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="modal-project-description" class="form-label">Project Description</label>
                            <textarea name="project_description" id="modal-project-description" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="modal-start-date" class="form-label">Start Date</label>
                            <input type="date" name="start_date" id="modal-start-date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="modal-end-date" class="form-label">End Date</label>
                            <input type="date" name="end_date" id="modal-end-date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="modal-project-status" class="form-label">Project Status</label>
                            <select name="project_status" id="modal-project-status" class="form-select" required>
                                <option value="Planned">Planned</option>
                                <option value="Ongoing">Ongoing</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="edit_project" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
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

        function populateEditModal(project) {
            document.getElementById('modal-project-id').value = project.project_id;
            document.getElementById('modal-project-name').value = project.project_name;
            document.getElementById('modal-project-description').value = project.project_description;
            document.getElementById('modal-start-date').value = project.start_date;
            document.getElementById('modal-end-date').value = project.end_date;
            document.getElementById('modal-project-status').value = project.project_status;
        }
    </script>

    <script>
        // Populate modal with current record data
        function populateModal(data) {
            document.getElementById('modal-project-id').value = data.project_id;
            document.getElementById('modal-project-name').value = data.project_name;
            document.getElementById('modal-project-description').value = data.project_description;
            document.getElementById('modal-start-date').value = data.start_date;
            document.getElementById('modal-end-date').value = data.end_date;
            document.getElementById('modal-project-status').value = data.project_status;
        }
    </script>
</body>

</html>