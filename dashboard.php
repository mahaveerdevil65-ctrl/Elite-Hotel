<?php
session_start();
include('db.php');

$success_msg = "";
$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email']) && isset($_POST['password'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $staff_result = $conn->query("SELECT * FROM staff WHERE email = '$email'");
    if ($staff_result && $staff_result->num_rows > 0) {
        $staff = $staff_result->fetch_assoc();
        if ($password === $staff['password'] || password_verify($password, $staff['password'])) {
            $_SESSION['staff_email'] = $staff['email'];
            $_SESSION['staff_name'] = $staff['name'];
            $_SESSION['staff_role'] = $staff['role'];
        }
    } else {
        $admin_result = $conn->query("SELECT * FROM admin");
        if ($admin_result && $admin_result->num_rows > 0) {
            while ($admin = $admin_result->fetch_assoc()) {
                $db_user = $admin['email'] ?? $admin['username'] ?? $admin['user'] ?? '';
                $db_pass = $admin['password'] ?? '';
                if ($email === $db_user && $password === $db_pass) {
                    $_SESSION['admin'] = $db_user;
                }
            }
        }
    }
}

$is_admin = isset($_SESSION['admin']);
$is_staff = isset($_SESSION['staff_email']);

if (!$is_admin && !$is_staff) {
    header("Location: login.php");
    exit();
}

$staff_role = "";
$staff_data = null;
if ($is_staff) {
    $s_email = $_SESSION['staff_email'];
    $staff_query = $conn->prepare("SELECT * FROM staff WHERE email = ?");
    $staff_query->bind_param("s", $s_email);
    $staff_query->execute();
    $staff_data = $staff_query->get_result()->fetch_assoc();
    $staff_role = $staff_data['role'] ?? '';
}

$total_bookings = 0;
$total_revenue = 0;
$food_count = 0;
$total_users = 0;

if ($is_admin) {
    $booking_count_query = $conn->query("SELECT COUNT(*) as total FROM room_bookings");
    if ($booking_count_query) { $total_bookings = $booking_count_query->fetch_assoc()['total'] ?? 0; }

    $revenue_query = $conn->query("SELECT SUM(total_price) as total_rev FROM room_bookings");
    if ($revenue_query) { $total_revenue = $revenue_query->fetch_assoc()['total_rev'] ?? 0; }

    $food_query = $conn->query("SELECT COUNT(*) as total FROM food_orders");
    if ($food_query) { $food_count = $food_query->fetch_assoc()['total'] ?? 0; }

    $user_count_query = $conn->query("SELECT COUNT(*) as total FROM users");
    if ($user_count_query) { $total_users = $user_count_query->fetch_assoc()['total'] ?? 0; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $is_admin ? 'Admin Dashboard' : 'Staff Portal'; ?> - Hotel Elite</title>
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
        }

        .logout-box a {
            color: #e74c3c;
            text-decoration: none;
            font-weight: bold;
            font-size: 0.95rem;
            transition: color 0.2s;
        }

        .logout-box a:hover {
            color: #c0392b;
        }

        .main-wrapper {
            display: flex;
            flex: 1;
        }

        aside {
            width: 240px;
            background: #1b263b;
            padding: 25px 0;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
        }

        aside ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        aside ul li a {
            display: block;
            color: #adb5bd;
            text-decoration: none;
            padding: 12px 25px;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.2s ease-in-out;
        }

        aside ul li a:hover, 
        aside ul li a.active {
            background: #007bff;
            color: #fff;
            border-left: 5px solid #f39c12;
        }

        .content {
            flex: 1;
            max-width: 1100px;
            margin: 30px auto;
            padding: 0 20px;
            width: 100%;
        }

        h2 {
            color: #0d1b2a;
            margin-bottom: 20px;
            font-size: 1.8rem;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: #fff;
            padding: 22px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            text-align: center;
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-3px);
        }

        .card h3 {
            margin: 0 0 10px 0;
            color: #6c757d;
            font-size: 0.95rem;
        }

        .card p {
            margin: 0;
            font-size: 1.8rem;
            font-weight: bold;
            color: #0d1b2a;
        }

        .card a {
            display: block;
            margin-top: 15px;
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
            font-size: 0.9rem;
            transition: color 0.2s;
        }

        .card a:hover {
            color: #0056b3;
        }

        .table-container {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .table-container h3 {
            margin-top: 0;
            color: #0d1b2a;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #e9ecef;
            padding: 12px 10px;
            text-align: center;
            font-size: 0.9rem;
        }

        th {
            background: #007bff;
            color: #fff;
            font-weight: 600;
        }

        tr:nth-child(even) {
            background: #fdfdfe;
        }

        tr:hover {
            background: #f1f4f8;
        }

        .status-badge {
            background: #28a745;
            color: #fff;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: bold;
            display: inline-block;
        }

        .badge {
            background: #007bff;
            color: #fff;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: bold;
            display: inline-block;
        }
    </style>
</head>
<body>

    <header>
        <div class="logo">Hotel Elite - <?php echo $is_admin ? 'Admin Dashboard' : 'Staff Portal'; ?></div>
        <div class="logout-box">
            <a href="logout.php">Logout</a>
        </div>
    </header>

    <div class="main-wrapper">
        <aside>
            <ul>
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <?php if ($is_admin): ?>
                    <li><a href="booking_management.php">Bookings</a></li>
                    <li><a href="room_management.php">Manage Rooms</a></li>
                    <li><a href="food_orders.php">Food Orders</a></li>
                    <li><a href="food_menu.php">Manage Menu</a></li>
                    <li><a href="manage_staff.php">Staff Details</a></li>
                    <li><a href="users.php">Users</a></li>
                    <li><a href="room_reports.php">Room Reports</a></li>
                    <li><a href="food_reports.php">Food Reports</a></li>
                <?php else: ?>
                    <li><a href="booking_management.php">Manage Bookings</a></li>
                    <li><a href="food_orders.php">Manage Food Orders</a></li>
                <?php endif; ?>
            </ul>
        </aside>

        <div class="content">
            <?php if ($is_admin): ?>
                <h2>Welcome, Admin!</h2>
                
                <div class="cards">
                    <div class="card">
                        <h3>Total Room Bookings</h3>
                        <p><?php echo $total_bookings; ?></p>
                        <a href="booking_management.php">Manage Bookings &rarr;</a>
                    </div>
                    <div class="card">
                        <h3>Total Revenue</h3>
                        <p>₹<?php echo number_format($total_revenue, 2); ?></p>
                        <a href="room_reports.php">View Reports &rarr;</a>
                    </div>
                    <div class="card">
                        <h3>Total Food Orders</h3>
                        <p><?php echo $food_count; ?></p>
                        <a href="food_orders.php">Manage Food Orders &rarr;</a>
                    </div>
                    <div class="card">
                        <h3>Total Registered Users</h3>
                        <p><?php echo $total_users; ?></p>
                        <a href="users.php">View Details &rarr;</a>
                    </div>
                </div>

                <div class="table-container">
                    <h3>Recent Room Bookings</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer Name</th>
                                <th>Room Type</th>
                                <th>Phone</th>
                                <th>Check-In & Check-Out</th>
                                <th>Amount</th>
                                <th>Payment Mode</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $recent_query = "SELECT b.*, r.room_type FROM room_bookings b LEFT JOIN rooms r ON b.room_id = r.id ORDER BY b.id DESC LIMIT 5";
                            $recent_result = $conn->query($recent_query);
                            
                            if ($recent_result && $recent_result->num_rows > 0) {
                                while ($row = $recent_result->fetch_assoc()) {
                                    $c_name = !empty($row['user_name']) ? $row['user_name'] : "N/A";
                                    $r_type = !empty($row['room_type']) ? $row['room_type'] : "Standard Room";
                                    $phone = !empty($row['phone']) ? $row['phone'] : "N/A";
                                    $dates = (!empty($row['check_in']) && !empty($row['check_out'])) ? $row['check_in'] . "<br><b>to</b><br>" . $row['check_out'] : "N/A";
                                    $amount = !empty($row['total_price']) ? "₹" . number_format($row['total_price'], 2) : "₹0.00";
                                    $pay_mode = !empty($row['payment_method']) ? $row['payment_method'] : "Cash";
                                    $pay_status = !empty($row['payment_status']) ? $row['payment_status'] : "Confirmed";

                                    echo "<tr>";
                                    echo "<td><b>#" . $row['id'] . "</b></td>";
                                    echo "<td>" . $c_name . "</td>";
                                    echo "<td><b>" . $r_type . "</b></td>";
                                    echo "<td>" . $phone . "</td>";
                                    echo "<td><small>" . $dates . "</small></td>";
                                    echo "<td><b>" . $amount . "</b></td>";
                                    echo "<td>" . $pay_mode . "</td>";
                                    echo "<td><span class='status-badge'>" . $pay_status . "</span></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8'>No recent bookings found!</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

            <?php else: ?>
                <h2>Welcome, <?php echo htmlspecialchars($staff_data['name']); ?>!</h2>
                
                <div class="card" style="text-align: left; margin-bottom: 25px; padding: 25px;">
                    <h3>Staff Profile & Assigned Duties</h3>
                    <p style="margin: 12px 0;"><b>Designation / Role:</b> <span class="badge"><?php echo htmlspecialchars($staff_data['role']); ?></span></p>
                    <p style="margin: 12px 0;"><b>Assigned Work:</b> <?php echo htmlspecialchars($staff_data['work_assigned']); ?></p>
                    <p style="margin: 12px 0;"><b>Status:</b> <span style="color: #28a745; font-weight: bold;"><?php echo htmlspecialchars($staff_data['status']); ?></span></p>
                </div>

                <div class="table-container">
                    <h3>Quick Operations</h3>
                    <p style="color: #6c757d; margin-bottom: 15px;">Your Roll Will be select!</p>
                    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                        <a href="booking_management.php" style="background: #007bff; color: #fff; padding: 12px 22px; text-decoration: none; border-radius: 5px; font-weight: bold; transition: background 0.2s;">Manage Room Bookings</a>
                        <a href="food_orders.php" style="background: #28a745; color: #fff; padding: 12px 22px; text-decoration: none; border-radius: 5px; font-weight: bold; transition: background 0.2s;">Manage Food Orders</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>