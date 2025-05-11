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
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        .portal-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }

        .portal-title {
            font-size: 2.5rem;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 20px;
        }

        .portal-subtitle {
            font-size: 1.2rem;
            color: #555;
            margin-bottom: 30px;
        }

        .portal-buttons .btn {
            width: 100%;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }

        .footer {
            position: absolute;
            bottom: 10px;
            width: 100%;
            text-align: center;
            color: #fff;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <div class="portal-container">
        <h1 class="portal-title">Welcome to Barangay Camaya</h1>
        <p class="portal-subtitle">Your gateway to community services and information.</p>
        <div class="portal-buttons">
            <a href="login.php" class="btn btn-primary">Login</a>
            <a href="register.php" class="btn btn-outline-primary">Sign Up</a>
        </div>
    </div>

    <div class="footer">
        &copy; <?= date('Y') ?> Barangay Camaya. All Rights Reserved.
    </div>
</body>

</html>