<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HealthBridge</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f9f9;
            color: #333;
        }

        header {
            background-color: #23cba7;
            padding: 15px 20px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header .logo {
            font-size: 1.5rem;
            font-weight: bold;
        }

        header nav a {
            color: white;
            margin-left: 20px;
            text-decoration: none;
            font-weight: 500;
        }

        header nav a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<header>
    <div class="logo">HealthBridge</div>
    <nav>
        <a href="../appointment/dashboard_patient.php">Home</a>
        <?php if (isset($_SESSION['user'])): ?>
            <a href="../logout.php">Logout</a>
        <?php else: ?>
            <a href="../login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>
