<?php
session_start();
// Only residents may edit their profile
if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'resident') {
    header('Location: login.php');
    exit;
}
require_once 'connection.php';

$user_id = (int)$_SESSION['user_id'];
$msg = '';

// Handle form submission
if (isset($_POST['update'])) {
    var_dump($_POST); // Debugging: Check the submitted form data
    $name    = $_POST['resident_name'];
    $dob     = $_POST['date_of_birth'];
    $gender  = $_POST['gender'];
    $contact = $_POST['contact_information'];
    $address = $_POST['address'];
    $email   = $_POST['email'];

    // Update the resident table
    $sql_resident = "UPDATE resident
                     SET resident_name='$name',
                         date_of_birth='$dob',
                         gender='$gender',
                         contact_information='$contact',
                         address='$address'
                     WHERE user_id=$user_id";

    if ($conn->query($sql_resident)) {
        // Update the user table to reflect the updated fullname and email
        $sql_user = "UPDATE user
                     SET fullname='$name',
                         email='$email'
                     WHERE user_id=$user_id";

        if ($conn->query($sql_user)) {
            $msg = '<div class="alert alert-success">Profile updated.</div>';
            $_SESSION['fullname'] = $name; // Update session name

            // Reload the updated data
            $res = $conn->query("SELECT r.*, u.email FROM resident r JOIN user u ON r.user_id = u.user_id WHERE r.user_id=$user_id");
            if ($res && $res->num_rows > 0) {
                $row = $res->fetch_assoc();
                var_dump($row); // Debugging: Check the fetched data
            }
        } else {
            $msg = '<div class="alert alert-danger">Failed to update user table: ' . $conn->error . '</div>';
        }
    } else {
        $msg = '<div class="alert alert-danger">Failed to update resident table: ' . $conn->error . '</div>';
        echo "Error updating resident table: " . $conn->error; // Debugging: Output the error
    }
}

// Fetch current data
if (!isset($row)) {
    $res = $conn->query("SELECT r.*, u.email FROM resident r JOIN user u ON r.user_id = u.user_id WHERE r.user_id=$user_id");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
    } else {
        $row = [
            'resident_name' => '',
            'date_of_birth' => '',
            'gender' => '',
            'contact_information' => '',
            'address' => '',
            'email' => '' // Fallback for email
        ];
        $msg = '<div class="alert alert-danger">No resident data found.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="mystyle.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 -->
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

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .card:hover {
            transform: scale(1.02);
        }

        #page-content {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body>
    <!-- Navbar -->
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
            <div class="mb-4"><span class="fs-4">Barangay Camaya</span></div>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="user_home.php" class="nav-link text-white">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="edit_profile.php" class="nav-link text-white active">
                        <i class="bi bi-person-circle me-2"></i> My Profile
                    </a>
                </li>
                <li class="nav-item">
                    <a href="file_incidents.php" class="nav-link text-white">
                        <i class="bi bi-exclamation-triangle me-2"></i> File Incident
                    </a>
                </li>
                <li class="nav-item">
                    <a href="view_projects.php" class="nav-link text-white">
                        <i class="bi bi-building me-2"></i> View Projects
                    </a>
                </li>
                <li class="nav-item">
                    <a href="view_incidents.php" class="nav-link text-white">
                        <i class="bi bi-eye me-2"></i> View Incidents
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
                <h1 class="h3 mb-4">Edit My Profile</h1>
                <?= $msg ?>
                <form id="editProfileForm" method="post">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input name="resident_name" class="form-control" value="<?= $row['resident_name'] ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control" value="<?= $row['date_of_birth'] ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select">
                            <option <?= $row['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                            <option <?= $row['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact Information</label>
                        <input name="contact_information" class="form-control" value="<?= $row['contact_information'] ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <input name="address" class="form-control" value="<?= $row['address'] ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input name="email" type="email" class="form-control" value="<?= $row['email'] ?>">
                    </div>
                    <button type="submit" id="hiddenSubmitButton" style="display: none;" name="update">Submit</button>
                    <button type="button" id="saveChangesButton" class="btn btn-primary">Save Changes</button>
                    <a href="user_home.php" class="btn btn-secondary ms-2 bi bi-arrow-left">Back</a>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('btn-toggle')
            .addEventListener('click', () =>
                document.getElementById('sidebar').classList.toggle('d-none')
            );

        // SweetAlert2 integration for Save Changes button
        document.getElementById('saveChangesButton').addEventListener('click', function() {
            Swal.fire({
                title: "Do you want to save the changes?",
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: "Save",
                denyButtonText: `Don't save`
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire("Saved!", "", "success");
                    document.getElementById('hiddenSubmitButton').click(); // Trigger the hidden submit button
                } else if (result.isDenied) {
                    Swal.fire("Changes are not saved", "", "info");
                }
            });
        });
    </script>
</body>

</html>