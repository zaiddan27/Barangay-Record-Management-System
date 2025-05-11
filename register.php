<?php
session_start();
require_once "connection.php";

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST['do_register'])) {
    // Full name fields
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $fullname = $first_name . " " . $middle_name . " " . $last_name;

    // Address fields
    $house_number = $_POST['house_number'];
    $street = $_POST['street'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $postal_code = $_POST['postal_code'];
    $address = $house_number . " " . $street . ", " . $city . ", " . $state . " " . $postal_code;

    // Other fields
    $un = $_POST['username'];
    $pw = md5($_POST['password']);
    $pw2 = md5($_POST['password2']);
    $em = $_POST['email'];
    $rl = $_POST['role'];
    $gender = $_POST['gender'];
    $dob = $_POST['date_of_birth'];
    $contact = $_POST['contact_information'];

    // Simple empty-field check
    if ($first_name == "" || $last_name == "" || $un == "" || $pw == "" || $pw2 == "" || $em == "" || $gender == "" || $dob == "" || $contact == "" || $house_number == "" || $street == "" || $city == "" || $state == "" || $postal_code == "") {
        echo "Registration Failed: All fields are required.";
    } elseif ($pw !== $pw2) {
        echo "Registration Failed: Passwords do not match.";
    } else {
        // Check if username already exists
        $check_username = "SELECT * FROM `user` WHERE `username` = '$un'";
        $result_username = $conn->query($check_username);

        // Check if email already exists
        $check_email = "SELECT * FROM `user` WHERE `email` = '$em'";
        $result_email = $conn->query($check_email);

        if ($result_username->num_rows > 0) {
            echo "Registration Failed: Username already exists. Please choose another username.";
        } elseif ($result_email->num_rows > 0) {
            echo "Registration Failed: Email already exists. Please use a different email address.";
        } else {
            // Insert into user table
            $sql_user = "
                INSERT INTO `user` 
                (fullname, role, username, password, email) 
                VALUES ('$fullname', '$rl', '$un', '$pw', '$em')
            ";

            if ($conn->query($sql_user) === FALSE) {
                echo "Error inserting into user table: " . $conn->error;
            } else {
                // Get the user_id of the last inserted user
                $user_id = $conn->insert_id;

                // Insert into the resident table if the user role is 'resident' or 'employee'
                if ($rl == 'resident' || $rl == 'employee') {
                    $sql_resident = "
                        INSERT INTO `resident` 
                        (resident_name, gender, date_of_birth, contact_information, address, user_id) 
                        VALUES ('$fullname', '$gender', '$dob', '$contact', '$address', '$user_id')
                    ";

                    if ($conn->query($sql_resident) === FALSE) {
                        echo "Error in resident insertion: " . $conn->error;
                    }
                }

                // Log the registration action in the lag table
                $log_action = "Added a new user: $fullname";
                $log_query = "
                    INSERT INTO `lag` (user_id, action, DateTime) 
                    VALUES ('$user_id', '$log_action', NOW())
                ";

                if ($conn->query($log_query) === FALSE) {
                    echo "Error logging action: " . $conn->error;
                } else {
                    echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sign Up Successful',
                                text: 'You can now log in to your account.',
                                confirmButtonText: 'OK',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                },
                                buttonsStyling: false
                            }).then(() => {
                                window.location.href = 'login.php';
                            });
                        });
                    </script>";
                }
            }
        }
    }
}
?>






<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container p-5 bg-light mt-5 w-50 shadow rounded mb-3">
        <h2 class="mb-4 text-center">Sign Up</h2>
        <form id="registerForm" action="register.php" method="post">

            <!-- Full Name Fields -->
            <div class="row mb-3">
                <div class="col-md-5">
                    <input type="text" name="first_name" class="form-control" placeholder="First Name" required>
                </div>
                <div class="col-md-5">
                    <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
                </div>
                <div class="col-md-2">
                    <input type="text" name="middle_name" class="form-control" placeholder="MI">
                </div>
            </div>

            <!-- Email and Username -->
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="mb-3">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>

            <!-- Password Fields -->
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" autocomplete="new-password" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password2" class="form-control" placeholder="Confirm Password" autocomplete="new-password" required>
            </div>

            <!-- Role and Gender -->
            <div class="mb-3">
                <select name="role" class="form-select" required>
                    <option value="" disabled selected>Select Your Role</option>
                    <option value="resident">Resident</option>
                    <option value="employee">Employee</option>
                </select>
            </div>
            <div class="mb-3">
                <select name="gender" class="form-select" required>
                    <option value="" disabled selected>Select Your Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>

            <!-- Date of Birth and Contact Information -->
            <div class="mb-3">
                <input type="date" name="date_of_birth" class="form-control" placeholder="Date of Birth" required>
            </div>
            <div class="mb-3">
                <input type="text" name="contact_information" class="form-control" placeholder="Contact Information" required>
            </div>

            <!-- Address Fields -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" name="house_number" id="house_number" class="form-control" placeholder="House Number" required>
                </div>
                <div class="col-md-8">
                    <input type="text" name="street" id="street" class="form-control" placeholder="Street and Barangay" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-5">
                    <input type="text" name="city" id="city" class="form-control" placeholder="City" required>
                </div>
                <div class="col-md-5">
                    <input type="text" name="state" id="state" class="form-control" placeholder="Country" required>
                </div>
                <div class="col-md-2">
                    <input type="text" name="postal_code" id="postal_code" class="form-control" placeholder="Postal Code" required>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center">
                <button type="submit" name="do_register" class="btn btn-primary mb-3">Register</button>
                <p>
                    Already have an account? <a href="login.php" class="btn btn-link">Login</a>
                </p>
            </div>
        </form>
    </div>
</body>

</html>