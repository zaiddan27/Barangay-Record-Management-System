<?php
session_start();
require_once "connection.php";  // Include database connection
include "verifyemail.php";      // Include email verification script

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$alert = ""; // Add this line at the very top

// Check if form is submitted
if (isset($_POST['do_register'])) {
    // Collect user input from the form
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
    $un = $_POST['username'];  // Username
    $pw = md5($_POST['password']);  // Password (encrypted using MD5)
    $pw2 = md5($_POST['password2']);  // Confirm password (encrypted using MD5)
    $em = $_POST['email'];  // Email
    $rl = 'resident';  // Always set role as resident for registration
    $gender = $_POST['gender'];  // Gender
    $dob = $_POST['date_of_birth'];  // Date of birth
    $contact = $_POST['contact_information'];  // Contact info

    // Generate OTP and set status as 'Pending'
    $otp = rand(100000, 999999);
    $status = "Pending";

    // Check if any of the fields are empty
    if ($first_name == "" || $last_name == "" || $un == "" || $pw == "" || $pw2 == "" || $em == "" || $gender == "" || $dob == "" || $contact == "" || $house_number == "" || $street == "" || $city == "" || $state == "" || $postal_code == "") {
        echo "Registration Failed: All fields are required.";
    } elseif ($pw !== $pw2) {  // Passwords do not match
        echo "Registration Failed: Passwords do not match.";
    } else {
        // Check if the username already exists in the database
        $check_username = "SELECT * FROM `user` WHERE `username` = '$un'";
        $result_username = $conn->query($check_username);

        // Check if the email already exists in the database
        $check_email = "SELECT * FROM `user` WHERE `email` = '$em'";
        $result_email = $conn->query($check_email);

        // If username or email already exists, show appropriate message
        if ($result_username->num_rows > 0) {
            echo "Registration Failed: Username already exists. Please choose another username.";
        } elseif ($result_email->num_rows > 0) {
            echo "Registration Failed: Email already exists. Please use a different email address.";
        } else {
            // Insert the user into the user table
            $sql_user = "
                INSERT INTO `user` 
                (fullname, role, username, password, email, status, otp) 
                VALUES ('$fullname', '$rl', '$un', '$pw', '$em', '$status', '$otp')
            ";

            if ($conn->query($sql_user) === FALSE) {
                echo "Error inserting into user table: " . $conn->error;
            } else {
                // Get the user_id of the last inserted user
                $user_id = $conn->insert_id;

                // Insert into the resident table if the role is 'resident' or 'employee'
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

                // Log the registration action in the log table
                $log_action = "Added a new user: $fullname";
                $log_query = "
                    INSERT INTO `lag` (user_id, action, DateTime) 
                    VALUES ('$user_id', '$log_action', NOW())
                ";

                // Send the OTP to the user's email for verification
                if ($conn->query($log_query) === TRUE) {
                    $emailSent = send_verification($fullname, $em, $otp);
                    if ($emailSent) {
?>
                        <script>
                            Swal.fire({
                                position: "center",
                                icon: "success",
                                title: "Registered",
                                text: "Verification email has been successfully sent.",
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.href = "verifyotp.php";
                            });
                        </script>';
                    <?php

                    } else {
                    ?>
                        <script>
                            Swal.fire({
                                icon: "error",
                                title: "Email Failed!",
                                text: "There was a problem sending the verification email.",
                                showConfirmButton: true
                            });
                        </script>'; <?php
                                }
                            } else {
                                echo "Error logging action: " . $conn->error;
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
    <title>Barangay Camaya Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .was-validated .form-control:valid,
        .form-control.is-valid {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='none' stroke='green' stroke-width='2' class='bi bi-check-lg' viewBox='0 0 16 16'%3e%3cpath d='M2 8l4 4 8-8'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(1em + 0.375rem) calc(1em + 0.375rem);
        }

        .was-validated .form-control:invalid,
        .form-control.is-invalid {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='none' stroke='red' stroke-width='2' class='bi bi-x-lg' viewBox='0 0 16 16'%3e%3cpath d='M2 2l12 12M14 2L2 14'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(1em + 0.375rem) calc(1em + 0.375rem);
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

    <div class="container" style="padding-bottom:200px; margin-top:200px; position: relative; z-index: 2;">

        <div class="p-4 bg-light mt-5 shadow rounded mx-auto text-center login-margin-top">
            <h2 class="mb-4 text-center">Sign Up</h2>
            <form id="registerForm" action="register.php" method="post" novalidate>
                <!-- Full Name Fields -->
                <div class="row mb-3 g-3">
                    <div class="col-12 col-sm-6 col-md-5 has-validation position-relative">
                        <input type="text" id="first_name" name="first_name" class="form-control" placeholder="First Name" required>
                        <div id="first_name-error" class="invalid-feedback"></div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-5 has-validation">
                        <input type="text" id="last_name" name="last_name" class="form-control" placeholder="Last Name" required>
                        <div id="last_name-error" class="invalid-feedback"></div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-2 has-validation">
                        <input type="text" id="middle_name" name="middle_name" class="form-control" placeholder="MI">
                        <div id="middle_name-error" class="invalid-feedback"></div>
                    </div>
                </div>

                <!-- Email and Username -->
                <div class="row mb-3 g-3">
                    <div class="col-12 has-validation">
                        <input type="email" id="email" name="email" class="form-control" placeholder="Email" required>
                        <div id="email-error" class="invalid-feedback"></div>
                    </div>
                    <div class="col-12 has-validation">
                        <input type="text" id="username" name="username" class="form-control" placeholder="Username" required>
                        <div id="username-error" class="invalid-feedback"></div>
                    </div>
                </div>

                <!-- Password Fields -->
                <div class="row mb-3 g-3">
                    <div class="col-12 has-validation">
                        <input type="password" id="password" name="password" class="form-control" placeholder="Password" autocomplete="new-password" required>
                        <div id="password-error" class="invalid-feedback"></div>
                    </div>
                    <div class="col-12 has-validation">
                        <input type="password" id="password2" name="password2" class="form-control" placeholder="Confirm Password" autocomplete="new-password" required>
                        <div id="password2-error" class="invalid-feedback"></div>
                    </div>
                </div>

                <!-- Role and Gender -->
                <div class="row mb-3 g-3">
                    <div class="col-12 has-validation">
                        <select id="gender" name="gender" class="form-select" required>
                            <option value="" disabled selected>Select Your Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                        <div id="gender-error" class="invalid-feedback"></div>
                    </div>
                </div>

                <!-- Date of Birth and Contact Information -->
                <div class="row mb-3 g-3">
                    <div class="col-12 col-sm-6 has-validation">
                        <input type="date" id="date_of_birth" name="date_of_birth" class="form-control" placeholder="Date of Birth" required>
                        <div id="date_of_birth-error" class="invalid-feedback"></div>
                    </div>
                    <div class="col-12 col-sm-6 has-validation">
                        <input type="text" id="contact_information" name="contact_information" class="form-control" placeholder="Contact Information" required>
                        <div id="contact_information-error" class="invalid-feedback"></div>
                    </div>
                </div>

                <!-- Address Fields -->
                <div class="row mb-3 g-3">
                    <div class="col-12 col-sm-6 col-md-4 has-validation">
                        <input type="text" id="house_number" name="house_number" class="form-control" placeholder="House Number" required>
                        <div id="house_number-error" class="invalid-feedback"></div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-8 has-validation">
                        <input type="text" id="street" name="street" class="form-control" placeholder="Street and Barangay" required>
                        <div id="street-error" class="invalid-feedback"></div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-5 has-validation">
                        <input type="text" id="city" name="city" class="form-control" placeholder="City" required>
                        <div id="city-error" class="invalid-feedback"></div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-5 has-validation">
                        <input type="text" id="state" name="state" class="form-control" placeholder="Country" required>
                        <div id="state-error" class="invalid-feedback"></div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-2 has-validation">
                        <input type="text" id="postal_code" name="postal_code" class="form-control" placeholder="Postal Code" required>
                        <div id="postal_code-error" class="invalid-feedback"></div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="text-center mt-4">
                    <button type="submit" name="do_register" class="btn w-50 mb-3"
                        style="background-color:#1abc9c; border:none; color:#fff; font-weight:500;">Register</button>
                    <p>Already have an account? <a href="login.php" class="btn btn-link" style="color:#1abc9c;">Login</a></p>
                </div>
            </form>
        </div>

        <?php if (!empty($alert)) echo $alert; ?>
        <?php if (isset($emailSent) && $emailSent): ?>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


            <script>
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Registered",
                    text: "Verification email has been successfully sent.",
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = "verifyotp.php";
                });
            </script>


        <?php elseif (isset($emailSent) && !$emailSent): ?>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                Swal.fire({
                    icon: "error",
                    title: "Email Failed!",
                    text: "There was a problem sending the verification email.",
                    showConfirmButton: true
                });
            </script>
        <?php endif; ?>

        <!-- Place this script before </body> -->
        <script>
            // Helper for setting valid/invalid
            function setValid(input, errorDiv) {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
                errorDiv.innerHTML = '';
            }

            function setInvalid(input, errorDiv, message) {
                input.classList.remove('is-valid');
                input.classList.add('is-invalid');
                errorDiv.innerHTML = message;
            }

            // Validation functions for each field
            function validate_first_name() {
                var input = document.getElementById('first_name');
                var errorDiv = document.getElementById('first_name-error');
                var value = input.value.trim();
                var regex = /^[a-zA-Z- ]+$/;
                if (value === '') {
                    setInvalid(input, errorDiv, 'Please enter firstname');
                    return false;
                } else if (value.length < 2) {
                    setInvalid(input, errorDiv, 'Firstname too short');
                    return false;
                } else if (!regex.test(value)) {
                    setInvalid(input, errorDiv, 'Only letters, spaces, and hyphens allowed');
                    return false;
                }
                setValid(input, errorDiv);
                return true;
            }

            function validate_last_name() {
                var input = document.getElementById('last_name');
                var errorDiv = document.getElementById('last_name-error');
                var value = input.value.trim();
                var regex = /^[a-zA-Z- ]+$/;
                if (value === '') {
                    setInvalid(input, errorDiv, 'Please enter lastname');
                    return false;
                } else if (value.length < 2) {
                    setInvalid(input, errorDiv, 'Lastname too short');
                    return false;
                } else if (!regex.test(value)) {
                    setInvalid(input, errorDiv, 'Only letters, spaces, and hyphens allowed');
                    return false;
                }
                setValid(input, errorDiv);
                return true;
            }

            function validate_middle_name() {
                var input = document.getElementById('middle_name');
                var errorDiv = document.getElementById('middle_name-error');
                var value = input.value.trim();
                // Allow empty or single/multiple letters (no numbers/symbols)
                var regex = /^[a-zA-Z]{0,}$/;
                if (value !== '' && !regex.test(value)) {
                    setInvalid(input, errorDiv, 'Middle initial must be letters only');
                    return false;
                }
                setValid(input, errorDiv);
                return true;
            }

            function validate_email() {
                var input = document.getElementById('email');
                var errorDiv = document.getElementById('email-error');
                var value = input.value.trim();
                var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (value === '') {
                    setInvalid(input, errorDiv, 'Please enter email');
                    return false;
                } else if (!regex.test(value)) {
                    setInvalid(input, errorDiv, 'Invalid email format');
                    return false;
                }
                setValid(input, errorDiv);
                return true;
            }

            function validate_username() {
                var input = document.getElementById('username');
                var errorDiv = document.getElementById('username-error');
                var value = input.value.trim();
                if (value === '') {
                    setInvalid(input, errorDiv, 'Please enter username');
                    return false;
                } else if (value.length < 4) {
                    setInvalid(input, errorDiv, 'Username too short');
                    return false;
                }
                setValid(input, errorDiv);
                return true;
            }

            function validate_password() {
                var input = document.getElementById('password');
                var errorDiv = document.getElementById('password-error');
                var value = input.value;
                if (value === '') {
                    setInvalid(input, errorDiv, 'Please enter password');
                    return false;
                } else if (value.length < 6) {
                    setInvalid(input, errorDiv, 'Password too short');
                    return false;
                }
                setValid(input, errorDiv);
                return true;
            }

            function validate_password2() {
                var input = document.getElementById('password2');
                var errorDiv = document.getElementById('password2-error');
                var value = input.value;
                var pw = document.getElementById('password').value;
                if (value === '') {
                    setInvalid(input, errorDiv, 'Please confirm password');
                    return false;
                } else if (value !== pw) {
                    setInvalid(input, errorDiv, 'Passwords do not match');
                    return false;
                }
                setValid(input, errorDiv);
                return true;
            }

            function validate_gender() {
                var input = document.getElementById('gender');
                var errorDiv = document.getElementById('gender-error');
                if (input.value === '') {
                    setInvalid(input, errorDiv, 'Please select gender');
                    return false;
                }
                setValid(input, errorDiv);
                return true;
            }

            function validate_date_of_birth() {
                var input = document.getElementById('date_of_birth');
                var errorDiv = document.getElementById('date_of_birth-error');
                if (input.value === '') {
                    setInvalid(input, errorDiv, 'Please enter date of birth');
                    return false;
                }
                setValid(input, errorDiv);
                return true;
            }

            function validate_contact_information() {
                var input = document.getElementById('contact_information');
                var errorDiv = document.getElementById('contact_information-error');
                var value = input.value.trim();
                // Philippine mobile number: starts with 09, 11 digits total
                var regex = /^09\d{9}$/;
                if (value === '') {
                    setInvalid(input, errorDiv, 'Please enter contact information');
                    return false;
                } else if (!regex.test(value)) {
                    setInvalid(input, errorDiv, 'Enter a valid Philippine mobile number (e.g., 09XXXXXXXXX)');
                    return false;
                }
                setValid(input, errorDiv);
                return true;
            }

            function validate_house_number() {
                var input = document.getElementById('house_number');
                var errorDiv = document.getElementById('house_number-error');
                var value = input.value.trim();
                if (value === '') {
                    setInvalid(input, errorDiv, 'Please enter house number');
                    return false;
                }
                setValid(input, errorDiv);
                return true;
            }

            function validate_street() {
                var input = document.getElementById('street');
                var errorDiv = document.getElementById('street-error');
                var value = input.value.trim();
                if (value === '') {
                    setInvalid(input, errorDiv, 'Please enter street');
                    return false;
                }
                setValid(input, errorDiv);
                return true;
            }

            function validate_city() {
                var input = document.getElementById('city');
                var errorDiv = document.getElementById('city-error');
                var value = input.value.trim();
                if (value === '') {
                    setInvalid(input, errorDiv, 'Please enter city');
                    return false;
                }
                setValid(input, errorDiv);
                return true;
            }

            function validate_state() {
                var input = document.getElementById('state');
                var errorDiv = document.getElementById('state-error');
                var value = input.value.trim();
                if (value === '') {
                    setInvalid(input, errorDiv, 'Please enter country');
                    return false;
                }
                setValid(input, errorDiv);
                return true;
            }

            function validate_postal_code() {
                var input = document.getElementById('postal_code');
                var errorDiv = document.getElementById('postal_code-error');
                var value = input.value.trim();
                if (value === '') {
                    setInvalid(input, errorDiv, 'Please enter postal code');
                    return false;
                }
                setValid(input, errorDiv);
                return true;
            }

            // Attach validation on blur
            document.getElementById('first_name').onblur = validate_first_name;
            document.getElementById('last_name').onblur = validate_last_name;
            document.getElementById('middle_name').onblur = validate_middle_name;
            document.getElementById('email').onblur = validate_email;
            document.getElementById('username').onblur = validate_username;
            document.getElementById('password').onblur = validate_password;
            document.getElementById('password2').onblur = validate_password2;
            document.getElementById('gender').onblur = validate_gender;
            document.getElementById('date_of_birth').onblur = validate_date_of_birth;
            document.getElementById('contact_information').onblur = validate_contact_information;
            document.getElementById('house_number').onblur = validate_house_number;
            document.getElementById('street').onblur = validate_street;
            document.getElementById('city').onblur = validate_city;
            document.getElementById('state').onblur = validate_state;
            document.getElementById('postal_code').onblur = validate_postal_code;

            // Validate all on submit
            document.getElementById('registerForm').onsubmit = function(e) {
                var valid = true;
                if (!validate_first_name()) valid = false;
                if (!validate_last_name()) valid = false;
                if (!validate_middle_name()) valid = false;
                if (!validate_email()) valid = false;
                if (!validate_username()) valid = false;
                if (!validate_password()) valid = false;
                if (!validate_password2()) valid = false;
                if (!validate_gender()) valid = false;
                if (!validate_date_of_birth()) valid = false;
                if (!validate_contact_information()) valid = false;
                if (!validate_house_number()) valid = false;
                if (!validate_street()) valid = false;
                if (!validate_city()) valid = false;
                if (!validate_state()) valid = false;
                if (!validate_postal_code()) valid = false;

                // Force blur on all fields to trigger validation UI
                [
                    'first_name', 'last_name', 'middle_name', 'email', 'username',
                    'password', 'password2', 'gender', 'date_of_birth', 'contact_information',
                    'house_number', 'street', 'city', 'state', 'postal_code'
                ].forEach(function(id) {
                    var el = document.getElementById(id);
                    if (el) el.blur();
                });

                if (!valid) e.preventDefault();
            };
        </script>
    </div>














    <div class="footer">
        &copy; <?= date('Y') ?> Barangay Camaya. All Rights Reserved.
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>