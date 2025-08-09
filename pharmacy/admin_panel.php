<?php
session_start();
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'pharmacist'])) {
    header("Location: ../login.php");
    exit();
}
?>

<?php
session_start();
$conn = new mysqli("localhost", "root", "", "healthcare_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Handle Add Medicine
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    $image_name = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $upload_dir = "images/";

    if (!is_dir($upload_dir)) mkdir($upload_dir);

    $target_path = $upload_dir . basename($image_name);
    move_uploaded_file($image_tmp, $target_path);

    $stmt = $conn->prepare("INSERT INTO medicines (name, price, stock, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdis", $name, $price, $stock, $image_name);

    $stmt->execute();
}

// Handle Update Medicine
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    $upload_dir = "images/";
    if (!is_dir($upload_dir)) mkdir($upload_dir);

    if (!empty($_FILES['image']['name'])) {
        // If a new image is uploaded
        $image_name = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $target_path = $upload_dir . basename($image_name);
        move_uploaded_file($image_tmp, $target_path);

        $stmt = $conn->prepare("UPDATE medicines SET name=?, price=?, stock=?, image=? WHERE id=?");
        $stmt->bind_param("sdisi", $name, $price, $stock, $image_name, $id);
    } else {
        // If image is not updated
        $stmt = $conn->prepare("UPDATE medicines SET name=?, price=?, stock=? WHERE id=?");
        $stmt->bind_param("sddi", $name, $price, $stock, $id);
    }

    $stmt->execute();
}

// Handle Delete Medicine
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM medicines WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

$result = $conn->query("SELECT * FROM medicines ORDER BY id DESC");
// Fetch orders for general order history, grouped by order_group_id
$orders_result = $conn->query("
    SELECT o.order_group_id, o.order_date, o.payment_method, o.bkash_number, o.bkash_txn,
           o.delivery_name, o.delivery_phone, o.delivery_address, o.delivery_city,
           u.name AS user_name, u.email AS user_email,
           GROUP_CONCAT(CONCAT(m.name, ' (', o.quantity, ' x ৳', o.price, ')') SEPARATOR '<br>') AS items,
           SUM(o.quantity * o.price) AS total_price
    FROM orders o
    JOIN medicines m ON o.medicine_id = m.id
    JOIN users u ON o.user_id = u.id
    GROUP BY o.order_group_id, o.order_date, o.payment_method, o.bkash_number, o.bkash_txn,
             o.delivery_name, o.delivery_phone, o.delivery_address, o.delivery_city,
             u.name, u.email
    ORDER BY o.order_date DESC
");

// Handle user-specific order search
$user_orders_result = null;
$user_search = '';
if (isset($_POST['search_user'])) {
    $user_search = trim($_POST['user_search']);
    if (!empty($user_search)) {
        $stmt = $conn->prepare("
            SELECT o.order_group_id, o.order_date, o.payment_method, o.bkash_number, o.bkash_txn,
                   o.delivery_name, o.delivery_phone, o.delivery_address, o.delivery_city,
                   u.name AS user_name, u.email AS user_email,
                   GROUP_CONCAT(CONCAT(m.name, ' (', o.quantity, ' x ৳', o.price, ')') SEPARATOR '<br>') AS items,
                   SUM(o.quantity * o.price) AS total_price
            FROM orders o
            JOIN medicines m ON o.medicine_id = m.id
            JOIN users u ON o.user_id = u.id
            WHERE u.name LIKE ? OR u.email LIKE ?
            GROUP BY o.order_group_id, o.order_date, o.payment_method, o.bkash_number, o.bkash_txn,
                     o.delivery_name, o.delivery_phone, o.delivery_address, o.delivery_city,
                     u.name, u.email
            ORDER BY o.order_date DESC
        ");
        $search_term = "%$user_search%";
        $stmt->bind_param("ss", $search_term, $search_term);
        $stmt->execute();
        $user_orders_result = $stmt->get_result();
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - Pharmacy</title>
    <style>
        body { font-family: Arial; margin: 20px; background: #f4f4f4; }
        h2 { color: #333; }
        form { background: white; padding: 20px; margin-bottom: 30px; border-radius: 10px; width: 400px; }
        label { display: block; margin-top: 10px; }
        input[type="text"], input[type="number"] {
            width: 100%; padding: 8px; margin-top: 5px;
        }
        button { margin-top: 15px; padding: 10px 15px; background: #0077cc; color: white; border: none; cursor: pointer; }
        table { width: 100%; background: white; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background: #0077cc; color: white; }
        .edit-form { background: #fff7e6; padding: 10px; margin-top: 10px; }
        a { text-decoration: none; color: red; font-weight: bold; }
        .user-search-form { background: white; padding: 20px; margin-bottom: 30px; border-radius: 10px; width: 400px; }
        .user-search-form input[type="text"] { width: calc(100% - 90px); display: inline-block; }
        .user-search-form button { display: inline-block; width: 80px; }
    </style>
</head>
<body>

<h2>Add New Medicine</h2>
<form method="POST" enctype="multipart/form-data">
    <label>Medicine Name:</label>
    <input type="text" name="name" required>

    <label>Price (৳):</label>
    <input type="number" name="price" step="0.01" required>

    <label>Stock Quantity:</label>
    <input type="number" name="stock" required>

    <label>Image:</label>
    <input type="file" name="image" accept="image/*" required>

    <button type="submit" name="add">Add Medicine</button>
</form>

<h2>Existing Medicines</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Price (৳)</th>
        <th>Stock</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= $row['price'] ?></td>
            <td><?= $row['stock'] ?></td>
            <td>
                <a href="?edit=<?= $row['id'] ?>">Edit</a> |
                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this medicine?')">Delete</a>
            </td>
        </tr>

        <?php if (isset($_GET['edit']) && $_GET['edit'] == $row['id']): ?>
            <tr>
                <td colspan="5">
                    <form method="POST" class="edit-form">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <label>Name:</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>" required>

                        <label>Price:</label>
                        <input type="number" name="price" value="<?= $row['price'] ?>" step="0.01" required>

                        <label>Stock:</label>
                        <input type="number" name="stock" value="<?= $row['stock'] ?>" required>

                        <label>Image:</label>
                        <input type="file" name="image" accept="image/*" required>
                        
                        <button type="submit" name="update">Update</button>

                    </form>
                </td>
            </tr>
        <?php endif; ?>
    <?php endwhile; ?>
</table>

<h2>Order History</h2>
<table>
    <tr>
        <th>Order Group ID</th>
        <th>User</th>
        <th>Email</th>
        <th>Items</th>
        <th>Total Price (৳)</th>
        <th>Payment Method</th>
        <th>bKash Number</th>
        <th>Transaction ID</th>
        <th>Delivery Name</th>
        <th>Delivery Phone</th>
        <th>Delivery Address</th>
        <th>Delivery City</th>
        <th>Order Date</th>
    </tr>
    <?php while ($order = $orders_result->fetch_assoc()): ?>
        <tr>
            <td><?= $order['order_group_id'] ?></td>
            <td><?= htmlspecialchars($order['user_name']) ?></td>
            <td><?= htmlspecialchars($order['user_email']) ?></td>
            <td><?= $order['items'] ?></td>
            <td><?= number_format($order['total_price'], 2) ?></td>
            <td><?= htmlspecialchars($order['payment_method']) ?></td>
            <td><?= htmlspecialchars($order['bkash_number'] ?? '-') ?></td>
            <td><?= htmlspecialchars($order['bkash_txn'] ?? '-') ?></td>
            <td><?= htmlspecialchars($order['delivery_name']) ?></td>
            <td><?= htmlspecialchars($order['delivery_phone']) ?></td>
            <td><?= htmlspecialchars($order['delivery_address']) ?></td>
            <td><?= htmlspecialchars($order['delivery_city']) ?></td>
            <td><?= $order['order_date'] ?></td>
        </tr>
    <?php endwhile; ?>
</table>

<h2>User Order Details</h2>
<form method="POST" class="user-search-form">
    <label>Search User (Name or Email):</label>
    <input type="text" name="user_search" value="<?= htmlspecialchars($user_search) ?>" placeholder="Enter name or email">
    <button type="submit" name="search_user">Search</button>
</form>

<?php if ($user_orders_result && $user_orders_result->num_rows > 0): ?>
    <table>
        <tr>
            <th>Order Group ID</th>
            <th>User</th>
            <th>Email</th>
            <th>Items</th>
            <th>Total Price (৳)</th>
            <th>Payment Method</th>
            <th>bKash Number</th>
            <th>Transaction ID</th>
            <th>Delivery Name</th>
            <th>Delivery Phone</th>
            <th>Delivery Address</th>
            <th>Delivery City</th>
            <th>Order Date</th>
        </tr>
        <?php while ($order = $user_orders_result->fetch_assoc()): ?>
            <tr>
                <td><?= $order['order_group_id'] ?></td>
                <td><?= htmlspecialchars($order['user_name']) ?></td>
                <td><?= htmlspecialchars($order['user_email']) ?></td>
                <td><?= $order['items'] ?></td>
                <td><?= number_format($order['total_price'], 2) ?></td>
                <td><?= htmlspecialchars($order['payment_method']) ?></td>
                <td><?= htmlspecialchars($order['bkash_number'] ?? '-') ?></td>
                <td><?= htmlspecialchars($order['bkash_txn'] ?? '-') ?></td>
                <td><?= htmlspecialchars($order['delivery_name']) ?></td>
                <td><?= htmlspecialchars($order['delivery_phone']) ?></td>
                <td><?= htmlspecialchars($order['delivery_address']) ?></td>
                <td><?= htmlspecialchars($order['delivery_city']) ?></td>
                <td><?= $order['order_date'] ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php elseif (isset($_POST['search_user']) && empty($user_search)): ?>
    <p style="color: red;">Please enter a name or email to search.</p>
<?php elseif (isset($_POST['search_user'])): ?>
    <p style="color: red;">No orders found for this user.</p>
<?php endif; ?>

</body>
</html>