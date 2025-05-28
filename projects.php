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

    // Handle image upload for edit
    $img_path_sql = "";
    if (isset($_FILES['edit_img_path']) && $_FILES['edit_img_path']['error'] == 0) {
        $target_dir = "images/projects/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $img_path = $target_dir . basename($_FILES["edit_img_path"]["name"]);
        move_uploaded_file($_FILES["edit_img_path"]["tmp_name"], $img_path);
        $img_path_sql = ", img_path = '$img_path'";
    }

    $updateSql = "UPDATE infrastructure_projects 
                  SET project_name = '$project_name', 
                      project_description = '$project_description', 
                      start_date = '$start_date', 
                      end_date = '$end_date', 
                      project_status = '$project_status'
                      $img_path_sql
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

    // Handle image upload
    $img_path = '';
    if (isset($_FILES['img_path']) && $_FILES['img_path']['error'] == 0) {
        $target_dir = "images/projects/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $img_path = $target_dir . basename($_FILES["img_path"]["name"]);
        move_uploaded_file($_FILES["img_path"]["tmp_name"], $img_path);
    }

    $insertSql = "INSERT INTO infrastructure_projects (project_name, project_description, start_date, end_date, project_status, img_path) 
                  VALUES ('$project_name', '$project_description', '$start_date', '$end_date', '$project_status', '$img_path')";

    if ($conn->query($insertSql)) {
        $msg = '<div class="alert alert-success">Project added successfully.</div>';
        $conn->query("INSERT INTO lag (user_id, action) VALUES ({$_SESSION['user_id']}, 'Added a new project: $project_name')");
    } else {
        $msg = '<div class="alert alert-danger">Failed to add project: ' . $conn->error . '</div>';
    }
}

// Handle delete functionality
if (isset($_POST['delete_project'])) {
    $project_id = (int)$_POST['project_id'];
    // Optionally, delete the image file from the server here if you want
    $conn->query("DELETE FROM infrastructure_projects WHERE project_id = $project_id");
    $msg = '<div class="alert alert-success">Project deleted successfully.</div>';
    $conn->query("INSERT INTO lag (user_id, action) VALUES ({$_SESSION['user_id']}, 'Deleted project ID: $project_id')");
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
                                <th>Image</th>
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
                                        <td>
                                            <?php if (!empty($row['img_path'])): ?>
                                                <img src="<?= htmlspecialchars($row['img_path']) ?>" width="80" height="60" style="object-fit:cover;">
                                            <?php else: ?>
                                                <span class="text-muted">No image</span>
                                            <?php endif; ?>
                                        </td>
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
                    <h2 class="h5 mt-4">Add New Project</h2>
                    <form action="projects.php" method="post" class="row g-3" enctype="multipart/form-data">
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
                        <div class="col-md-6">
                            <label for="img_path" class="form-label">Project Image</label>
                            <input type="file" name="img_path" id="img_path" class="form-control" accept="image/*">
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
                <form action="projects.php" method="post" enctype="multipart/form-data" id="editProjectForm">
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
                        <div class="mb-3">
                            <label for="modal-edit-img-path" class="form-label">Change Project Image</label>
                            <input type="file" name="edit_img_path" id="modal-edit-img-path" class="form-control" accept="image/*">
                            <div class="mt-2">
                                <img id="modal-current-img" src="" alt="Current Image" style="max-width:100px; max-height:80px; object-fit:cover;">
                            </div>
                            <small class="text-muted">Leave blank to keep current image.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="edit_project" class="btn btn-primary">Save Changes</button>
                        <button type="button" class="btn btn-danger" id="deleteProjectBtn">Delete</button>
                    </div>
                </form>
                <!-- Hidden form for delete -->
                <form action="projects.php" method="post" id="deleteProjectForm" style="display:none;">
                    <input type="hidden" name="project_id" id="delete-project-id">
                    <input type="hidden" name="delete_project" value="1">
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
        // Populate modal with current record data, including image
        function populateModal(data) {
            document.getElementById('modal-project-id').value = data.project_id;
            document.getElementById('modal-project-name').value = data.project_name;
            document.getElementById('modal-project-description').value = data.project_description;
            document.getElementById('modal-start-date').value = data.start_date;
            document.getElementById('modal-end-date').value = data.end_date;
            document.getElementById('modal-project-status').value = data.project_status;
            document.getElementById('modal-current-img').src = data.img_path ? data.img_path : '';
            document.getElementById('delete-project-id').value = data.project_id;
        }

        // SweetAlert for delete confirmation
        document.getElementById('deleteProjectBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "This will permanently delete the project.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteProjectForm').submit();
                }
            });
        });
    </script>

    <!-- search clear full list again functionality  -->
    <script>
        document.querySelector('input[name="searchInput01"]').addEventListener('input', function() {
            if (this.value === '') {
                this.form.submit();
            }
        });
    </script>
</body>

</html>