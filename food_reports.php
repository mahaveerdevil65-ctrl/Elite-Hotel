<?php
session_start();
include('db.php');

if (!isset($_SESSION['admin']) && !isset($_SESSION['staff_email'])) {
    header("Location: login.php");
    exit();
}

$food_search = isset($_GET['food_search']) ? trim($_GET['food_search']) : "";
$food_month = isset($_GET['food_month']) ? trim($_GET['food_month']) : "";

$base_query = "FROM food_orders WHERE 1=1";

if (!empty($food_search)) {
    $safe_f_search = mysqli_real_escape_string($conn, $food_search);
    $base_query .= " AND (customer_name LIKE '%$safe_f_search%' OR room_no LIKE '%$safe_f_search%' OR items LIKE '%$safe_f_search%')";
}

if (!empty($food_month)) {
    $safe_month = mysqli_real_escape_string($conn, $food_month);
    $base_query .= " AND order_date LIKE '$safe_month%'";
}

$f_rev_sql = "SELECT SUM(total_amount) as total_food_amt " . $base_query;
$food_total_query = $conn->query($f_rev_sql);
$food_row = $food_total_query->fetch_assoc();
$grand_food_income = $food_row['total_food_amt'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Food Orders Report - Hotel Management</title>
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
            border-left: 5px solid #28a745;
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

        .badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: bold;
            color: #fff;
            background: #28a745;
            display: inline-block;
        }

        @media print {
            header, aside, .top-bar, .filter-form, .btn-back, .btn-print {
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
                <li><a href="room_reports.php">Room Reports</a></li>
                <li><a href="food_reports.php" class="active">Food Reports</a></li>
            </ul>
        </aside>

        <div class="content">
            <div class="top-bar">
                <h2>Food Orders Financial Report</h2>
                <div style="display: flex; gap: 10px;">
                    <button class="btn-print" onclick="window.print()">Print Report</button>
                    <a href="dashboard.php" class="btn-back">&larr; Back to Dashboard</a>
                </div>
            </div>

            <div class="stat-card">
                <h4>Total Food Orders Revenue</h4>
                <p>₹<?php echo number_format($grand_food_income, 2); ?></p>
            </div>

            <div class="report-section" style="padding: 20px 25px; margin-bottom: 25px;">
                <h4 style="color: #0d1b2a; margin-bottom: 15px; font-size: 1.1rem;">Month-wise Summary</h4>
                <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <?php
                    $summary_sql = "SELECT DATE_FORMAT(order_date, '%Y-%m') as order_month, SUM(total_amount) as monthly_total, COUNT(*) as total_orders " . $base_query . " GROUP BY order_month ORDER BY order_month DESC";
                    $summary_res = $conn->query($summary_sql);
                    if ($summary_res && $summary_res->num_rows > 0) {
                        while ($s_row = $summary_res->fetch_assoc()) {
                            if (!empty($s_row['order_month'])) {
                                $m_name = date('F Y', strtotime($s_row['order_month'] . '-01'));
                                echo "<div style='background: #f8f9fa; border: 1px solid #e9ecef; padding: 12px 18px; border-radius: 6px; min-width: 180px;'>
                                        <div style='font-size: 0.85rem; color: #6c757d;'>" . $m_name . "</div>
                                        <div style='font-size: 1.15rem; font-weight: bold; color: #28a745;'>₹" . number_format($s_row['monthly_total'], 2) . "</div>
                                        <div style='font-size: 0.75rem; color: #495057;'>Orders: " . $s_row['total_orders'] . "</div>
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
                <h3>Food Orders History</h3>
                
                <form method="GET" action="" class="filter-form">
                    <input type="text" name="food_search" placeholder="Search name, room no, items..." value="<?php echo htmlspecialchars($food_search); ?>">
                    <input type="month" name="food_month" value="<?php echo htmlspecialchars($food_month); ?>">
                    <button type="submit">Filter Report</button>
                    <?php if (!empty($food_search) || !empty($food_month)): ?>
                        <a href="food_reports.php">Reset</a>
                    <?php endif; ?>
                </form>

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer Name</th>
                            <th>Room No</th>
                            <th>Ordered Items</th>
                            <th>Payment Mode</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $food_query = "SELECT * " . $base_query . " ORDER BY id DESC";

                        $f_result = $conn->query($food_query);
                        if ($f_result && $f_result->num_rows > 0) {
                            while ($row = $f_result->fetch_assoc()) {
                                $c_name = $row['customer_name'] ?? "N/A";
                                $r_no = $row['room_no'] ?? "N/A";
                                $items = $row['items'] ?? "N/A";
                                $pay = $row['payment_mode'] ?? "Cash";
                                $total = $row['total_amount'] ?? 0;
                                $status = $row['status'] ?? "Delivered";

                                echo "<tr>";
                                echo "<td>#" . htmlspecialchars($row['id']) . "</td>";
                                echo "<td>" . htmlspecialchars($c_name) . "</td>";
                                echo "<td>" . htmlspecialchars($r_no) . "</td>";
                                echo "<td style='text-align: left;'><small>" . htmlspecialchars($items) . "</small></td>";
                                echo "<td>" . htmlspecialchars($pay) . "</td>";
                                echo "<td><b>₹" . number_format($total, 2) . "</b></td>";
                                echo "<td><span class='badge'>" . htmlspecialchars($status) . "</span></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>No food orders found matching your filter!</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>