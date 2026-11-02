<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', 1);

session_start();
include('db.php');

if (!isset($_SESSION['user_email']) || empty($_SESSION['user_email'])) {
    header("Location: index.php");
    exit();
}

$logged_in_email = $_SESSION['user_email'];
$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$date_filter = isset($_GET['date_filter']) ? trim($_GET['date_filter']) : "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Hotel Elite</title>
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

        .container {
            max-width: 1200px;
            margin: 30px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            width: 100%;
            flex: 1;
        }

        h2 {
            color: #111827;
            margin-bottom: 5px;
            font-size: 26px;
            font-weight: bold;
        }

        p {
            color: #6c757d;
            margin-bottom: 25px;
            font-size: 0.95rem;
        }

        .filter-form {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .filter-form input {
            flex: 1;
            min-width: 180px;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 0.9rem;
            outline: none;
        }

        .filter-form input:focus {
            border-color: #f59e0b;
        }

        .filter-form button {
            background: #111827;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
        }

        .filter-form button:hover {
            background: #1f2937;
        }

        .filter-form a {
            background: #6c757d;
            color: #fff;
            padding: 10px 16px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            font-size: 0.9rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 25px;
        }

        th, td {
            border: 1px solid #e9ecef;
            padding: 12px 10px;
            text-align: center;
            font-size: 0.9rem;
        }

        th {
            background: #111827;
            color: #fff;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background: #fdfdfe;
        }

        tr:hover {
            background: #f1f4f8;
        }

        .status-btn {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: bold;
            display: inline-block;
            color: #fff;
        }

        .bg-success {
            background: #10b981;
        }

        .bg-warning {
            background: #f59e0b;
            color: #212529;
        }

        .btn-edit {
            background: #f59e0b;
            color: #fff;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            font-size: 0.85rem;
            display: inline-block;
            transition: background 0.2s;
        }

        .btn-edit:hover {
            background: #d97706;
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
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <?php if (isset($_SESSION['user_email'])): ?>
                        <li><a href="my_bookings.php" style="color: #f59e0b; font-weight: bold;">My Booking</a></li>
                        <li><a href="my_orders.php">My Order</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>

        <div class="header-bottom">
            <?php if (isset($_SESSION['user_email'])): ?>
                <a href="logout.php" class="btn-nav-login" style="background: #e74c3c;">Logout</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="container">
        <h2>My Room Bookings History</h2>
        <p>View and filter all your historical and active room reservations below:</p>

        <form method="GET" action="" class="filter-form" autocomplete="off">
            <input type="text" name="search" placeholder="Search by name, email or phone..." value="<?php echo htmlspecialchars($search); ?>">
            <input type="date" name="date_filter" value="<?php echo htmlspecialchars($date_filter); ?>">
            <button type="submit">Filter Rooms</button>
            <?php if (!empty($search) || !empty($date_filter)): ?>
                <a href="my_bookings.php">Reset</a>
            <?php endif; ?>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Room Name</th>
                    <th>Room No</th>
                    <th>Guest Name</th>
                    <th>Phone / Email</th>
                    <th>Check-In</th>
                    <th>Check-Out</th>
                    <th>Total Bill</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT b.*, r.room_type, r.room_no as db_room_no FROM room_bookings b LEFT JOIN rooms r ON b.room_id = r.id WHERE (b.user_email = '$logged_in_email' OR b.user_name LIKE '%$logged_in_email%')";

                if (!empty($search)) {
                    $safe_search = mysqli_real_escape_string($conn, $search);
                    $query .= " AND (b.user_name LIKE '%$safe_search%' OR b.user_email LIKE '%$safe_search%' OR b.phone LIKE '%$safe_search%')";
                }

                if (!empty($date_filter)) {
                    $safe_date = mysqli_real_escape_string($conn, $date_filter);
                    $query .= " AND (b.check_in LIKE '%$safe_date%' OR b.check_out LIKE '%$safe_date%')";
                }

                $query .= " ORDER BY b.id DESC";
                $result = $conn->query($query);

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $r_name = $row['room_type'] ?? 'Standard Room';
                        $r_no = !empty($row['room_no']) ? $row['room_no'] : ($row['db_room_no'] ?? 'N/A');
                        $p_status = $row['payment_status'] ?? 'Pending';
                        $status_class = (stripos($p_status, 'Paid') !== false || stripos($p_status, 'Confirmed') !== false) ? 'bg-success' : 'bg-warning';

                        echo "<tr>";
                        echo "<td><b>#" . $row['id'] . "</b></td>";
                        echo "<td>" . htmlspecialchars($r_name) . "</td>";
                        echo "<td><b style='color:#f59e0b;'>" . htmlspecialchars($r_no) . "</b></td>";
                        echo "<td>" . htmlspecialchars($row['user_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['phone']) . "<br><small style='color:#6c757d;'>" . htmlspecialchars($row['user_email']) . "</small></td>";
                        echo "<td>" . htmlspecialchars($row['check_in']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['check_out']) . "</td>";
                        echo "<td><b>₹" . number_format($row['total_price'], 2) . "</b></td>";
                        echo "<td><span class='status-btn $status_class'>" . htmlspecialchars($p_status) . "</span></td>";
                        echo "<td><a href='booking.php?edit=" . $row['id'] . "' class='btn-edit'>Edit</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='10'>No room bookings found!</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <footer>
        <p>&copy; 2026 Hotel Elite, Ahmedabad. All Rights Reserved.</p>
    </footer>
</body>
</html>