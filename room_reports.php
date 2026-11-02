<?php
session_start();
include('db.php');

if (!isset($_SESSION['admin']) && !isset($_SESSION['staff_email'])) {
    header("Location: login.php");
    exit();
}

$room_search = isset($_GET['room_search']) ? trim($_GET['room_search']) : "";
$room_month = isset($_GET['room_month']) ? trim($_GET['room_month']) : "";

$base_query = "FROM room_bookings rb LEFT JOIN rooms r ON rb.room_id = r.id WHERE 1=1";

if (!empty($room_search)) {
    $safe_r_search = mysqli_real_escape_string($conn, $room_search);
    $base_query .= " AND (rb.user_name LIKE '%$safe_r_search%' OR r.room_no LIKE '%$safe_r_search%' OR r.floor LIKE '%$safe_r_search%')";
}

if (!empty($room_month)) {
    $safe_month = mysqli_real_escape_string($conn, $room_month);
    $base_query .= " AND rb.check_in LIKE '$safe_month%'";
}

$b_rev_sql = "SELECT SUM(rb.total_price) as total_booking_amt " . $base_query;
$booking_total_query = $conn->query($b_rev_sql);
$booking_row = $booking_total_query->fetch_assoc();
$grand_booking_income = $booking_row['total_booking_amt'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Room Bookings Report - Hotel Management</title>
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

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        h2 {
            color: #0d1b2a;
            font-size: 1.8rem;
        }

        .btn-back {
            background: #6c757d;
            color: #fff;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .btn-print {
            background: #28a745;
            color: #fff;
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 0.9rem;
            cursor: pointer;
            text-decoration: none;
        }

        .stat-card {
            background: #fff;
            padding: 22px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border-left: 5px solid #007bff;
            margin-bottom: 25px;
            max-width: 350px;
        }

        .stat-card h4 {
            margin: 0 0 10px 0;
            color: #6c757d;
            font-size: 0.95rem;
        }

        .stat-card p {
            margin: 0;
            font-size: 1.6rem;
            font-weight: bold;
            color: #0d1b2a;
        }

        .report-section {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .report-section h3 {
            margin-top: 0;
            color: #0d1b2a;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .filter-form {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-form input {
            padding: 10px 12px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 0.9rem;
            flex: 1;
            min-width: 160px;
        }

        .filter-form button {
            background: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
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

        @media print {
            header, aside, .top-bar, .filter-form, .btn-back, .btn-print, .report-section:nth-of-type(1) {
                display: none !important;
            }
            .report-section {
                box-shadow: none;
                border: none;
                margin: 0;
                padding: 0;
            }
            body {
                background: #fff;
            }
            .main-wrapper {
                display: block;
            }
            .content {
                margin: 0;
                padding: 0;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">Hotel Elite - Management</div>
        <div class="logout-box"><a href="login.php">Logout</a></div>
    </header>

    <div class="main-wrapper">
       <aside>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="booking_management.php">Bookings</a></li>
                <li><a href="room_management.php">Manage Rooms</a></li>
                <li><a href="food_orders.php">Food Orders</a></li>
                <li><a href="food_menu.php">Manage Menu</a></li>
                <li><a href="manage_staff.php">Manage Staff</a></li>
                <li><a href="users.php">Users</a></li>
                <li><a href="room_reports.php" class="active">Room Reports</a></li>
                <li><a href="food_reports.php">Food Reports</a></li>
            </ul>
        </aside>

        <div class="content">
            <div class="top-bar">
                <h2>Room Bookings Financial Report</h2>
                <div style="display: flex; gap: 10px;">
                    <button class="btn-print" onclick="window.print()">Print Report</button>
                    <a href="dashboard.php" class="btn-back">&larr; Back to Dashboard</a>
                </div>
            </div>

            <div class="stat-card">
                <h4>Total Room Bookings Revenue</h4>
                <p>₹<?php echo number_format($grand_booking_income, 2); ?></p>
            </div>

            <div class="report-section" style="padding: 20px 25px; margin-bottom: 25px;">
                <h4 style="color: #0d1b2a; margin-bottom: 15px; font-size: 1.1rem;">Month-wise Summary</h4>
                <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <?php
                    $summary_sql = "SELECT DATE_FORMAT(rb.check_in, '%Y-%m') as booking_month, SUM(rb.total_price) as monthly_total, COUNT(*) as total_bookings " . $base_query . " GROUP BY booking_month ORDER BY booking_month DESC";
                    $summary_res = $conn->query($summary_sql);
                    if ($summary_res && $summary_res->num_rows > 0) {
                        while ($s_row = $summary_res->fetch_assoc()) {
                            if (!empty($s_row['booking_month'])) {
                                $m_name = date('F Y', strtotime($s_row['booking_month'] . '-01'));
                                echo "<div style='background: #f8f9fa; border: 1px solid #e9ecef; padding: 12px 18px; border-radius: 6px; min-width: 180px;'>
                                        <div style='font-size: 0.85rem; color: #6c757d;'>" . $m_name . "</div>
                                        <div style='font-size: 1.15rem; font-weight: bold; color: #007bff;'>₹" . number_format($s_row['monthly_total'], 2) . "</div>
                                        <div style='font-size: 0.75rem; color: #495057;'>Bookings: " . $s_row['total_bookings'] . "</div>
                                      </div>";
                            }
                        }
                    } else {
                        echo "<p style='font-size: 0.9rem; color: #6c757d;'>No monthly data available.</p>";
                    }
                    ?>
                </div>
            </div>

            <div class="report-section">
                <h3>Room Bookings History</h3>
                
                <form method="GET" action="" class="filter-form">
                    <input type="text" name="room_search" placeholder="Search user name, room no, floor..." value="<?php echo htmlspecialchars($room_search); ?>">
                    <input type="month" name="room_month" value="<?php echo htmlspecialchars($room_month); ?>">
                    <button type="submit">Filter Report</button>
                    <?php if (!empty($room_search) || !empty($room_month)): ?>
                        <a href="room_reports.php">Reset</a>
                    <?php endif; ?>
                </form>

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer Name</th>
                            <th>Room No</th>
                            <th>Floor</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Total Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $booking_query = "SELECT rb.*, r.room_no, r.floor " . $base_query . " ORDER BY rb.id DESC";
                        
                        $b_result = $conn->query($booking_query);
                        if ($b_result && $b_result->num_rows > 0) {
                            while ($row = $b_result->fetch_assoc()) {
                                $name = $row['user_name'] ?? "N/A";
                                $room_no = $row['room_no'] ?? "N/A";
                                $floor = $row['floor'] ?? "-";
                                $in = $row['check_in'] ?? "N/A";
                                $out = $row['check_out'] ?? "N/A";
                                $price = $row['total_price'] ?? 0;

                                echo "<tr>";
                                echo "<td>#" . htmlspecialchars($row['id']) . "</td>";
                                echo "<td>" . htmlspecialchars($name) . "</td>";
                                echo "<td><b>" . htmlspecialchars($room_no) . "</b></td>";
                                echo "<td>" . htmlspecialchars($floor) . "</td>";
                                echo "<td>" . htmlspecialchars($in) . "</td>";
                                echo "<td>" . htmlspecialchars($out) . "</td>";
                                echo "<td><b>₹" . number_format($price, 2) . "</b></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>No room bookings found matching your filter!</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>