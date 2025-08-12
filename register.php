<?php
include 'includes/db.php'; // Adjust path if needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $phone    = $_POST['phone'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = $_POST['role']; // patient, doctor, pharmacist, admin

    // Insert into users table
    $stmt = $conn->prepare("INSERT INTO users (name, email, phone, username, password, role) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $email, $phone, $username, $password, $role);

    if ($stmt->execute()) {
        $user_id = $conn->insert_id; // Get the inserted user's ID

        // Insert into role-specific table
        switch ($role) {
            case 'doctor':
                $roleStmt = $conn->prepare("INSERT INTO doctors (specialization, user_id) VALUES (?, ?)");
                $roleStmt->bind_param("si", $specialization, $user_id);
                break;
            case 'patient':
                $roleStmt = $conn->prepare("INSERT INTO patients (user_id, blood_group) VALUES (?, ?)");
                $roleStmt->bind_param("is", $user_id, $blood_group);
                break;
            case 'pharmacist':
                $roleStmt = $conn->prepare("INSERT INTO pharmacists (id, name, email, phone) VALUES (?, ?, ?, ?)");
                $roleStmt->bind_param("isss", $user_id, $name, $email, $phone);
                break;
            case 'admin':
                $roleStmt = $conn->prepare("INSERT INTO admins (user_id, name, email, phone) VALUES (?, ?, ?, ?)");
                $roleStmt->bind_param("isss", $user_id, $name, $email, $phone);
                break;
            default:
                echo "<p style='color: red;'>Unknown role selected.</p>";
                exit();
        }

        if ($roleStmt->execute()) {
            echo "<p style='color: green;'>Registration successful. <a href='login.php'>Login Now</a></p>";
        } else {
            echo "<p style='color: red;'>Error inserting into $role table: " . $roleStmt->error . "</p>";
        }

    } else {
        echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Register</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="form-container">
    <h2 class="reg_heading">User Registration</h2>
    <form method="POST" action="">
      <input class="reg_input" type="text" name="name" placeholder="Full Name" required><br>
      <input class="reg_input" type="email" name="email" placeholder="Email" required><br>
      <input class="reg_input" type="text" name="phone" placeholder="Phone Number" required><br>
      <input class="reg_input" type="text" name="username" placeholder="Username" required><br>
      <input class="reg_input" type="password" name="password" placeholder="Password" required><br>
      <select name="role" required>
        <option value="patient">Patient</option>
        <option value="doctor">Doctor</option>
        <!-- <option value="pharmacist">Pharmacist</option> -->
        <option value="admin">Admin</option>
      </select><br><br>
      <button type="submit">Register</button>
    </form>
    <div class="reg_login">
      <p style='color: green;text-align:center;'>Already registered? <a href='login.php'>Login Now</a></p>
    </div>
  </div>
</body>
</html>
