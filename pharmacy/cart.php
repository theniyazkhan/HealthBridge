<?php
session_start();
include '../includes/db.php';

// Handle Remove Item
if (isset($_GET['remove'])) {
    $remove_id = $_GET['remove'];
    if (isset($_SESSION['cart'][$remove_id])) {
        unset($_SESSION['cart'][$remove_id]);
    }
    header("Location: cart.php"); // Refresh the page
    exit;
}

// Handle Reduce Item
if (isset($_GET['reduce'])) {
    $reduce_id = $_GET['reduce'];
    if (isset($_SESSION['cart'][$reduce_id])) {
        $_SESSION['cart'][$reduce_id]--;
        if ($_SESSION['cart'][$reduce_id] <= 0) {
            unset($_SESSION['cart'][$reduce_id]);
        }
    }
    header("Location: cart.php");
    exit;
}

// Handle Add Item
if (isset($_GET['add'])) {
    $add_id = $_GET['add'];
    if (isset($_SESSION['cart'][$add_id])) {
        // Check stock before incrementing
        $stmt = $conn->prepare("SELECT stock FROM medicines WHERE id = ?");
        $stmt->bind_param("i", $add_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $medicine = $result->fetch_assoc();
        
        if ($medicine && $_SESSION['cart'][$add_id] < $medicine['stock']) {
            $_SESSION['cart'][$add_id]++;
        }
    }
    header("Location: cart.php");
    exit;
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<h2>Your cart is empty.</h2>";
    echo "<a href='search_medicine.php'>Back to Pharmacy</a>";
    exit;
}

// Dummy user_id (replace with session login logic)
$user = $_SESSION['user'];

// Fetch medicine info
$cart = $_SESSION['cart'];
$medicine_ids = implode(',', array_keys($cart));
$result = $conn->query("SELECT * FROM medicines WHERE id IN ($medicine_ids)");

$medicines = [];
$total = 0;
while ($row = $result->fetch_assoc()) {
    $row['quantity'] = $cart[$row['id']];
    $row['subtotal'] = $row['quantity'] * $row['price'];
    $total += $row['subtotal'];
    $medicines[] = $row;
}

// Handle order confirmation
if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['confirm_order'])) {
    $delivery_name = $_SESSION['user']['name'];
    $delivery_phone = $_SESSION['user']['phone'];
    $delivery_address = $_POST['delivery_address'];
    $delivery_city = $_POST['delivery_city'];

    $payment_method = $_POST['payment_method'];
    $bkash_number = $_POST['bkash_number'] ?? null;
    $bkash_txn = $_POST['bkash_txn'] ?? null;

    // Validate required fields
    if ($payment_method === "bKash" && (empty($bkash_number) || empty($bkash_txn))) {
        echo "<div class='error-message'>Please provide both bKash number and transaction ID.</div>";
        exit;
    }

    $user_id = $_SESSION['user']['id'];
    $order_group_id = time(); // Use timestamp as unique order group ID

    $conn->begin_transaction();

    try {
        foreach ($medicines as $med) {
            if ($med['quantity'] > $med['stock']) {
                throw new Exception("Insufficient stock for {$med['name']}");
            }

            $stmt = $conn->prepare("
                INSERT INTO orders 
                (user_id, medicine_id, quantity, price, payment_method, bkash_number, bkash_txn, order_date, 
                 delivery_name, delivery_phone, delivery_address, delivery_city, order_group_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, CURDATE(), ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "iiidsssssssi",
                $user_id,
                $med['id'],
                $med['quantity'],
                $med['price'],
                $payment_method,
                $bkash_number,
                $bkash_txn,
                $delivery_name,
                $delivery_phone,
                $delivery_address,
                $delivery_city,
                $order_group_id
            );

            $stmt->execute();

            // Update stock
            $stmt = $conn->prepare("UPDATE medicines SET stock = stock - ? WHERE id = ?");
            $stmt->bind_param("ii", $med['quantity'], $med['id']);
            $stmt->execute();
        }

        $conn->commit();
        $_SESSION['cart'] = [];
        ?>
        <div class="order-success-centered">
        <span class="success-icon">✔</span>
        <h2>Order placed successfully using <strong><?= htmlspecialchars($payment_method) ?></strong>!</h2>
        <a href="search_medicine.php" class="back-button">Back to Pharmacy</a>
        </div>
        <?php
    } catch (Exception $e) {
        $conn->rollback();
        echo "<div class='error-message'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
        exit;
    }
}


?>

<!DOCTYPE html>
<html>

<head>
    <title>Cart - HealthBridge</title>
    <style>
        body {
            font-family: Arial;
            margin: 30px;
            background: #f4f4f4;
        }

        table {
            width: 100%;
            background: white;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: center;
        }

        th {
            background: #0077cc;
            color: white;
        }

        h2 {
        color: #333;
    }

    .total {
        font-size: 20px;
        text-align: right;
        margin-bottom: 20px;
    }

    button {
        padding: 10px 20px;
        background: green;
        color: white;
        border: none;
        cursor: pointer;
    }

    a {
        text-decoration: none;
        color: #0077cc;
    }

    #bkashFields {
        margin-top: 15px;
        padding: 15px;
        background: white;
        border-radius: 5px;
        border: 1px solid #ccc;
        width: 400px;
    }

    #bkashFields label {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    #bkashFields label span {
        flex: 0 0 120px;
        font-weight: bold;
        color: #333;
        padding-right: 10px; /* Increased gap after colon */
    }

    #bkashFields input[type="text"] {
        flex: 1;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 14px;
    }

    .deliveryFields {
        margin-top: 15px;
        padding: 15px;
        background: white;
        border-radius: 5px;
        border: 1px solid #ccc;
        width: 400px;
    }

    .deliveryFields label {
        display: flex;
        align-items: flex-start;
        margin-bottom: 10px;
    }

    .deliveryFields label span {
        flex: 0 0 120px;
        font-weight: bold;
        color: #333;
        padding-right: 10px;
    }

    .deliveryFields input[type="text"],
    .deliveryFields textarea {
        flex: 1;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 14px;
        resize: vertical; /* Allows vertical resizing for textarea */
    }

   .deliveryFields textarea {
        height: 30px; /* Fixed height for better usability */
    }

    .form-container {
        margin-top: 15px;
        padding: 15px;
        background: #e6f7fa; /* Light blue tint */
        border-radius: 5px;
        border: 1px solid #ccc;
        width: 430px;
    }

    .confirm-button {
        margin-top: 15px;
        padding: 10px;
         width: 430px; /* Matches form container width */
        background: green;
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 14px;
        cursor: pointer;
        display: block;
    }

    .confirm-button:hover {
        background: darkgreen;
    }

    .error-message {
    margin: 20px auto;
    padding: 20px;
    background: #ffe6e6;
    border-radius: 10px;
    border: 1px solid #cc0000;
    width: 400px;
    text-align: center;
    color: #cc0000;
    animation: fadeIn 0.5s ease-in;
    }

    </style>
</head>

<body>

    <h2>Your Cart</h2>
    <table>
        <tr>
            <th>Medicine Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
        </tr>
        <?php foreach ($medicines as $med): ?>
            <tr>
                <td><?= htmlspecialchars($med['name']) ?></td>
                <td>৳<?= $med['price'] ?></td>
                <td>
                    <?= $med['quantity'] ?>
                    <a href="?add=<?= $med['id'] ?>" style="margin-left:10px; color:green;">[+]</a>
                    <a href="?reduce=<?= $med['id'] ?>" style="margin-left:10px; color:orange;">[-]</a>
                    <a href="?remove=<?= $med['id'] ?>" style="margin-left:10px; color:red;">[Remove]</a>
                </td>
                <td>৳<?= number_format($med['subtotal'], 2) ?></td>
            </tr>
        <?php endforeach; ?>

    </table>

    <div class="total"><strong>Total: ৳<?= number_format($total, 2) ?></strong></div>

    <div class="form-container">
    <form method="POST">
        <h3>Select Payment Method:</h3>
        <label><input type="radio" name="payment_method" value="Cash on Delivery" required onchange="showPaymentFields()"> Cash on Delivery</label><br>
        <label><input type="radio" name="payment_method" value="bKash" onchange="showPaymentFields()"> bKash</label><br>

        <div id="bkashFields" style="display: none;">
            <label><span>bKash Number:</span> <input type="text" name="bkash_number"></label>
            <label><span>Transaction ID:</span> <input type="text" name="bkash_txn"></label>
        </div>
        
        <h3>Delivery Info:</h3>
        <p><strong>Name:</strong> <?= htmlspecialchars($_SESSION['user']['name']) ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($_SESSION['user']['phone']) ?></p>

        <div class="deliveryFields">
            <h3>Delivery Address:</h3>
            <label><span>Address:</span> <textarea name="delivery_address" required></textarea></label>
            <label><span>City:</span> <input type="text" name="delivery_city" required></label>
        </div>

        <button type="submit" name="confirm_order" class="confirm-button">Confirm Order</button>
    </form>
</div>

    <script>
        function showPaymentFields() {
            const method = document.querySelector('input[name="payment_method"]:checked').value;
            document.getElementById('bkashFields').style.display = (method === 'bKash') ? 'block' : 'none';
        }
    </script>



    <p><a href="search_medicine.php">← Back to Search</a></p>

</body>

</html>