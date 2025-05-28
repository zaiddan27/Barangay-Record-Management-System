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
                    <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content Row, separated columns -->
    <div class="container position-relative main-content-spacing" style="z-index:1;">
        <div class="row">
            <!-- Left: Title and Subtitle (no bg) -->
            <div class="col-md-6 mb-4 mb-md-0 d-flex align-items-center">
                <div class="w-100 p-4">
                    <h1 class="portal-title mb-3">Barangay Camaya</h1>
                    <p class="portal-subtitle">
                        Barangay Camaya is a vibrant community dedicated to providing efficient, transparent, and responsive public service.<br>
                        We strive for unity, safety, and sustainable development for all residents.
                    </p>
                </div>
            </div>
            <!-- Right: Login & Sign Up -->
            <div class="col-md-6 d-flex align-items-center justify-content-center">
                <div class="p-4 rounded login-card" style="max-width:370px; width:100%;">
                    <h5 class="mb-4 text-white">Access Portal</h5>
                    <a href="login.php" class="btn btn-primary w-100 mb-3"
                        style="background-color:#1abc9c; border:none;">Login</a>
                    <a href="register.php" class="btn btn-outline-light w-100"
                        style="border:2px solid #1abc9c;">Sign Up</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Infrastructure Projects Section -->
    <?php
    require_once "connection.php";
    $infra_sql = "SELECT * FROM infrastructure_projects ORDER BY start_date DESC LIMIT 4";
    $infra_result = $conn->query($infra_sql);

    $projects = [];
    if ($infra_result && $infra_result->num_rows > 0) {
        while ($proj = $infra_result->fetch_assoc()) {
            $projects[] = $proj;
        }
    }
    ?>
    <div class="container position-relative" style="z-index:1; margin-top: 250px;">
        <div class="p-4 rounded" style="background:rgba(9,41,34,0.85); max-width:1100px; margin:0 auto;">
            <h3 class="text-center mb-4" style="color:#1abc9c;">Recent Infrastructure Projects</h3>
            <?php if (count($projects) > 0): ?>
                <!-- Highlight the most recent project as a clean card -->
                <div class="row justify-content-center align-items-center mb-4" style="background:none; border-radius:14px; box-shadow:0 2px 12px rgba(0,0,0,0.08); overflow:hidden;">
                    <div class="col-md-5 d-flex justify-content-end" style="padding: 1.5rem 1rem 1.5rem 1.5rem;">
                        <div class="text-end" style="max-width: 100%;">
                            <h4 class="text-white mb-2"><?= htmlspecialchars($projects[0]['project_name']) ?></h4>
                            <p class="text-white-50 mb-3"><?= htmlspecialchars($projects[0]['project_description']) ?></p>
                            <div class="mb-2 text-white-50" style="font-size:1rem;">
                                <?= date('M d, Y', strtotime($projects[0]['start_date'])) ?>
                                -
                                <?= date('M d, Y', strtotime($projects[0]['end_date'])) ?>
                            </div>
                            <span class="badge" style="background-color:#1abc9c; color:#fff; font-size:1rem; padding:.6em 1.2em;">
                                <?= htmlspecialchars($projects[0]['project_status']) ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex justify-content-start" style="padding: 1.5rem 1.5rem 1.5rem 0;">
                        <img src="<?= htmlspecialchars($projects[0]['img_path']) ?>" alt="Project Image"
                            class="rounded shadow-sm" style="width:100%; max-width:280px; height:190px; object-fit:cover; background:#eee;">
                    </div>
                </div>
                <!-- Next three projects as small cards -->
                <div class="row justify-content-center">
                    <?php for ($i = 1; $i < count($projects); $i++): ?>
                        <div class="col-md-4 mb-4 text-center">
                            <img src="<?= htmlspecialchars($projects[$i]['img_path']) ?>" alt="Project Image"
                                class="rounded mb-2" style="width:100%; max-width:180px; height:120px; object-fit:cover; background:#eee;">
                            <h5 class="text-white mb-1"><?= htmlspecialchars($projects[$i]['project_name']) ?></h5>
                            <p class="text-white-50 mb-1" style="min-height:40px;"><?= htmlspecialchars($projects[$i]['project_description']) ?></p>
                            <small class="text-white-50">
                                <?= date('M d, Y', strtotime($projects[$i]['start_date'])) ?>
                                -
                                <?= date('M d, Y', strtotime($projects[$i]['end_date'])) ?>
                                <br>
                                <span class="badge" style="background-color:#1abc9c; color:#fff;">
                                    <?= htmlspecialchars($projects[$i]['project_status']) ?>
                                </span>
                            </small>
                        </div>
                    <?php endfor; ?>
                </div>
            <?php else: ?>
                <div class="col-12 text-center text-white-50">No infrastructure projects found.</div>
            <?php endif; ?>
        </div>
    </div>
    <!-- End Infrastructure Projects Section -->

    <!-- Mission and Vision Section moved further down -->
    <div class="container position-relative" style="z-index:1; margin-top: 120px;">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="p-4 rounded" style="background:rgba(9,41,34,0.85); max-width:900px; margin:0 auto;">
                    <h3 class="text-center mb-4" style="color:#1abc9c;">Mission & Vision</h3>
                    <div class="row">
                        <div class="col-md-6 mb-4 mb-md-0">
                            <h5 class="text-white fw-bold mb-2 text-center text-md-start">Mission</h5>
                            <p class="text-white text-justify" style="text-align: justify;">
                                Our mission is to foster a safe, inclusive, and progressive community by delivering transparent, efficient, and compassionate public service to every resident of Barangay Camaya. We are committed to empowering our citizens through accessible programs, sustainable initiatives, and active participation in local governance. By upholding integrity, accountability, and unity, we strive to create an environment where every individual can thrive and contribute to the collective well-being of our barangay.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-white fw-bold mb-2 text-center text-md-start">Vision</h5>
                            <p class="text-white text-justify" style="text-align: justify;">
                                We envision Barangay Camaya as a model community recognized for its strong sense of solidarity, dynamic leadership, and commitment to sustainable development. Our vision is to be a place where residents enjoy a high quality of life, empowered by responsive governance, robust social services, and a harmonious relationship with the environment. Together, we aim to build a future where progress and tradition go hand in hand, ensuring prosperity and peace for generations to come.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leadership/Hierarchy Section -->
    <div class="container position-relative" style="z-index:1; margin-top: 60px; padding-bottom: 150px;">
        <div class="p-4 rounded" style="background:rgba(9,41,34,0.85); max-width:900px; margin:0 auto;">
            <h3 class="text-center mb-4" style="color:#1abc9c;">Barangay Leadership</h3>
            <div class="row justify-content-center">
                <div class="col-md-4 mb-4 text-center">
                    <img src="images/c.png" alt="Barangay Captain" class="rounded-circle mb-2"
                        style="width:100px; height:100px; object-fit:cover;">
                    <h5 class="text-white mb-1">Juan Dela Cruz</h5>
                    <p class="text-white-50 mb-0">Barangay Captain</p>
                </div>
                <div class="col-md-4 mb-4 text-center">
                    <img src="images/a.png" alt="Kagawad" class="rounded-circle mb-2"
                        style="width:100px; height:100px; object-fit:cover;">
                    <h5 class="text-white mb-1">Maria Santos</h5>
                    <p class="text-white-50 mb-0">Kagawad</p>
                </div>
                <!-- Add more leaders as needed -->
            </div>
            <div class="row justify-content-center">
                <div class="col-md-4 mb-4 text-center">
                    <img src="images/b.png" alt="SK Chairman" class="rounded-circle mb-2"
                        style="width:100px; height:100px; object-fit:cover;">
                    <h5 class="text-white mb-1">Pedro Reyes</h5>
                    <p class="text-white-50 mb-0">SK Chairman</p>
                </div>
                <!-- Add more as needed -->
            </div>
        </div>
    </div>

    <div class="footer">
        &copy; <?= date('Y') ?> Barangay Camaya. All Rights Reserved.
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>