<?php
session_start();
include '../includes/db.php';

// Check if the user is logged in and is a doctor
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'doctor') {
    header("Location: ../login.php");
    exit();
}

$doctor_user_id = $_SESSION['user']['id'];

// Fetch doctor details from the 'doctors' table using the user's ID
$dstmt = $conn->prepare("SELECT * FROM doctors WHERE user_id = ?");
$dstmt->bind_param("i", $doctor_user_id);
$dstmt->execute();
$doctorDetails = $dstmt->get_result()->fetch_assoc();

// Fetch appointments for the logged-in doctor
$apptStmt = $conn->prepare("
    SELECT a.*, u.name AS patient_name, u.phone AS patient_phone
    FROM appointments a
    JOIN users u ON a.patient_id = u.id
    WHERE a.doctor_id = ?
    ORDER BY a.appointment_date ASC
");
$apptStmt->bind_param("i", $doctorDetails['doctor_id']);
$apptStmt->execute();
$appointments = $apptStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- <link rel="stylesheet" href="../css/doctor_dashboard.css">  -->
     <style>
        /* doctor_dashboard.css */

/* Global and Base Styling */
:root {
    --primary-color: #23cba7;
    --secondary-color: #1a6f63;
    --background-color: #f0f8ff;
    --text-color: #333;
    --link-color: #2196f3;
    --border-radius-small: 6px;
    --border-radius-medium: 8px;
    --border-radius-large: 10px;
    --box-shadow-light: 0 2px 5px rgba(0, 0, 0, 0.1);
    --box-shadow-medium: 0 5px 20px rgba(0, 0, 0, 0.1);
}

* {
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: var(--background-color);
    margin: 0;
    padding: 0;
    color: var(--text-color);
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

/* Header */
.site-header {
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: white;
    box-shadow: var(--box-shadow-light);
}

.site-title {
    font-size: 28px;
    font-weight: 600;
    letter-spacing: 1px;
}

.header-nav {
    display: flex;
    gap: 15px;
}

.nav-link {
    color: white;
    text-decoration: none;
    font-weight: 400;
    font-size: 16px;
    padding: 8px 15px;
    border-radius: var(--border-radius-small);
    transition: background-color 0.3s ease;
}

.nav-link:hover {
    background-color: rgba(255, 255, 255, 0.2);
    text-decoration: none;
}

/* Main Content and Containers */
.main-content {
    flex-grow: 1;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 20px;
}

.dashboard-container {
    padding: 30px;
    text-align: center;
    background: white;
    border-radius: var(--border-radius-large);
    box-shadow: var(--box-shadow-medium);
    max-width: 900px;
    width: 100%;
    margin: 40px auto;
}

/* Headings */
.dashboard-heading {
    color: var(--secondary-color);
    font-size: 36px;
    margin-bottom: 20px;
}

.section-title {
    font-size: 1.8rem;
    color: var(--secondary-color);
    border-bottom: 2px solid #e0e0e0;
    padding-bottom: 10px;
    margin-top: 40px;
    margin-bottom: 20px;
}

/* Doctor Details */
.doctor-details {
    background-color: #f7f7f7;
    border-left: 5px solid var(--primary-color);
    padding: 15px 20px;
    margin-bottom: 30px;
    border-radius: 5px;
    text-align: left;
}

.doctor-details p {
    margin: 5px 0;
    font-size: 1.1rem;
    color: #555;
}

.doctor-details p span {
    font-weight: 500;
    color: #333;
}

/* Appointments Table */
.table-responsive {
    overflow-x: auto;
}

.appointments-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.appointments-table th, .appointments-table td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #e0e0e0;
}

.appointments-table th {
    background-color: var(--primary-color);
    color: white;
    font-weight: 500;
    text-transform: uppercase;
}

.appointments-table tr:hover {
    background-color: #f9f9f9;
}

.appointments-table tbody tr:last-child td {
    border-bottom: none;
}

.no-appointments {
    text-align: center;
    color: #888;
    font-style: italic;
    padding: 20px;
}

/* Action Buttons */
.action-link {
    padding: 8px 12px;
    border-radius: var(--border-radius-small);
    text-decoration: none;
    font-weight: 500;
    transition: background-color 0.3s;
}

.edit-button {
    background-color: var(--link-color);
    color: white;
}

.edit-button:hover {
    background-color: #0d8bf2;
    text-decoration: none;
}

/* Footer */
.site-footer {
    background-color: #333;
    color: #f0f8ff;
    text-align: center;
    padding: 20px;
    margin-top: auto;
    font-size: 14px;
}
     </style>
</head>
<body>
    <header class="site-header">
        <div class="site-title">HealthBridge</div>
        <nav class="header-nav">
            <!-- <a class="nav-link" href="#">Dashboard</a> -->
            <a class="nav-link" href="edit_profile.php">Edit Profile</a>
            <a class="nav-link" href="../logout.php">Logout</a>
        </nav>
    </header>

    <main class="main-content">
        <div class="dashboard-container">
            <h2 class="dashboard-heading">Welcome Dr. <?php echo htmlspecialchars($_SESSION['user']['name']); ?></h2>
            
            <div class="doctor-details">
                <p>Email: <span><?php echo htmlspecialchars($_SESSION['user']['email']); ?></span></p>
                <p>Specialization: <span><?php echo htmlspecialchars($doctorDetails['specialization']); ?></span></p>
            </div>

            <h3 class="section-title">Your Upcoming Appointments</h3>
            
            <?php if ($appointments->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="appointments-table">
                        <thead>
                            <tr>
                                <th>Patient Name</th>
                                <th>Phone</th>
                                <th>Date</th>
                                <th>Reason</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $appointments->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['patient_phone']); ?></td>
                                    <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['reason']); ?></td>
                                    <td>
                                        <a href="edit_appointment.php?id=<?php echo $row['appointment_id']; ?>" class="action-link edit-button">Edit</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="no-appointments">No upcoming appointments found.</p>
            <?php endif; ?>
        </div>
    </main>
    <footer class="site-footer">
        <p>&copy; <?php echo date("Y"); ?> HealthBridge. All rights reserved.</p>
    </footer>
</body>
</html>