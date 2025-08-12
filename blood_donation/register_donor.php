<?php

session_start();
include '../includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'patient') {
    header("Location: ../login.php");
    exit();
}
// Add donor
if (isset($_POST['add_donor'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $blood_group_id = $_POST['blood_group'];
    $last_donation_date = !empty($_POST['last_donation_date']) ? $_POST['last_donation_date'] : NULL;
    $image = "uploads/" . basename($_FILES["image"]["name"]);
    move_uploaded_file($_FILES["image"]["tmp_name"], $image);

    $stmt = $conn->prepare("INSERT INTO donors (name, image, phone, address, blood_group_id, last_donation_date, approved) VALUES (?, ?, ?, ?, ?, ?, 1)");
    $stmt->bind_param("ssssss", $name, $image, $phone, $address, $blood_group_id, $last_donation_date);
    $stmt->execute();
}
// Fetch data
$blood_groups = $conn->query("SELECT * FROM blood_groups");
$pending_donors = $conn->query("SELECT donors.*, blood_groups.name AS blood_group FROM donors JOIN blood_groups ON donors.blood_group_id = blood_groups.id WHERE donors.approved = 0");
$donors_list = $conn->query("SELECT donors.*, blood_groups.name AS blood_group FROM donors JOIN blood_groups ON donors.blood_group_id = blood_groups.id WHERE donors.approved = 1");
$blood_requests = $conn->query("SELECT r.*, bg.name AS blood_group, d.name AS donor_name, d.phone AS donor_phone FROM requests r JOIN blood_groups bg ON r.blood_group_id = bg.id LEFT JOIN donors d ON r.donor_id = d.id ORDER BY r.id DESC");
?>


<!DOCTYPE html>
<html>
<head>
    <title>Register Blood Donor</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f6fa; padding: 20px; }
        h1 { text-align: center; color: #d63031; margin-bottom: 30px; }
        .section { background: white; padding: 20px; border-radius: 10px; margin-bottom: 30px; box-shadow: 0px 4px 8px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background: #d63031; color: white; }
        input, select { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ccc; border-radius: 5px; }
        button { background: #d63031; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer; }
        a { padding: 5px 10px; color: white; border-radius: 5px; text-decoration: none; }
        .approve { background: green; }
        .reject { background: red; }
    </style>
</head>
<body>

<h1>Register Blood Donor</h1>

<!-- Add Donor -->
<div class="section">
    <h2>Add New Donor</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="text" name="phone" placeholder="Phone Number" required>
        <input type="text" name="address" placeholder="Address" required>
        <select name="blood_group" required>
            <option value="">Select Blood Group</option>
            <?php while($bg = $blood_groups->fetch_assoc()) { ?>
                <option value="<?= $bg['id'] ?>"><?= $bg['name'] ?></option>
            <?php } ?>
        </select>
        <input type="file" name="image" accept="image/*" required>
        <label>Last Donation Date:</label>
        <input type="date" name="last_donation_date">
        <button type="submit" name="add_donor">Add Donor</button>
    </form>
</div>

</body>
</html>