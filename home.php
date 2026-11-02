<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', 1);

session_start();
include('db.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hotel Elite - Home</title>
    <link rel="stylesheet" href="style.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #f4f7f6;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            background: #0d1b2a;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 40px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #ffc107;
            text-decoration: none;
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 18px;
            align-items: center;
            margin: 0;
            padding: 0;
            flex-wrap: wrap;
        }

        nav ul li a {
            color: #adb5bd;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: color 0.2s;
            white-space: nowrap;
        }

        nav ul li a:hover {
            color: #fff;
        }
        
        .sub-nav-bar {
            background: #1b263b;
            padding: 10px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .welcome-user {
            color: #ffc107;
            font-weight: bold;
            font-size: 0.95rem;
        }

        .sub-nav-right {
            display: flex;
            gap: 15px;
            align-items: center;
            margin-left: auto;
        }

        .auth-btn {
            padding: 6px 16px;
            border-radius: 5px;
            font-weight: bold;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background 0.2s;
        }

        .btn-login-sub {
            background: #28a745;
            color: #fff;
        }

        .btn-login-sub:hover {
            background: #218838;
        }
        
        .btn-signup-sub {
            background: #ffc107;
            color: #212529;
        }

        .btn-signup-sub:hover {
            background: #e0a800;
        }
        
        .btn-logout-sub {
            background: #e74c3c;
            color: #fff;
        }

        .btn-logout-sub:hover {
            background: #c0392b;
        }

        .hero {
            background: linear-gradient(rgba(13, 27, 42, 0.75), rgba(13, 27, 42, 0.75)), url('https://images.unsplash.com/photo-1566073771259-6a8506099945') no-repeat center center/cover;
            height: 420px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: #fff;
            padding: 0 20px;
        }

        .hero h1 {
            font-size: 2.8rem;
            margin-bottom: 12px;
            color: #fff;
        }

        .hero p {
            font-size: 1.15rem;
            margin: 0;
            color: #cbd5e1;
        }
        
        .container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
            text-align: center;
            width: 100%;
            flex: 1;
        }

        .section-title {
            color: #0d1b2a;
            font-size: 1.8rem;
            margin-bottom: 8px;
        }

        .section-desc {
            color: #6c757d;
            margin-bottom: 30px;
            font-size: 1rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            text-align: left;
        }

        .feature-card {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s;
        }

        .feature-card:hover {
            transform: translateY(-4px);
        }

        .feature-card h3 {
            color: #0d1b2a;
            margin-bottom: 12px;
            font-size: 1.3rem;
        }

        .feature-card p {
            color: #6c757d;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .card-link {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
            font-size: 0.95rem;
            transition: color 0.2s;
        }

        .card-link:hover {
            color: #0056b3;
            text-decoration: underline;
        }

        footer {
            text-align: center;
            padding: 20px;
            background: #0d1b2a;
            color: #fff;
            margin-top: auto;
            font-size: 0.9rem;
            border-top: 1px solid #1b263b;
        }
    </style>
</head>
<body>

    <header>
        <a href="home.php" class="logo">Hotel Elite</a>
        <nav>
            <ul>
                <li><a href="home.php" style="color: #fff; font-weight: bold;">Home</a></li>
                <li><a href="rooms.php">Rooms</a></li>
                <li><a href="food.php">Food</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
                
                <?php if (isset($_SESSION['user_email'])): ?>
                    <li><a href="my_bookings.php">My Booking</a></li>
                    <li><a href="my_orders.php">My Order</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <div class="sub-nav-bar">
        <div>
            <?php if (isset($_SESSION['user_email'])): ?>
                <span class="welcome-user">Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>!</span>
            <?php endif; ?>
        </div>
        <div class="sub-nav-right">
            <?php 
            if (isset($_SESSION['user_email'])) {
                echo '<a href="logout.php" class="auth-btn btn-logout-sub">Logout</a>';
            } else {
                echo '<a href="index.php" class="auth-btn btn-login-sub">Login</a>';
                echo '<a href="signup.php" class="auth-btn btn-signup-sub">Sign Up</a>';
            }
            ?>
        </div>
    </div>

    <div class="hero">
        <h1>Welcome to Hotel Elite</h1>
        <p>Experience ultimate luxury, comfortable rooms, and royal dining services.</p>
    </div>

    <div class="container">
        <h2 class="section-title">Our Premium Services</h2>
        <p class="section-desc">Enjoy world-class hospitality and seamless online booking.</p>

        <div class="features-grid">
            <div class="feature-card">
                <h3>Luxurious Rooms</h3>
                <p>Choose from our wide range of Deluxe, Executive, and Suite rooms equipped with modern amenities and scenic views.</p>
                <a href="rooms.php" class="card-link">View Rooms &rarr;</a>
            </div>

            <div class="feature-card">
                <h3>Room Service & Dining</h3>
                <p>Craving delicious food? Order from our special menu including Snacks, Drinks, Thalis, and Main Course right to your room.</p>
                <a href="food.php" class="card-link">Explore Menu &rarr;</a>
            </div>

            <div class="feature-card">
                <h3>Track Bookings</h3>
                <p>Easily manage and search your past room bookings and food orders securely using your personal account history.</p>
                <a href="my_bookings.php" class="card-link">My Bookings &rarr;</a>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Hotel Elite, Ahmedabad. All Rights Reserved.</p>
    </footer>

</body>
</html>