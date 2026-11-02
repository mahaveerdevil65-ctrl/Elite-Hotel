<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', 1);

session_start();
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Elite - Room Availability</title>
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

        .container {
            max-width: 1100px;
            margin: 30px auto;
            padding: 0 20px;
            width: 100%;
            flex: 1;
        }

        .page-header {
            background: linear-gradient(rgba(13, 27, 42, 0.8), rgba(13, 27, 42, 0.8)), url('https://images.unsplash.com/photo-1566073771259-6a8506099945');
            background-size: cover;
            background-position: center;
            color: #fff;
            padding: 40px 20px;
            text-align: center;
            border-radius: 8px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .page-header h2 {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .badge {
            background: #f39c12;
            color: #fff;
            padding: 6px 16px;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;
            font-size: 0.85rem;
            margin-bottom: 10px;
        }

        .content-box {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            overflow-x: auto;
        }

        .content-box h3 {
            color: #0d1b2a;
            border-bottom: 2px solid #f39c12;
            display: inline-block;
            padding-bottom: 5px;
            margin-bottom: 20px;
            font-size: 1.4rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            text-align: left;
        }

        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #e9ecef;
            font-size: 0.95rem;
        }

        th {
            background-color: #0d1b2a;
            color: #fff;
            text-transform: uppercase;
            font-size: 0.85rem;
            font-weight: 600;
        }

        tr:hover {
            background-color: #f1f4f8;
        }

        .status-available {
            background-color: #28a745;
            color: #fff;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            display: inline-block;
        }

        .status-booked {
            background-color: #dc3545;
            color: #fff;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            display: inline-block;
        }

        footer {
            text-align: center;
            padding: 20px;
            color: #adb5bd;
            background: #0d1b2a;
            border-top: 1px solid #1b263b;
            margin-top: auto;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <header>
        <a href="home.php" class="logo">Hotel Elite</a>
        <nav>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="rooms.php">Rooms</a></li>
                <li><a href="room-availability.php" style="color: #fff; font-weight: bold;">Availability</a></li>
                <li><a href="food.php">Food</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
                
                <?php if (isset($_SESSION['user_email'])): ?>
                    <li><a href="my_bookings.php">My Booking</a></li>
                    <li><a href="my_orders.php">My Order</a></li>
                    <li><a href="logout.php" style="color: #e74c3c; font-weight: bold;">Logout</a></li>
                <?php else: ?>
                    <li><a href="index.php" style="color: #28a745; font-weight: bold;">Login</a></li>
                    <li><a href="signup.php" style="color: #ffc107; font-weight: bold;">Signup</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="page-header">
            <span class="badge">Live Tracking</span>
            <h2>Room Availability Status</h2>
            <p style="margin-top: 10px; color: #cbd5e1; font-size: 1.05rem;">Check real-time room occupancy and availability.</p>
        </div>

        <div class="content-box">
            <h3>All Rooms Status</h3>
            
            <table>
                <thead>
                    <tr>
                        <th>Room No</th>
                        <th>Floor</th>
                        <th>Room Type</th>
                        <th>Price (₹)</th>
                        <th>Status</th>
                        <th>Booking Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT r.*, 
                              CASE WHEN rb.id IS NOT NULL THEN 'Booked' ELSE 'Available' END as booking_status
                              FROM rooms r 
                              LEFT JOIN room_bookings rb ON r.room_no = rb.room_no AND rb.check_out >= CURDATE()
                              ORDER BY r.id DESC";
                    
                    $result = mysqli_query($conn, $query);

                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $badgeClass = ($row['booking_status'] == 'Available') ? 'status-available' : 'status-booked';
                            $rStatus = isset($row['room_status']) ? $row['room_status'] : 'Active';
                            $floor = isset($row['floor']) ? $row['floor'] : '';
                            $price = isset($row['price']) ? $row['price'] : '1800.00';

                            echo "<tr>";
                            echo "<td><strong>" . $row['room_no'] . "</strong></td>";
                            echo "<td>" . $floor . "</td>";
                            echo "<td>" . $row['room_type'] . "</td>";
                            echo "<td>₹" . $price . "</td>";
                            echo "<td>" . $rStatus . "</td>";
                            echo "<td><span class='" . $badgeClass . "'>" . $row['booking_status'] . "</span></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' style='text-align:center;'>No rooms found in database.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Hotel Elite. All Rights Reserved.</p>
    </footer>
</body>
</html>