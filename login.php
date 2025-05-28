<?php
require_once "connection.php";
include "verifyemail.php";

$swal_script = "";

if (isset($_POST['do_login'])) {
    session_start();
    $jausername = $_POST['username'];
    $jaemail = $_POST['username'];
    $japassword = md5($_POST['password']);

    $_SESSION['username'] = $jausername;
    $_SESSION['email'] = $jaemail;
    $_SESSION['password'] = $japassword;

    $jaloginsql = "SELECT * FROM user WHERE (username = '$jausername' OR email = '$jaemail') AND password = '$japassword' AND status = 'Active'";
    $jaresult = $conn->query($jaloginsql);

    if ($jaresult->num_rows == 1) {
        $jafield = $jaresult->fetch_assoc();
        $_SESSION['user_id'] = $jafield['user_id'];
        $_SESSION['fullname'] = $jafield['fullname'];
        $_SESSION['role'] = $jafield['role'];
        $jauser_id = $_SESSION['user_id'];
        $conn->query("INSERT INTO lag (user_id, action) VALUES ($jauser_id, 'User logged in')");

        if ($jafield['role'] == "admin") {
            $swal_script = "<script>
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Admin Login successful',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = 'admin.php';
                });
            </script>";
        } else if ($jafield['role'] == "employee") {
            $swal_script = "<script>
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Employee Login successful',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = 'employee_dashboard.php';
                });
            </script>";
        } else {
            $swal_script = "<script>
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Resident Login successful',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = 'user_home.php';
                });
            </script>";
        }
    } else {
        $swal_script = "<script>
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: 'Incorrect username or password',
                showConfirmButton: false,
                timer: 1500
            });
        </script>";
    }
}
?>



<!-- login.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Camaya Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: url('images/portalbgp.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            margin: 0;
            position: relative;
        }

        /* Dim overlay for better text contrast */
        .bg-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(9, 41, 34, 0.7);
            z-index: 0;
        }

        .navbar-custom {
            background-color: #092922 !important;
            padding: 1.5rem 0;
        }

        .navbar-brand,
        .navbar-nav .nav-link {
            color: #fff !important;
            font-weight: 500;
            letter-spacing: 1px;
        }

        .portal-title {
            font-size: 2.7rem;
            font-weight: bold;
            color: #1abc9c;
            margin-bottom: 18px;
            letter-spacing: 1px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.18);
        }

        .portal-subtitle {
            font-size: 1.25rem;
            color: #fff;
            margin-bottom: 30px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.18);
        }

        .login-card {
            background-color: rgba(9, 41, 34, 0.95);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .footer {
            background-color: #092922;
            width: 100%;
            text-align: center;
            color: #fff;
            font-size: 0.9rem;
            position: fixed;
            left: 0;
            bottom: 0;
            padding: 1rem 0;
            margin: 0;
            z-index: 1030;
        }

        .main-content-spacing {
            margin-top: 250px;
        }
    </style>
</head>

<body>
    <div class="bg-overlay"></div>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top shadow">
        <div class="container">
            <a class="navbar-brand" href="#">Barangay Camaya</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="portal.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>



    <!-- Outer container with padding-bottom and margin-top -->
    <div class="container" style="padding-bottom:200px; margin-top:200px; position: relative; z-index: 2;">
        <!-- Responsive Login Form (smaller width on desktop) -->
        <div class="p-4 bg-light mt-5 shadow rounded mx-auto text-center login-margin-top"
            style="max-width:400px; position: relative; z-index: 2;">
            <h2 class="mb-4">Login</h2>
            <form action="login.php" method="post">
                <!-- Username or Email -->
                <div class="mb-3 text-start">
                    <label for="username" class="form-label">Username or Email</label>
                    <input type="text" name="username" id="username" class="form-control"
                        placeholder="Enter your username or email" required>
                </div>
                <!-- Password -->
                <div class="mb-3 text-start">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control"
                        placeholder="Enter your password" required>
                </div>
                <!-- Submit Button -->
                <div>
                    <button type="submit" name="do_login" class="btn w-100 mb-3"
                        style="background-color:#1abc9c; border:none; color:#fff; font-weight:500;">Login</button>
                    <p>
                        Don't have an account? <a href="register.php" class="btn btn-link" style="color:#1abc9c;">Sign Up</a>
                    </p>
                </div>
            </form>
        </div>
    </div>




    <div class="footer">
        &copy; <?= date('Y') ?> Barangay Camaya. All Rights Reserved.
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?= $swal_script ?>
</body>

</html>