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
if (isset($_POST['edit_household'])) {
    $household_id = (int)$_POST['household_id'];
    $household_head = str_replace("'", "''", $_POST['household_head']); // Escape single quotes
    $address = str_replace("'", "''", $_POST['address']); // Escape single quotes
    $contact_information = str_replace("'", "''", $_POST['contact_information']); // Escape single quotes
    $number_of_members = (int)$_POST['number_of_members'];

    $updateSql = "UPDATE household 
                  SET household_head = '$household_head', 
                      address = '$address', 
                      contact_information = '$contact_information', 
                      number_of_members = $number_of_members 
                  WHERE household_id = $household_id";

    if ($conn->query($updateSql)) {
        $msg = '<div class="alert alert-success">Household updated successfully.</div>';
    } else {
        $msg = '<div class="alert alert-danger">Failed to update household: ' . $conn->error . '</div>';
    }
}

// Handle add functionality
if (isset($_POST['add_household'])) {
    $household_head = str_replace("'", "''", $_POST['household_head']);
    $address = str_replace("'", "''", $_POST['address']);
    $contact_information = str_replace("'", "''", $_POST['contact_information']);
    $number_of_members = (int)$_POST['number_of_members'];

    $insertSql = "INSERT INTO household (household_head, address, contact_information, number_of_members) 
                  VALUES ('$household_head', '$address', '$contact_information', $number_of_members)";

    if ($conn->query($insertSql)) {
        $msg = '<div class="alert alert-success">Household added successfully.</div>';
    } else {
        $msg = '<div class="alert alert-danger">Failed to add household: ' . $conn->error . '</div>';
    }
}

// Fetch households with search functionality
$searchInput02 = $_POST['searchInput02'] ?? '';
$selectsql = "SELECT * FROM household 
              WHERE household_id LIKE '%$searchInput02%' 
                 OR household_head LIKE '%$searchInput02%' 
                 OR address LIKE '%$searchInput02%' 
                 OR contact_information LIKE '%$searchInput02%' 
                 OR number_of_members LIKE '%$searchInput02%'";

$result = $conn->query($selectsql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Households</title>
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
                <h1 class="h3 mb-4">Households</h1>
                <?= $msg ?>
                <form action="household.php" method="post" class="mb-4">
                    <div class="row g-3">
                        <div class="col-auto">
                            <input type="search" name="searchInput02" placeholder="Search Households" class="form-control" value="<?= $searchInput02 ?>">
                        </div>
                        <div class="col-auto">
                            <input type="submit" name="btnsearch02" value="Search" class="btn btn-primary">
                        </div>
                    </div>
                </form>

                <!-- Households Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Household ID</th>
                                <th>Household Head</th>
                                <th>Address</th>
                                <th>Contact Information</th>
                                <th>Number of Members</th>
                                <?php if ($userRole === 'admin' || $userRole === 'employee'): ?>
                                    <th>Actions</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $row['household_id'] ?></td>
                                        <td><?= htmlspecialchars($row['household_head']) ?></td>
                                        <td><?= htmlspecialchars($row['address']) ?></td>
                                        <td><?= htmlspecialchars($row['contact_information']) ?></td>
                                        <td><?= $row['number_of_members'] ?></td>
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
                                    <td colspan="6" class="text-center">No records found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="household.php" method="post">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel">Edit Household</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="household_id" id="modal-household-id">
                                    <div class="mb-3">
                                        <label for="modal-household-head" class="form-label">Household Head</label>
                                        <input type="text" name="household_head" id="modal-household-head" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="modal-address" class="form-label">Address</label>
                                        <input type="text" name="address" id="modal-address" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="modal-contact-information" class="form-label">Contact Information</label>
                                        <input type="text" name="contact_information" id="modal-contact-information" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="modal-number-of-members" class="form-label">Number of Members</label>
                                        <input type="number" name="number_of_members" id="modal-number-of-members" class="form-control" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" name="edit_household" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <script>
                    // Populate modal with current record data
                    function populateModal(data) {
                        document.getElementById('modal-household-id').value = data.household_id;
                        document.getElementById('modal-household-head').value = data.household_head;
                        document.getElementById('modal-address').value = data.address;
                        document.getElementById('modal-contact-information').value = data.contact_information;
                        document.getElementById('modal-number-of-members').value = data.number_of_members;
                    }

                    //   search clear full list again functionality  

                    document.querySelector('input[name="searchInput02"]').addEventListener('input', function() {
                        if (this.value === '') {
                            this.form.submit();
                        }
                    });
                </script>

                <?php if ($userRole === 'admin' || $userRole === 'employee'): ?>
                    <h2 class="h5 mt-4">Add New Household</h2>
                    <form action="household.php" method="post" class="row g-3">
                        <div class="col-md-6">
                            <label for="household_head" class="form-label">Household Head</label>
                            <input type="text" name="household_head" class="form-control" placeholder="Household Head" required>
                        </div>
                        <div class="col-md-6">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" name="address" class="form-control" placeholder="Address" required>
                        </div>
                        <div class="col-md-6">
                            <label for="contact_information" class="form-label">Contact Information</label>
                            <input type="text" name="contact_information" class="form-control" placeholder="Contact Information" required>
                        </div>
                        <div class="col-md-6">
                            <label for="number_of_members" class="form-label">Number of Members</label>
                            <input type="number" name="number_of_members" class="form-control" placeholder="Number of Members" required>
                        </div>
                        <div class="col-12">
                            <button type="submit" name="add_household" class="btn btn-primary">Add Household</button>
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

    <!-- for nav toggle -->
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