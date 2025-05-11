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

// Handle add functionality
if (isset($_POST['add_resident'])) {
    $resident_name = str_replace("'", "''", $_POST['resident_name']); // Escape single quotes
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $contact_information = str_replace("'", "''", $_POST['contact_information']); // Escape single quotes
    $address = str_replace("'", "''", $_POST['address']); // Escape single quotes
    $user_id = !empty($_POST['user_id']) ? (int)$_POST['user_id'] : "NULL"; // Set to NULL if empty

    $insertSql = "INSERT INTO resident (resident_name, date_of_birth, gender, contact_information, address, user_id) 
                  VALUES ('$resident_name', '$date_of_birth', '$gender', '$contact_information', '$address', $user_id)";

    if ($conn->query($insertSql)) {
        $msg = '<div class="alert alert-success">Resident added successfully.</div>';
        $conn->query("INSERT INTO lag (user_id, action) VALUES ({$_SESSION['user_id']}, 'Added a new resident: $resident_name')");
    } else {
        $msg = '<div class="alert alert-danger">Failed to add resident: ' . $conn->error . '</div>';
    }
}

// Handle edit functionality
if (isset($_POST['edit_resident'])) {
    $resident_id = (int)$_POST['resident_id'];
    $resident_name = str_replace("'", "''", $_POST['resident_name']); // Escape single quotes
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $contact_information = str_replace("'", "''", $_POST['contact_information']); // Escape single quotes
    $address = str_replace("'", "''", $_POST['address']); // Escape single quotes
    $user_id = !empty($_POST['user_id']) ? (int)$_POST['user_id'] : "NULL"; // Set to NULL if empty

    $updateSql = "UPDATE resident 
                  SET resident_name = '$resident_name', 
                      date_of_birth = '$date_of_birth', 
                      gender = '$gender', 
                      contact_information = '$contact_information', 
                      address = '$address', 
                      user_id = $user_id 
                  WHERE resident_id = $resident_id";

    if ($conn->query($updateSql)) {
        $msg = '<div class="alert alert-success">Resident updated successfully.</div>';
        $conn->query("INSERT INTO lag (user_id, action) VALUES ({$_SESSION['user_id']}, 'Updated resident details for: $resident_name')");
    } else {
        $msg = '<div class="alert alert-danger">Failed to update resident: ' . $conn->error . '</div>';
    }
}

// Fetch residents with search functionality
$searchInput = $_POST['searchInput'] ?? '';
$searchInput = str_replace("'", "''", $searchInput); // Escape single quotes for search input

$selectsql = "SELECT r.*, u.fullname AS user_full_name 
              FROM resident r 
              LEFT JOIN user u ON r.user_id = u.user_id 
              WHERE r.resident_id LIKE '%$searchInput%' 
                 OR r.resident_name LIKE '%$searchInput%' 
                 OR r.date_of_birth LIKE '%$searchInput%' 
                 OR r.gender LIKE '%$searchInput%' 
                 OR r.contact_information LIKE '%$searchInput%' 
                 OR r.address LIKE '%$searchInput%' 
                 OR u.fullname LIKE '%$searchInput%'";

$result = $conn->query($selectsql);

if (!$result) {
    die("Error in SQL query: " . $conn->error . "<br>Query: " . $selectsql);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Residents</title>
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
                    <a href="resident.php" class="nav-link text-white active">
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
                <h1 class="h3 mb-4">Residents</h1>
                <?= $msg ?>
                <form action="resident.php" method="post" class="mb-4">
                    <div class="row g-3">
                        <div class="col-auto">
                            <input type="search" name="searchInput" placeholder="Search Residents" class="form-control" value="<?= htmlspecialchars($searchInput) ?>">
                        </div>
                        <div class="col-auto">
                            <input type="submit" name="btnsearch" value="Search" class="btn btn-primary">
                        </div>
                    </div>
                </form>

                <!-- Residents Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Resident ID</th>
                                <th>Resident Name</th>
                                <th>Date of Birth</th>
                                <th>Gender</th>
                                <th>Contact Information</th>
                                <th>Address</th>
                                <th>User Name</th>
                                <?php if ($userRole === 'admin' || $userRole === 'employee'): ?>
                                    <th>Actions</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $row['resident_id'] ?></td>
                                        <td><?= htmlspecialchars($row['resident_name']) ?></td>
                                        <td><?= $row['date_of_birth'] ?></td>
                                        <td><?= $row['gender'] ?></td>
                                        <td><?= htmlspecialchars($row['contact_information']) ?></td>
                                        <td><?= htmlspecialchars($row['address']) ?></td>
                                        <td><?= $row['user_full_name'] ? htmlspecialchars($row['user_full_name']) : 'No User' ?></td>
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

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="resident.php" method="post">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel">Edit Resident</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="resident_id" id="modal-resident-id">
                                    <div class="mb-3">
                                        <label for="modal-resident-name" class="form-label">Resident Name</label>
                                        <input type="text" name="resident_name" id="modal-resident-name" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="modal-date-of-birth" class="form-label">Date of Birth</label>
                                        <input type="date" name="date_of_birth" id="modal-date-of-birth" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="modal-gender" class="form-label">Gender</label>
                                        <select name="gender" id="modal-gender" class="form-select" required>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="modal-contact-information" class="form-label">Contact Information</label>
                                        <input type="text" name="contact_information" id="modal-contact-information" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="modal-address" class="form-label">Address</label>
                                        <input type="text" name="address" id="modal-address" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="modal-user-id" class="form-label">User</label>
                                        <select name="user_id" id="modal-user-id" class="form-select">
                                            <option value="" selected>No User</option>
                                            <?php
                                            $users = $conn->query("SELECT user_id, fullname FROM user");
                                            while ($user = $users->fetch_assoc()) {
                                                echo '<option value="' . $user['user_id'] . '">' . htmlspecialchars($user['fullname']) . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" name="edit_resident" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <?php if ($userRole === 'admin' || $userRole === 'employee'): ?>
                    <h2 class="h5 mt-4">Add New Resident</h2>
                    <form action="resident.php" method="post" class="row g-3">
                        <div class="col-md-6">
                            <label for="resident_name" class="form-label">Resident Name</label>
                            <input type="text" name="resident_name" class="form-control" placeholder="Resident Name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="gender" class="form-label">Gender</label>
                            <select name="gender" class="form-select" required>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="contact_information" class="form-label">Contact Information</label>
                            <input type="text" name="contact_information" class="form-control" placeholder="Contact Information" required>
                        </div>
                        <div class="col-md-6">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" name="address" class="form-control" placeholder="Address" required>
                        </div>
                        <div class="col-md-6">
                            <label for="user_id" class="form-label">User</label>
                            <select name="user_id" class="form-select">
                                <option value="" selected>No User</option>
                                <?php
                                $users = $conn->query("SELECT user_id, fullname FROM user");
                                while ($user = $users->fetch_assoc()) {
                                    echo '<option value="' . $user['user_id'] . '">' . htmlspecialchars($user['fullname']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" name="add_resident" class="btn btn-primary">Add Resident</button>
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

    <!-- Sidebar toggle script -->
    <script>
        document.getElementById('btn-toggle').addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('d-none');
        });

        // Highlight active link
        document.querySelectorAll('#sidebar .nav-link').forEach(link => {
            if (link.href === window.location.href) link.classList.add('active');
        });

        // Populate modal with current record data
        function populateModal(data) {
            document.getElementById('modal-resident-id').value = data.resident_id;
            document.getElementById('modal-resident-name').value = data.resident_name;
            document.getElementById('modal-date-of-birth').value = data.date_of_birth;
            document.getElementById('modal-gender').value = data.gender;
            document.getElementById('modal-contact-information').value = data.contact_information;
            document.getElementById('modal-address').value = data.address;
            document.getElementById('modal-user-id').value = data.user_id || '';
        }
    </script>
</body>

</html>