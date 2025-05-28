<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container mt-5 w-25 border border-primary rounded p-5">
        <div class="row mb-5">
            <div class="col text-center fw-bold">
                <span class="display-4 text-primary">OTP Verification</span>
            </div>
        </div>
        <div class="row my-3">
            <div class="col text-center fw-bold">
                <span class="text-primary h6">One time password (OTP) was sent to your email</span>
            </div>
        </div>

        <form action="verifyotp.php" method="post">
            <div class="form-outline mb-4">
                <label class="form-label" for="form2Example1">Enter the OTP Number to verify</label>
                <input type="text" name="otp" id="form2Example1" class="form-control" required />
            </div>
            <input type="submit" name="sub" value="Verify" class="btn btn-primary btn-block w-100 mb-4">
        </form>
    </div>
</body>

</html>

<?php
require_once "connection.php";

if (isset($_POST['sub'])) {
    $jaotp_input = $_POST['otp'];

    // Adjust table and column names as per your schema
    $jasql = "SELECT * FROM user WHERE otp = '$jaotp_input'";
    $jaresult = $conn->query($jasql);

    if ($jaresult->num_rows == 1) {
        $jaupdate_sql = "UPDATE user SET status = 'Active', otp = NULL WHERE otp = '$jaotp_input'";
        $conn->query($jaupdate_sql);
?>
        <script>
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Account Activated",
                showConfirmButton: false,
                timer: 3000
            }).then(() => {
                window.location.href = "login.php";
            });
        </script>
    <?php
    } elseif (isset($_POST['sub'])) {
        // invalid otp
    ?>
        <script>
            Swal.fire({
                position: "center",
                icon: "error",
                title: "Invalid OTP Number",
                showConfirmButton: false,
                timer: 3000
            });
        </script>
<?php
    }
}
?>