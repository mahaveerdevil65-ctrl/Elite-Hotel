<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', 1);

session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Elite - About Us</title>
    <link rel="stylesheet" href="style.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f4f7f6;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        header {
            background-color: #111827;
            color: #ffffff;
            padding: 15px 50px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #f59e0b;
            text-decoration: none;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 25px;
            align-items: center;
            margin: 0;
            padding: 0;
        }

        nav ul li a {
            color: #ffffff;
            text-decoration: none;
            font-size: 16px;
            transition: color 0.3s;
        }

        nav ul li a:hover {
            color: #f59e0b;
        }

        .header-bottom {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            width: 100%;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 10px;
        }

        .btn-nav-login {
            background-color: #10b981;
            color: white;
            padding: 8px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
        }

        .btn-nav-signup {
            background-color: #f59e0b;
            color: white;
            padding: 8px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
        }

        .container {
            flex: 1;
            max-width: 900px;
            margin: 30px auto;
            padding: 0 20px;
            width: 100%;
        }

        .about-card {
            background: #fff;
            padding: 40px 35px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 25px;
            text-align: center;
        }

        .about-card h2 {
            color: #111827;
            margin-bottom: 15px;
            font-size: 26px;
            font-weight: bold;
        }

        .about-card p {
            color: #6c757d;
            font-size: 1.05rem;
            line-height: 1.6;
        }

        .location-card {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            text-align: center;
        }

        .location-card h3 {
            margin-bottom: 8px;
            color: #111827;
            font-size: 1.4rem;
            font-weight: bold;
        }

        .location-card p {
            color: #6c757d;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }

        .map-wrapper {
            overflow: hidden;
            border-radius: 6px;
            height: 350px;
            border: 1px solid #e9ecef;
        }

        .map-wrapper iframe {
            width: 100%;
            height: 100%;
            border: 0;
        }

        footer {
            background-color: #111827;
            color: #ffffff;
            text-align: center;
            padding: 18px 20px;
            font-size: 0.95rem;
            margin-top: auto;
        }
    </style>
</head>
<body>

    <header>

        <div class="header-top">
            <a href="home.php" class="logo">Hotel Elite</a>
            <nav>
                <ul>
                    <li><a href="home.php">Home</a></li>
                    <li><a href="rooms.php">Rooms</a></li>
                    <li><a href="food.php">Food</a></li>
                    <li><a href="about.php" style="color: #f59e0b; font-weight: bold;">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <?php if(isset($_SESSION['user_email'])): ?>
                        <li><a href="my_bookings.php">My Booking</a></li>
                        <li><a href="my_orders.php">My Order</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>

        <div class="header-bottom">
            <?php if(isset($_SESSION['user_email'])): ?>
                <a href="logout.php" class="btn-nav-login" style="background: #e74c3c;">Logout</a>
            <?php else: ?>
                <a href="index.php" class="btn-nav-login">Login</a>
                <a href="signup.php" class="btn-nav-signup">Sign Up</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="container">
        <div class="about-card">
            <h2>About Hotel Elite, Ahmedabad</h2>
            <p>
                Welcome to Hotel Elite located in the heart of Ahmedabad, Gujarat (CG Road, Navrangpura). We combine modern luxury with traditional Gujarati hospitality to offer an unforgettable stay.
            </p>
        </div>

        <div class="location-card">
            <h3>Our Location</h3>
            <p>CG Road, Navrangpura, Ahmedabad, Gujarat 380009</p>
            
            <div class="map-wrapper">
                <iframe 
                    allowfullscreen="" 
                    loading="lazy" 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3671.865672045617!2d72.55776!3d23.03386!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x395e84f526ea29d7%3A0x854e4c9f1a07011b!2sC%20G%20Rd%2C%20Navrangpura%2C%20Ahmedabad%2C%20Gujarat!5e0!3m2!1sen!2sin!4v1650000000000!5m2!1sen!2sin">
                </iframe>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Hotel Elite, Ahmedabad. All Rights Reserved.</p>
    </footer>

</body>
</html>