<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles_admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="navbar">
        <div class="site-title">HealthBridge</div>
        <div>
            <!-- <a class="nav-link" href="../appointment/dashboard_patient.php">Home</a> -->
            <a class="nav-link" href="../healthcare_system/logout.php">Logout</a>
        </div>
    </div>
    <div class="dashboard-container">
        <h2 class="dashboard-heading">Admin Panel</h2>
        <p class="welcome-message">Welcome, <?php echo htmlspecialchars($_SESSION['user']['name']); ?>!</p>
        <div class="dashboard-actions">
            <a href="appointment/admin.php" class="action-button">
                <span class="button-icon">🗓️</span>
                Medical Appointment System
            </a>
            <a href="blood_donation/blood_admin.php" class="action-button">
                <span class="button-icon">🩸</span>
                Blood Donation System
            </a>
            <a href="pharmacy/admin_panel.php" class="action-button">
                <span class="button-icon">💊</span>
                Pharmacy Management System
            </a>
        </div>
    </div>
</body>
</html>