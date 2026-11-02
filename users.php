<?php
session_start();
include('db.php');

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$filter_date = isset($_GET['filter_date']) ? trim($_GET['filter_date']) : "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registered Users - Admin Dashboard</title>
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
            transition: background 0.2s;
        }

        .btn-back:hover {
            background: #5a6268;
        }

        .card { 
            background: #fff; 
            padding: 25px; 
            border-radius: 8px; 
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); 
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
            min-width: 200px; 
        }

        .filter-form input:focus {
            border-color: #007bff;
            outline: none;
        }

        .filter-form button { 
            background: #007bff; 
            color: #fff; 
            border: none; 
            padding: 10px 20px; 
            font-weight: bold; 
            border-radius: 5px; 
            cursor: pointer; 
            transition: background 0.2s;
        }

        .filter-form button:hover {
            background: #0056b3;
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
    </style>
</head>
<body>
    <header>
        <div class="logo">Hotel Elite - Admin</div>
        <div class="logout-box"><a href="logout.php">Logout</a></div>
    </header>

    <div class="main-wrapper">
        <aside>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="booking_management.php">Bookings</a></li>
                <li><a href="room_management.php">Manage Rooms</a></li>
                <li><a href="food_orders.php">Food Orders</a></li>
                <li><a href="food_menu.php">Manage Menu</a></li>
                <li><a href="manage_staff.php">Staff Details</a></li>
                <li><a href="users.php" class="active">Users</a></li>
                <li><a href="room_reports.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'room_reports.php' ? 'active' : ''; ?>">Room Reports</a></li>
                <li><a href="food_reports.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'food_reports.php' ? 'active' : ''; ?>">Food Reports</a></li>
            </ul>
        </aside>

        <div class="content">
            <div class="top-bar">
                <h2>Registered Users List</h2>
                <a href="dashboard.php" class="btn-back">&larr; Back to Dashboard</a>
            </div>

            <div class="card">
                <form method="GET" action="" class="filter-form" autocomplete="off">
                    <input type="text" name="search" placeholder="Search by name or email..." value="<?php echo htmlspecialchars($search); ?>" autocomplete="off">
                    <input type="date" name="filter_date" value="<?php echo htmlspecialchars($filter_date); ?>" autocomplete="off">
                    <button type="submit">Filter</button>
                    <?php if (!empty($search) || !empty($filter_date)): ?>
                        <a href="users.php">Reset</a>
                    <?php endif; ?>
                </form>

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User Name</th>
                            <th>Email Address</th>
                            <th>Last Login Activity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $u_query = "SELECT * FROM users WHERE 1=1";
                        if (!empty($search)) {
                            $safe_search = mysqli_real_escape_string($conn, $search);
                            $u_query .= " AND (name LIKE '%$safe_search%' OR email LIKE '%$safe_search%')";
                        }
                        if (!empty($filter_date)) {
                            $safe_date = mysqli_real_escape_string($conn, $filter_date);
                            $u_query .= " AND last_login LIKE '$safe_date%'";
                        }
                        $u_query .= " ORDER BY id DESC";

                        $res = $conn->query($u_query);
                        if ($res && $res->num_rows > 0) {
                            while ($row = $res->fetch_assoc()) {
                                $last_login = !empty($row['last_login']) ? $row['last_login'] : "Never Logged In";
                                echo "<tr>";
                                echo "<td><b>#" . $row['id'] . "</b></td>";
                                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                echo "<td>" . htmlspecialchars($last_login) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No users found!</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>