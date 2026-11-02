<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', 1);

session_start();
include('db.php');

if (!isset($_SESSION['user_email']) || empty($_SESSION['user_email'])) {
    header("Location: index.php");
    exit();
}

$logged_in_name = $_SESSION['user_name'] ?? '';
$food_search = isset($_GET['food_search']) ? trim($_GET['food_search']) : "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Hotel Elite</title>
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
            max-width: 1100px;
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
            background: #10b981;
            color: #fff;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: bold;
            display: inline-block;
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
                        <li><a href="my_bookings.php">My Booking</a></li>
                        <li><a href="my_orders.php" style="color: #f59e0b; font-weight: bold;">My Order</a></li>
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
        <h2>My Food Orders History</h2>
        <p>View and track all your food and dining orders placed at Hotel Elite:</p>

        <form method="GET" action="" class="filter-form" autocomplete="off">
            <input type="text" name="food_search" placeholder="Search by customer name or room no..." value="<?php echo htmlspecialchars($food_search); ?>" autocomplete="off">
            <button type="submit">Filter Food</button>
            <?php if (!empty($food_search)): ?>
                <a href="my_orders.php">Reset</a>
            <?php endif; ?>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Room No</th>
                    <th>Items</th>
                    <th>Total Amount</th>
                    <th>Payment Mode</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $food_query = "SELECT * FROM food_orders WHERE 1=1";

                if (!empty($logged_in_name)) {
                    $safe_food_name = mysqli_real_escape_string($conn, $logged_in_name);
                    $food_query .= " AND customer_name LIKE '%$safe_food_name%'";
                }

                if (!empty($food_search)) {
                    $safe_f_search = mysqli_real_escape_string($conn, $food_search);
                    $food_query .= " AND (customer_name LIKE '%$safe_f_search%' OR room_no LIKE '%$safe_f_search%')";
                }

                $food_query .= " ORDER BY id DESC";
                $food_result = $conn->query($food_query);

                if ($food_result && $food_result->num_rows > 0) {
                    while ($frow = $food_result->fetch_assoc()) {
                        $c_name = $frow['customer_name'] ?? "N/A";
                        echo "<tr>";
                        echo "<td><b>#" . $frow['id'] . "</b></td>";
                        echo "<td>" . htmlspecialchars($c_name) . "</td>";
                        echo "<td>" . htmlspecialchars($frow['room_no'] ?? 'N/A') . "</td>";
                        echo "<td style='text-align: left;'>" . htmlspecialchars($frow['items'] ?? '') . "</td>";
                        echo "<td><b>₹" . number_format(($frow['total_amount'] ?? 0), 2) . "</b></td>";
                        echo "<td>" . htmlspecialchars($frow['payment_mode'] ?? 'Cash') . "</td>";
                        echo "<td><span class='status-btn'>" . htmlspecialchars($frow['status'] ?? 'Ordered') . "</span></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No food orders found!</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <footer>
        <p>&copy; 2026 Hotel Elite. All Rights Reserved.</p>
    </footer>
</body>
</html>