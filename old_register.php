<?php
session_start();
require_once "connection.php";
// SESSION["MSG"]
// SESSION END 
// $msg = "";

if (isset($_POST['do_register'])) {
    $fn = $_POST['fullname'];
    $un = $_POST['username'];
    $pw = md5($_POST['password']);
    $pw2 = md5($_POST['password2']);
    $em = $_POST['email'];
    $rl = $_POST['role']; // "resident" or "employee"
    $gender = $_POST['gender'];
    $dob = $_POST['date_of_birth'];
    $contact = $_POST['contact_information'];
    $address = $_POST['address'];

    // simple empty‑field check
    if ($fn == "" || $un == "" || $pw == "" || $pw2 == "" || $em == "" || $gender == "" || $dob == "" || $contact == "" || $address == "") {
        $msg = "<div class='alert alert-danger'>All fields are required.</div>";
    } elseif ($pw !== $pw2) {
        $msg = "<div class='alert alert-danger'>Passwords do not match.</div>";
    } else {
        // Insert into user table
        $sql_user = "
            INSERT INTO `user` 
            (fullname, role, username, password, email) 
            VALUES ('$fn', '$rl', '$un', '$pw', '$em')
        ";

        if ($conn->query($sql_user)) {
            // Get the user_id of the last inserted user
            $user_id = $conn->insert_id;

            // Insert into the resident table if the user role is 'resident'
            if ($rl == 'resident') {
                $sql_resident = "
                    INSERT INTO `resident` 
                    (resident_name, gender, date_of_birth, contact_information, address) 
                    VALUES ('$fn', '$gender', '$dob', '$contact', '$address')
                ";
                $conn->query($sql_resident);
            }

            $msg = "<div class='alert alert-success'>Registration successful. <a href='login.php'>Login here</a>.</div>";
        } else {
            $msg = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
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
</head>

<body>
    <div class="container p-5 bg-light mt-5 w-50">
        <h2 class="mb-4">Sign Up</h2>
        <?= $msg ?>
        <form action="register.php" method="post">
            <div class="mb-3">
                <label>Full Name</label>
                <input type="text" name="fullname" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="text" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Confirm Password</label>
                <input type="password" name="password2" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Role</label>
                <select name="role" class="form-select" required>
                    <option value="resident">Resident</option>
                    <option value="employee">Employee</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Gender</label>
                <select name="gender" class="form-select" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Date of Birth</label>
                <input type="date" name="date_of_birth" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Contact Information</label>
                <input type="text" name="contact_information" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Address</label>
                <input type="text" name="address" class="form-control" required>
            </div>
            <button type="submit" name="do_register" class="btn btn-primary">Register</button>
            <a href="login.php" class="btn btn-link">Back to Login</a>
        </form>
    </div>
</body>

</html>