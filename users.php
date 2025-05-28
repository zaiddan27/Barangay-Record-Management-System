<!-- barangay_official.php -->

<?php
session_start(); // Start the session

// Check if the user is logged in and has a role
if (!isset($_SESSION['role'])) {
    // Redirect to login page if the session is not set
    header('Location: login.php');
    exit;
}

require_once "connection.php";

$msg = ''; // Message to display feedback to the user

// Search functionality
$searchInput10 = $_POST['searchInput10'] ?? '';
$searchInput10 = str_replace("'", "''", $searchInput10); // Escape single quotes for search input

if (!empty($searchInput10)) {
    $selectsql = "SELECT * FROM user WHERE 
                  user_id LIKE '%$searchInput10%' 
                  OR fullname LIKE '%$searchInput10%' 
                  OR role LIKE '%$searchInput10%' 
                  OR email LIKE '%$searchInput10%'";
} else {
    $selectsql = "SELECT * FROM user";
}

// Add user functionality
if (isset($_POST['add_user'])) {
    $fullname = $_POST['fullname'];
    $role = $_POST['role'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = md5($_POST['password']); // Hash the password

    $insertSql = "INSERT INTO user (fullname, role, username, password, email) 
                  VALUES ('$fullname', '$role', '$username', '$password', '$email')";

    if ($conn->query($insertSql)) {
        $msg = '<div class="alert alert-success">User added successfully.</div>';
        $conn->query("INSERT INTO lag (user_id, action) VALUES ({$_SESSION['user_id']}, 'Added a new user: $fullname')");
    } else {
        $msg = '<div class="alert alert-danger">Failed to add user: ' . $conn->error . '</div>';
    }
}

// Edit user functionality
if (isset($_POST['edit_user'])) {
    $user_id = (int)$_POST['user_id'];
    $fullname = $_POST['fullname'];
    $role = $_POST['role'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($password)) {
        if (strlen($password) !== 32) {
            $password = md5($password); // Hash the password if not already hashed
        }
        $updateSql = "UPDATE user 
                      SET fullname = '$fullname', 
                          role = '$role', 
                          username = '$username', 
                          password = '$password', 
                          email = '$email' 
                      WHERE user_id = $user_id";
    } else {
        $updateSql = "UPDATE user 
                      SET fullname = '$fullname', 
                          role = '$role', 
                          username = '$username', 
                          email = '$email' 
                      WHERE user_id = $user_id";
    }

    if ($conn->query($updateSql)) {
        $msg = '<div class="alert alert-success">User updated successfully.</div>';
        $conn->query("INSERT INTO lag (user_id, action) VALUES ({$_SESSION['user_id']}, 'Updated user: $fullname')");
    } else {
        $msg = '<div class="alert alert-danger">Failed to update user: ' . $conn->error . '</div>';
    }
}

if (isset($_POST['user_id']) && isset($_POST['current_status'])) {
    $user_id = (int)$_POST['user_id'];
    $current_status = $_POST['current_status'];
    // Toggle status: if currently Active, set to Inactive; else set to Active
    $new_status = ($current_status === 'Active') ? 'Inactive' : 'Active';
    $update_sql = "UPDATE user SET status = '$new_status' WHERE user_id = $user_id";
    if ($conn->query($update_sql)) {
        echo "<div class='alert alert-success mt-3'>Account status updated successfully.</div>";
    } else {
        echo "<div class='alert alert-danger mt-3'>Error updating status: " . $conn->error . "</div>";
    }
    exit; // Important for AJAX
}

$result = $conn->query($selectsql);

if (!$result) {
    die("Error in SQL query: " . $conn->error . "<br>Query: " . $selectsql);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="mystyle.css">
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

        /* Switch styles */
        .switch {
            position: relative;
            display: inline-block;
            width: 46px;
            height: 24px;
            vertical-align: middle;
        }

        .switch input {
            display: none;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #1abc9c;
        }

        input:checked+.slider:before {
            transform: translateX(22px);
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
                <h1 class="h3 mb-4">Users</h1>
                <?= $msg ?>
                <form action="users.php" method="post" class="mb-4">
                    <div class="row g-3">
                        <div class="col-auto">
                            <input type="search" name="searchInput10" placeholder="Search Users" class="form-control" value="<?= htmlspecialchars($searchInput10) ?>">
                        </div>
                        <div class="col-auto">
                            <input type="submit" name="btnsearch10" value="Search" class="btn btn-primary">
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>User ID</th>
                                <th>Fullname</th>
                                <th>Role</th>
                                <th>Username</th>
                                <th>Password</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $row['user_id'] ?></td>
                                        <td><?= htmlspecialchars($row['fullname']) ?></td>
                                        <td><?= htmlspecialchars($row['role']) ?></td>
                                        <td><?= htmlspecialchars($row['username']) ?></td>
                                        <td><?= htmlspecialchars($row['password']) ?></td>
                                        <td><?= htmlspecialchars($row['email']) ?></td>
                                        <td>
                                            <label class="switch">
                                                <input type="checkbox" class="status-toggle"
                                                    data-user-id="<?= $row['user_id'] ?>"
                                                    data-current-status="<?= $row['status'] ?>"
                                                    <?= ($row['status'] === 'Active') ? 'checked' : '' ?>>
                                                <span class="slider"></span>
                                            </label>
                                            <span class="ms-2"><?= htmlspecialchars($row['status'] ?? 'Pending') ?></span>
                                        </td>
                                        <td>
                                            <button
                                                class="btn btn-warning btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editModal"
                                                onclick="populateModal(<?= htmlspecialchars(json_encode($row)) ?>)">
                                                Edit
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">No records found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <h2 class="h5 mt-4">Add New User</h2>
                <form action="users.php" method="post" class="row g-3">
                    <div class="col-md-6">
                        <label for="fullname" class="form-label">Fullname</label>
                        <input type="text" name="fullname" class="form-control" placeholder="Fullname" required>
                    </div>
                    <div class="col-md-6">
                        <label for="role" class="form-label">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="admin">Admin</option>
                            <option value="resident">Resident</option>
                            <option value="employee">Employee</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Username" required>
                    </div>
                    <div class="col-md-6">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
                        <a href="admin.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="users.php" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="user_id" id="modal-user-id">
                        <div class="mb-3">
                            <label for="modal-fullname" class="form-label">Fullname</label>
                            <input type="text" name="fullname" id="modal-fullname" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="modal-role" class="form-label">Role</label>
                            <select name="role" id="modal-role" class="form-select" required>
                                <option value="admin">Admin</option>
                                <option value="resident">Resident</option>
                                <option value="employee">Employee</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="modal-username" class="form-label">Username</label>
                            <input type="text" name="username" id="modal-username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="modal-password" class="form-label">Password</label>
                            <input type="text" name="password" id="modal-password" class="form-control">
                            <small class="text-muted">Leave blank to keep the current password.</small>
                        </div>
                        <div class="mb-3">
                            <label for="modal-email" class="form-label">Email</label>
                            <input type="email" name="email" id="modal-email" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="edit_user" class="btn btn-primary">Save Changes</button>
                    </div>
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

        // Populate modal with current record data
        function populateModal(data) {
            document.getElementById('modal-user-id').value = data.user_id;
            document.getElementById('modal-fullname').value = data.fullname;
            document.getElementById('modal-role').value = data.role;
            document.getElementById('modal-username').value = data.username;
            document.getElementById('modal-password').value = ''; // Leave password blank
            document.getElementById('modal-email').value = data.email;
        }

        // Toggle user status
        function toggleStatus(userId, currentStatus) {
            const formData = new FormData();
            formData.append('user_id', userId);
            formData.append('current_status', currentStatus);

            fetch('users.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    // Refresh the page to see the changes
                    location.reload();
                })
                .catch(error => console.error('Error:', error));
        }
    </script>

    <!-- search clear full list again functionality  -->
    <script>
        document.querySelector('input[name="searchInput10"]').addEventListener('input', function() {
            if (this.value === '') {
                this.form.submit();
            }
        });
    </script>

    <script>
        document.querySelectorAll('.status-toggle').forEach(function(toggle) {
            // Set initial color for Pending/NULL
            if (toggle.dataset.currentStatus !== 'Active') {
                toggle.checked = false;
            }
            toggle.addEventListener('change', function() {
                const userId = this.getAttribute('data-user-id');
                const currentStatus = this.getAttribute('data-current-status');
                fetch('', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `user_id=${userId}&current_status=${currentStatus}`
                    })
                    .then(response => response.text())
                    .then(data => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Status Updated!',
                            text: 'Account status updated successfully.',
                            confirmButtonColor: '#1abc9c'
                        }).then(() => {
                            location.reload();
                        });
                    });
            });
        });
    </script>

</body>

</html>