<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>


<body>
    <?php
    session_start();
    //declare values
    $uname = $_SESSION['username']; //session username
    $fullname = $_SESSION['fullname']; //session fullname

    ?>
    <form action=""></form>

    <h1>Welcome</h1>
    <h2>Employee Dashboard</h2>
    <h3>Employee Name: <?php echo $uname ?></h3>
</body>

</html>