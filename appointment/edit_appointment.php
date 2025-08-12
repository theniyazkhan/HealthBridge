<?php
session_start();
include '../includes/db.php';

// Check for doctor login
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'doctor') {
    header("Location: ../login.php");
    exit();
}

$doctor_user_id = $_SESSION['user']['id'];

// Check if an appointment ID is provided in the URL
if (!isset($_GET['id'])) {
    header("Location: doctor_dashboard.php");
    exit();
}

$appointment_id = $_GET['id'];

// Handle form submission for updating the appointment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_date = $_POST['appointment_date'];
    $new_reason = $_POST['reason'];

    // Update the appointment in the database
    $updateStmt = $conn->prepare("UPDATE appointments SET appointment_date = ?, reason = ? WHERE appointment_id = ? AND doctor_id = (SELECT doctor_id FROM doctors WHERE user_id = ?)");
    $updateStmt->bind_param("ssii", $new_date, $new_reason, $appointment_id, $doctor_user_id);

    if ($updateStmt->execute()) {
        $success_message = "Appointment updated successfully!";
    } else {
        $error_message = "Error updating appointment: " . $updateStmt->error;
    }
}

// Fetch the specific appointment details for the form
$apptStmt = $conn->prepare("
    SELECT a.*, u.name AS patient_name
    FROM appointments a
    JOIN users u ON a.patient_id = u.id
    WHERE a.appointment_id = ? AND a.doctor_id = (SELECT doctor_id FROM doctors WHERE user_id = ?)
");
$apptStmt->bind_param("ii", $appointment_id, $doctor_user_id);
$apptStmt->execute();
$appointment = $apptStmt->get_result()->fetch_assoc();

if (!$appointment) {
    header("Location: doctor_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Appointment</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/edit_appointment.css">
</head>
<body>
    <header class="site-header">
        <div class="site-title">HealthBridge</div>
        <nav class="header-nav">
            <a class="nav-link" href="../doctor_dashboard.php">Dashboard</a>
            <a class="nav-link" href="../logout.php">Logout</a>
        </nav>
    </header>

    <main class="main-content">
        <div class="form-container">
            <h2>Edit Appointment for <?php echo htmlspecialchars($appointment['patient_name']); ?></h2>
            
            <?php if (isset($success_message)): ?>
                <p style="color: green;"><?php echo htmlspecialchars($success_message); ?></p>
            <?php elseif (isset($error_message)): ?>
                <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="appointment_date">Appointment Date:</label>
                    <input type="datetime-local" id="appointment_date" name="appointment_date" value="<?php echo date('Y-m-d\TH:i', strtotime($appointment['appointment_date'])); ?>" required>
                </div>
                <div class="form-group">
                    <label for="reason">Reason:</label>
                    <textarea id="reason" name="reason" rows="4" required><?php echo htmlspecialchars($appointment['reason']); ?></textarea>
                </div>
                <button type="submit">Update Appointment</button>
            </form>
        </div>
    </main>
    <footer class="site-footer">
        <p>&copy; <?php echo date("Y"); ?> HealthBridge. All rights reserved.</p>
    </footer>
</body>
</html>