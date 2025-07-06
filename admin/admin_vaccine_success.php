<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define paths to includes
$header_path = __DIR__ . '/../includes/header.php';
$footer_path = __DIR__ . '/../includes/footer.php';

// Verify include files exist
if (!file_exists($header_path)) die("Error: Missing header file at " . $header_path);
if (!file_exists($footer_path)) die("Error: Missing footer file at " . $footer_path);

// Check for required session data
if (!isset($_SESSION['success_message'])) {
    header("Location: admin_add_vaccination.php");
    exit;
}

// Get data from session/URL
$vaccine_id = $_GET['id'] ?? '';
$success_message = $_SESSION['success_message'];
unset($_SESSION['success_message']);

// Include header
include $header_path;
?>

<!-- Your HTML content here -->

<?php
// Include footer
include $footer_path;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Include the same head content as admin_add_vaccination.php -->
    <title>Vaccine Added Successfully | Department of Animal Husbandry & Veterinary Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Include the same header/navigation as admin_add_vaccination.php -->

    <div class="container py-4">
        <div class="alert alert-success text-center">
            <h4><?= htmlspecialchars($success_message) ?></h4>
            <p>Vaccine ID: <?= htmlspecialchars($vaccine_id) ?></p>
            <div class="mt-3">
                <a href="admin_add_vaccination.php" class="btn btn-primary">Add Another Vaccine</a>
                <a href="admin.php" class="btn btn-secondary">Return to Dashboard</a>
            </div>
        </div>
    </div>

    <!-- Include the same footer as admin_add_vaccination.php -->
<?php include __DIR__ . '/../includes/footer.php'; ?>