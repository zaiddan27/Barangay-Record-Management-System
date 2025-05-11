<?php
session_start();
require_once "connection.php";

// Only allow admin or employee to view logs
if (empty($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'employee')) {
    header('Location: login.php');
    exit;
}

$msg = ''; // Initialize message variable

// Archive logs older than 30 days
$archiveQuery = "
    INSERT INTO lag_archive (lag_id, user_id, action, DateTime)
    SELECT lag_id, user_id, action, DateTime
    FROM lag
    WHERE DateTime < NOW() - INTERVAL 30 DAY
";
$conn->query($archiveQuery);

// Delete archived logs from the main table
$deleteQuery = "DELETE FROM lag WHERE DateTime < NOW() - INTERVAL 30 DAY";
$conn->query($deleteQuery);

// Handle "Clear Logs" button
if (isset($_POST['clear_logs'])) {
    $clearLogsQuery = "DELETE FROM lag";
    if ($conn->query($clearLogsQuery)) {
        $msg = '<div class="alert alert-success">All logs cleared successfully.</div>';
    } else {
        $msg = '<div class="alert alert-danger">Failed to clear logs: ' . $conn->error . '</div>';
    }
}

// Handle individual log deletion
if (isset($_POST['delete_log_id'])) {
    $deleteLogId = (int)$_POST['delete_log_id'];
    $deleteLogQuery = "DELETE FROM lag WHERE lag_id = $deleteLogId";

    if ($conn->query($deleteLogQuery)) {
        $msg = '<div class="alert alert-success">Log deleted successfully.</div>';
    } else {
        $msg = '<div class="alert alert-danger">Failed to delete log.</div>';
    }
}

// Handle search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Fetch logs data with search and join
$logsQuery = "
    SELECT l.lag_id, u.fullname AS user_name, l.action, l.DateTime
    FROM lag l
    JOIN user u ON l.user_id = u.user_id
    WHERE u.fullname LIKE '%$search%' OR l.action LIKE '%$search%'
    ORDER BY l.DateTime DESC
    LIMIT 100
";
$logsResult = $conn->query($logsQuery);

// Check if the query executed successfully
if (!$logsResult) {
    die("Error in query: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
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
    <!-- Mobile top navbar -->
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
            <div class="d-flex align-items-center mb-4">
                <span class="fs-4">Barangay Camaya</span>
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
                <li class="nav-item">
                    <a href="users.php" class="nav-link text-white">
                        <i class="bi bi-person me-2"></i> Users
                    </a>
                </li>
                <li class="nav-item">
                    <a href="lags.php" class="nav-link text-white active">
                        <i class="bi bi-journal-text me-2"></i> Logs
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
                <h1 class="h3 mb-4">Logs</h1>

                <!-- Display Messages -->
                <?= $msg ?>

                <!-- Clear Logs Button -->
                <form method="post" onsubmit="return confirm('Are you sure you want to clear all logs?');">
                    <button type="submit" name="clear_logs" class="btn btn-danger mb-3">
                        <i class="bi bi-trash"></i> Clear Logs
                    </button>
                </form>

                <!-- Add and Search -->
                <div class="d-flex justify-content-between mb-3">
                    <form class="d-flex" method="get">
                        <input class="form-control me-2" type="search" name="search" placeholder="Search logs" value="<?= $search ?>">
                        <button class="btn btn-outline-primary" type="submit">Search</button>
                    </form>
                </div>

                <!-- Logs Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Log ID</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Date & Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($logsResult && $logsResult->num_rows > 0): ?>
                                <?php while ($log = $logsResult->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $log['lag_id'] ?></td>
                                        <td><?= $log['user_name'] ?></td>
                                        <td><?= $log['action'] ?></td>
                                        <td><?= date('F j, Y, g:i A', strtotime($log['DateTime'])) ?></td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm delete-log-btn" data-log-id="<?= $log['lag_id'] ?>">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4">No logs found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Back Button -->
                <a href="admin.php" class="btn btn-secondary mt-4">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('btn-toggle').addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('d-none');
        });
        document.querySelectorAll('#sidebar .nav-link').forEach(link => {
            if (link.href === window.location.href) link.classList.add('active');
        });




        document.querySelectorAll('.delete-log-btn').forEach(button => {
            button.addEventListener('click', function() {
                const logId = this.getAttribute('data-log-id');

                Swal.fire({
                    title: 'Are you sure you want to delete this log?',
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                },
                                body: `delete_log_id=${logId}`
                            })
                            .then(response => response.text())
                            .then(data => {
                                Swal.fire('Deleted!', 'Log deleted successfully.', 'success').then(() => {
                                    location.reload();
                                });
                            })
                            .catch(error => {
                                Swal.fire('Error!', 'Failed to delete the log.', 'error');
                            });
                    }
                });
            });
        });
    </script>
</body>

</html>