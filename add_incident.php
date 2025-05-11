<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'connection.php';

$user_id = (int)$_SESSION['user_id'];
$msg = '';

// file incident
if (isset($_POST['submit'])) {
    $official_id      = (int)$_POST['official_id'];
    $incident_type    = $_POST['incident_type'];
    $incident_details = $_POST['incident_details'];
    // professor style insert:
    $sql = "INSERT INTO incident_report
          (resident_id, official_id, incident_type, incident_details, date_reported)
          VALUES
          ($user_id, $official_id,
           '$incident_type',
           '$incident_details',
           NOW())";
    if ($conn->query($sql)) {
        $msg = '<div class="alert alert-success">Incident reported.</div>';
    }
}

// fetch officials for dropdown
$off = $conn->query("SELECT * FROM barangay_official");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>File Incident</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container py-5">
        <h2>File New Incident</h2>
        <?= $msg ?>
        <form method="post" class="mt-4">
            <div class="mb-3">
                <label class="form-label">Assign To Official</label>
                <select name="official_id" class="form-select">
                    <?php while ($r = $off->fetch_assoc()): ?>
                        <option value="<?= $r['official_id'] ?>">
                            <?= htmlspecialchars($r['official_name']) ?> (<?= htmlspecialchars($r['position']) ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Incident Type</label>
                <input name="incident_type" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Details</label>
                <textarea name="incident_details" class="form-control" rows="4"></textarea>
            </div>
            <button name="submit" class="btn btn-danger">Submit Report</button>
            <a href="resident_home.php" class="btn btn-secondary ms-2">Cancel</a>
        </form>
    </div>
</body>

</html>