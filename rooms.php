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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rooms & Suites - Hotel Elite</title>
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
            color: #fff;
            padding: 8px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
        }

        .btn-nav-signup {
            background-color: #f59e0b;
            color: #fff;
            padding: 8px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
        }

        .container {
            max-width: 1100px;
            margin: 30px auto;
            padding: 0 20px;
            width: 100%;
            flex: 1;
        }

        .page-title {
            text-align: center;
            color: #111827;
            margin-bottom: 30px;
            font-size: 2rem;
            font-weight: bold;
        }

        .rooms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }

        .room-card {
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s;
        }

        .room-card:hover {
            transform: translateY(-4px);
        }

        .room-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: #e9ecef;
        }

        .room-body {
            padding: 20px;
            text-align: left;
        }

        .room-badge {
            background: #f59e0b;
            color: #fff;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 10px;
        }

        .room-title {
            font-size: 1.25rem;
            color: #111827;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .room-info {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 6px;
        }

        .room-price {
            font-size: 1.3rem;
            color: #10b981;
            font-weight: bold;
            margin: 15px 0;
        }

        .room-btns {
            display: flex;
            gap: 10px;
        }

        .btn {
            flex: 1;
            text-align: center;
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            font-size: 0.9rem;
            transition: background 0.2s;
        }

        .btn-details {
            background: #111827;
            color: #fff;
        }

        .btn-details:hover {
            background: #1f2937;
        }

        .btn-book {
            background: #10b981;
            color: #fff;
        }

        .btn-book:hover {
            background: #059669;
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
                    <li><a href="rooms.php" style="color: #f59e0b; font-weight: bold;">Rooms</a></li>
                    <li><a href="food.php">Food</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <?php if (isset($_SESSION['user_email'])): ?>
                        <li><a href="my_bookings.php">My Booking</a></li>
                        <li><a href="my_orders.php">My Order</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>

        <div class="header-bottom">
            <?php if (isset($_SESSION['user_email'])): ?>
                <a href="logout.php" class="btn-nav-login" style="background: #e74c3c;">Logout</a>
            <?php else: ?>
                <a href="index.php" class="btn-nav-login">Login</a>
                <a href="signup.php" class="btn-nav-signup">Sign Up</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="container">
        <h2 class="page-title">Our Exclusive Rooms & Suites</h2>
        <div class="rooms-grid">
            <?php
            $result = $conn->query("SELECT * FROM rooms ORDER BY id ASC");
            if ($result && $result->num_rows > 0) {
                while ($room = $result->fetch_assoc()) {
                    $bed = !empty($room['bed_type']) ? $room['bed_type'] : 'King Size Bed';
                    $img = !empty($room['image']) ? $room['image'] : 'https://images.unsplash.com/photo-1611892440504-42a792e24d32';
                    
                    if (isset($_SESSION['user_email'])) {
                        $book_link = "booking.php?room_id=" . $room['id'];
                    } else {
                        $book_link = "index.php";
                    }
                    
                    echo '<div class="room-card">
                            <img src="' . $img . '" alt="' . $room['room_type'] . '" class="room-img">
                            <div class="room-body">
                                <span class="room-badge">Room No: ' . $room['room_no'] . '</span>
                                <div class="room-title">' . $room['room_type'] . '</div>
                                <div class="room-info">🛏️ Bed Type: <b>' . $bed . '</b></div>
                                <div class="room-price">₹' . $room['price'] . ' <span style="font-size:12px; color:#6c757d;">/ night</span></div>
                                <div class="room-btns">
                                    <a href="standard-room.php?id=' . $room['id'] . '" class="btn btn-details">View Details</a>
                                    <a href="' . $book_link . '" class="btn btn-book">Book Now</a>
                                </div>
                            </div>
                          </div>';
                }
            } else {
                echo "<p style='text-align:center;'>No rooms found.</p>";
            }
            ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Hotel Elite, Ahmedabad. All Rights Reserved.</p>
    </footer>
</body>
</html>