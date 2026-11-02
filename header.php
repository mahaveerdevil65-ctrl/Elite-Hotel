<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Elite - Management System</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f4f6f9;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        .main-header {
            background-color: #0d1b2a;
            color: #fff;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .logo-area h2 {
            font-size: 22px;
            color: #ffc107;
            letter-spacing: 1px;
        }

        .center-title h1 {
            font-size: 20px;
            font-weight: 600;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .nav-links {
            background: #fff;
            padding: 12px 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }

        .nav-links a {
            text-decoration: none;
            color: #495057;
            font-weight: 600;
            font-size: 14px;
            padding: 6px 12px;
            border-radius: 4px;
            transition: 0.2s;
        }

        .nav-links a:hover {
            background: #007bff;
            color: #fff;
        }

        .main-content {
            padding: 0 20px 40px 20px;
            flex: 1;
        }

        footer {
            background: #0d1b2a;
            color: #aaa;
            text-align: center;
            padding: 20px;
            font-size: 0.9rem;
            margin-top: auto;
        }
    </style>
</head>
<body>

    <header class="main-header">
        <div class="logo-area">
            <h2>HOTEL ELITE</h2>
        </div>
        <div class="center-title">
            <h1>Hotel Management System</h1>
        </div>
    </header>

    <div class="nav-links">
        <a href="home.php">Home</a>
        <a href="rooms.php">Rooms</a>
        <a href="food.php">Food</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
        
        <?php if (isset($_SESSION['user_email'])): ?>
            <a href="my_bookings.php">My Booking</a>
            <a href="my_orders.php">My Order</a>
            <a href="logout.php" style="color: #e74c3c; font-weight: bold;">Logout</a>
        <?php else: ?>
            <a href="index.php" style="color: #28a745; font-weight: bold;">Login</a>
            <a href="signup.php" style="color: #ffc107; font-weight: bold;">Signup</a>
        <?php endif; ?>
    </div>

    <div class="main-content">