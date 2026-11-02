<?php
session_start();
include('db.php');

if (!isset($_SESSION['admin']) && !isset($_SESSION['staff_email'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['add_food_item'])) {
    $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
    $price = (float)$_POST['price'];
    
    $conn->query("INSERT INTO food_items (food_name, price) VALUES ('$item_name', $price)");
    header("Location: food_menu.php");
    exit();
}

if (isset($_POST['update_food_price'])) {
    $item_id = (int)$_POST['item_id'];
    $new_price = (float)$_POST['new_price'];
    
    $conn->query("UPDATE food_items SET price = $new_price WHERE id = $item_id");
    header("Location: food_menu.php");
    exit();
}

if (isset($_GET['delete_item'])) {
    $item_id = (int)$_GET['delete_item'];
    $conn->query("DELETE FROM food_items WHERE id = $item_id");
    header("Location: food_menu.php");
    exit();
}

$menu_search = isset($_GET['menu_search']) ? trim($_GET['menu_search']) : "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Food Menu Management - Hotel Elite</title>
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
        }

        .main-wrapper {
            display: flex;
            flex: 1;
        }

        aside {
            width: 240px;
            background: #1b263b;
            padding: 25px 0;
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

        h2, h3 {
            color: #0d1b2a;
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

        .card {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
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
        }

        .btn-delete {
            background: #dc3545;
            color: #fff;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.8rem;
            display: inline-block;
        }

        .btn-update {
            background: #28a745;
            color: #fff;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
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
            min-width: 180px;
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
        }

        .add-form {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }

        .add-form input {
            padding: 10px 12px;
            font-size: 0.9rem;
            border: 1px solid #ced4da;
            border-radius: 5px;
            flex: 1;
            min-width: 200px;
        }

        .add-form button {
            background: #28a745;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">Hotel Elite - Menu Management</div>
        <div class="logout-box"><a href="logout.php">Logout</a></div>
    </header>

    <div class="main-wrapper">
        <aside>
            <ul>
                <li><a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a></li>
                <li><a href="booking_management.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'booking_management.php' ? 'active' : ''; ?>">Bookings</a></li>
                <li><a href="room_management.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'room_management.php' ? 'active' : ''; ?>">Manage Rooms</a></li>
                <li><a href="food_orders.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'food_orders.php' ? 'active' : ''; ?>">Food Orders</a></li>
                <li><a href="food_menu.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'food_menu.php' ? 'active' : ''; ?>">Manage Menu</a></li>
                <li><a href="manage_staff.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_staff.php' ? 'active' : ''; ?>">Manage Staff</a></li>
                <li><a href="users.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">Users</a></li>
                <li><a href="room_reports.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'room_reports.php' ? 'active' : ''; ?>">Room Reports</a></li>
                <li><a href="food_reports.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'food_reports.php' ? 'active' : ''; ?>">Food Reports</a></li>
            </ul>
        </aside>

        <div class="content">
            <div class="top-bar">
                <h2>Add & Update Food Menu Items</h2>
                <a href="dashboard.php" class="btn-back">&larr; Back to Dashboard</a>
            </div>

            <div class="card">
                <h3>Add New Food Item</h3>
                <form action="" method="POST" class="add-form" autocomplete="off">
                    <input type="text" name="item_name" placeholder="Item Name (e.g. Masala Dosa)" required autocomplete="off">
                    <input type="number" step="0.01" name="price" placeholder="Price (₹)" required autocomplete="off">
                    <button type="submit" name="add_food_item">+ Add Item</button>
                </form>

                <h3>Existing Menu Items & Price Updater</h3>
                <form method="GET" action="" class="filter-form" autocomplete="off">
                    <input type="text" name="menu_search" placeholder="Search menu item name..." value="<?php echo htmlspecialchars($menu_search); ?>" autocomplete="off">
                    <button type="submit">Search Item</button>
                    <?php if (!empty($menu_search)): ?>
                        <a href="food_menu.php">Reset</a>
                    <?php endif; ?>
                </form>

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Item Name</th>
                            <th>Current Price (₹)</th>
                            <th>Update Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $menu_query = "SELECT * FROM food_items WHERE 1=1";
                        if (!empty($menu_search)) {
                            $safe_m_search = mysqli_real_escape_string($conn, $menu_search);
                            $menu_query .= " AND food_name LIKE '%$safe_m_search%'";
                        }
                        $menu_query .= " ORDER BY id DESC";

                        $menu_res = $conn->query($menu_query);
                        if ($menu_res && $menu_res->num_rows > 0) {
                            while ($m = $menu_res->fetch_assoc()) {
                                $m_id = $m['id'];
                                $m_name = $m['food_name'] ?? "N/A";
                                $m_price = $m['price'] ?? 0;

                                echo "<tr>";
                                echo "<td><b>#" . $m_id . "</b></td>";
                                echo "<td style='text-align: left; padding-left: 20px;'><b>" . htmlspecialchars($m_name) . "</b></td>";
                                echo "<td><b>₹" . number_format($m_price, 2) . "</b></td>";
                                echo "<td>
                                        <form action='' method='POST' style='margin:0; padding:0; border:none; background:none; display:flex; justify-content:center; gap:8px;' autocomplete='off'>
                                            <input type='hidden' name='item_id' value='" . $m_id . "'>
                                            <input type='number' step='0.01' name='new_price' value='" . $m_price . "' style='width:100px; padding:6px;' required autocomplete='off'>
                                            <button type='submit' name='update_food_price' class='btn-update'>Save</button>
                                        </form>
                                      </td>";
                                echo "<td><a href='food_menu.php?delete_item=" . $m_id . "' class='btn-delete' onclick='return confirm(\"Delete this menu item?\")'>Delete</a></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>No menu items found!</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>