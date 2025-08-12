<?php
session_start();
include '../includes/db.php';

// if (!isset($_SESSION['user_id'])) {
//     header("Location: ../login.php");
//     exit();
// }

$user_id = $_SESSION['user_id'];

// Check if user is patient
$patient_query = "SELECT patient_id FROM patients WHERE user_id = ?";
$stmt = $conn->prepare($patient_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$patient_result = $stmt->get_result();

if ($patient_row = $patient_result->fetch_assoc()) {
    $patient_id = $patient_row['patient_id'];
    $sql = "SELECT a.*, u.name AS doctor_name
            FROM appointments a
            JOIN doctors d ON a.doctor_id = d.doctor_id
            JOIN users u ON d.user_id = u.user_id
            WHERE a.patient_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $patient_id);
} else {
    // User is doctor
    $doctor_query = "SELECT doctor_id FROM doctors WHERE user_id = ?";
    $stmt = $conn->prepare($doctor_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $doctor_result = $stmt->get_result();
    $doctor_row = $doctor_result->fetch_assoc();
    $doctor_id = $doctor_row['doctor_id'];

    $sql = "SELECT a.*, u.name AS patient_name
            FROM appointments a
            JOIN patients p ON a.patient_id = p.patient_id
            JOIN users u ON p.user_id = u.user_id
            WHERE a.doctor_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $doctor_id);
}

$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>
<head>
    <title>My Appointments</title>
    <link rel="stylesheet" href="../css/doctor_dashboard.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
            padding: 30px;
        }

        h2 {
            color: #23cba7;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th {
            background-color: #23cba7;
            color: white;
            padding: 12px;
        }

        td {
            padding: 12px;
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .back-btn {
            margin-top: 20px;
            display: inline-block;
            background-color: #23cba7;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <h2>My Appointments</h2>

    <?php if ($result->num_rows > 0): ?>
    <table>
        <tr>
            <th>Appointment ID</th>
            <th>Patient Name</th>
            <th>Doctor Name</th>
            <th>Specialization</th>
            <th>Date</th>
            <th>Status</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['appointment_id']) ?></td>
            <td><?= htmlspecialchars($row['user_name']) ?></td>
            <td>Dr. <?= htmlspecialchars($row['doctor_name']) ?></td>
            <td><?= htmlspecialchars($row['specialization']) ?></td>
            <td><?= htmlspecialchars($row['appointment_date']) ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <?php else: ?>
        <p style="text-align:center;">You have no appointments booked.</p>
    <?php endif; ?>

    <div style="text-align:center;">
        <a href="../dashboard/user_dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>
</body>
</html>
