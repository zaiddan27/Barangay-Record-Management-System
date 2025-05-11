<!-- login.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container p-5 bg-light mt-5 w-50 shadow rounded">
        <h2 class="mb-4 text-center">Login</h2>
        <form action="login.php" method="post">
            <!-- Username or Email -->
            <div class="mb-3">
                <label for="username" class="form-label">Username or Email</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Enter your username or email" required>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
            </div>

            <!-- Submit Button -->
            <div class="text-center">
                <button type="submit" name="do_login" class="btn btn-primary w-100 mb-3">Login</button>
                <p>
                    Don't have an account? <a href="register.php" class="btn btn-link">Sign Up</a>
                </p>
            </div>
        </form>
    </div>
</body>

</html>

<?php
require_once "connection.php";

//button function
if (isset($_POST['do_login'])) {
    //session start
    session_start();

    //user inp
    $username = $_POST['username'];
    $email = $_POST['username']; // using the same field for both email and username
    $password = md5($_POST['password']); //encryption password

    //declare value
    $_SESSION['username'] = $username; //session username
    $_SESSION['email'] = $email; //session email
    $_SESSION['password'] = $password; //session password

    $loginsql = "SELECT * FROM user WHERE (username = '" . $username . "' OR email = '" . $email . "') AND password = '" . $password . "'";
    $result = $conn->query($loginsql);

    // Check if login credentials are correct
    if ($result->num_rows == 1) {
        $field = $result->fetch_assoc();

        // User role and fullname
        $_SESSION['user_id'] = $field['user_id'];
        $_SESSION['fullname'] = $field['fullname'];
        $_SESSION['role'] = $field['role'];

        // Log the login action
        $user_id = $_SESSION['user_id'];
        $conn->query("INSERT INTO lag (user_id, action) VALUES ($user_id, 'User logged in')");

        // Redirect based on user role
        if ($field['role'] == "admin") {
            echo '<script>
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Admin Login successful",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = "admin.php";
                    });
                  </script>';
        } else if ($field['role'] == "employee") {
            echo '<script>
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Employee Login successful",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = "employee_dashboard.php";
                    });
                  </script>';
        } else {
            echo '<script>
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Resident Login successful",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = "user_home.php";
                    });
                  </script>';
        }
    } else {
        echo '<script>
                Swal.fire({
                    position: "center",
                    icon: "error",
                    title: "Incorrect username or password",
                    showConfirmButton: false,
                    timer: 1500
                });
              </script>';
    }
}
?>