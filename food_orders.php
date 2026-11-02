<?php
session_start();
include('db.php');

if (!isset($_SESSION['admin']) && !isset($_SESSION['staff_email'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['update_order'])) {
    $id = (int)$_POST['order_id'];
    $c_name = $_POST['customer_name'];
    $r_no = $_POST['room_no'];
    $items = $_POST['items'];
    $total = $_POST['total_amount'];
    $pay_mode = $_POST['payment_mode'];
    $pay_status = $_POST['payment_status'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE food_orders SET customer_name = ?, room_no = ?, items = ?, total_amount = ?, payment_mode = ?, payment_status = ?, status = ? WHERE id = ?");
    $stmt->bind_param("sssdsssi", $c_name, $r_no, $items, $total, $pay_mode, $pay_status, $status, $id);
    $stmt->execute();
    header("Location: food_orders.php");
    exit();
}

if (isset($_GET['delete_order'])) {
    $id = (int)$_GET['delete_order'];
    $conn->query("DELETE FROM food_orders WHERE id = $id");
    header("Location: food_orders.php");
    exit();
}

$search_query = isset($_GET['search']) ? trim($_GET['search']) : "";
$status_filter = isset($_GET['status_filter']) ? trim($_GET['status_filter']) : "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Food Orders - Hotel Elite</title>
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

        .badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: bold;
            color: #fff;
            display: inline-block;
        }

        .badge-ordered {
            background: #ffc107;
            color: #000;
        }

        .badge-preparing {
            background: #17a2b8;
        }

        .badge-delivered {
            background: #28a745;
        }

        .badge-paid {
            background: #28a745;
        }

        .badge-pending {
            background: #dc3545;
        }

        .btn-delete {
            background: #dc3545;
            color: #fff;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.8rem;
            display: inline-block;
        }

        .btn-edit {
            background: #ffc107;
            color: #212529;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.8rem;
            cursor: pointer;
            display: inline-block;
            margin-bottom: 4px;
        }

        .filter-form {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-form input, 
        .filter-form select {
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
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.2);
        }

        .form-group {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            text-align: left;
        }

        .form-group label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #495057;
        }

        .form-group input, 
        .form-group select {
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 0.95rem;
        }

        .modal-btns {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-save {
            background: #28a745;
            color: #fff;
            border: none;
            padding: 10px 18px;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-cancel {
            background: #6c757d;
            color: #fff;
            border: none;
            padding: 10px 18px;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">Hotel Elite - Food Orders</div>
        <div class="logout-box"><a href="login.php">Logout</a></div>
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
                <h2>Customer Food Orders List</h2>
                <a href="dashboard.php" class="btn-back">&larr; Back to Dashboard</a>
            </div>

            <div class="card">
                <form method="GET" action="" class="filter-form" autocomplete="off">
                    <input type="text" name="search" placeholder="Search name, room, items..." value="<?php echo htmlspecialchars($search_query); ?>" autocomplete="off">
                    
                    <select name="status_filter">
                        <option value="">-- Filter by Status --</option>
                        <option value="Ordered" <?php if ($status_filter == 'Ordered') echo 'selected'; ?>>Ordered</option>
                        <option value="Preparing" <?php if ($status_filter == 'Preparing') echo 'selected'; ?>>Preparing</option>
                        <option value="Delivered" <?php if ($status_filter == 'Delivered') echo 'selected'; ?>>Delivered</option>
                    </select>

                    <button type="submit">Apply Filter</button>
                    <?php if (!empty($search_query) || !empty($status_filter)): ?>
                        <a href="food_orders.php">Reset</a>
                    <?php endif; ?>
                </form>

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer Name</th>
                            <th>Room No</th>
                            <th>Ordered Items</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Food Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT * FROM food_orders WHERE 1=1";
                        if (!empty($search_query)) {
                            $safe_search = mysqli_real_escape_string($conn, $search_query);
                            $query .= " AND (customer_name LIKE '%$safe_search%' OR room_no LIKE '%$safe_search%' OR items LIKE '%$safe_search%')";
                        }
                        if (!empty($status_filter)) {
                            $safe_status = mysqli_real_escape_string($conn, $status_filter);
                            $query .= " AND status = '$safe_status'";
                        }
                        $query .= " ORDER BY id DESC";
                        $result = $conn->query($query);

                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $id = $row['id'];
                                $c_name = $row['customer_name'] ?? $row['user_name'] ?? "N/A";
                                $r_no = $row['room_no'] ?? "N/A";
                                $items = $row['items'] ?? $row['food_name'] ?? "N/A";
                                $total = $row['total_amount'] ?? $row['total_price'] ?? 0;
                                $pay_mode = $row['payment_mode'] ?? "Cash";
                                $pay_status = $row['payment_status'] ?? "Pending";
                                $status = $row['status'] ?? "Ordered";

                                $status_class = "badge-ordered";
                                if (strtolower($status) == 'preparing') $status_class = "badge-preparing";
                                if (strtolower($status) == 'delivered') $status_class = "badge-delivered";
                                $pay_class = strtolower($pay_status) == 'paid' ? "badge-paid" : "badge-pending";

                                echo "<tr>";
                                echo "<td><b>#" . $id . "</b></td>";
                                echo "<td>" . htmlspecialchars($c_name) . "</td>";
                                echo "<td><b>" . htmlspecialchars($r_no) . "</b></td>";
                                echo "<td style='text-align: left;'><small>" . htmlspecialchars($items) . "</small></td>";
                                echo "<td><b>₹" . number_format($total, 2) . "</b><br><small>(" . htmlspecialchars($pay_mode) . ")</small></td>";
                                echo "<td><span class='badge " . $pay_class . "'>" . htmlspecialchars($pay_status) . "</span></td>";
                                echo "<td><span class='badge " . $status_class . "'>" . htmlspecialchars($status) . "</span></td>";
                                echo "<td>
                                        <button class='btn-edit' onclick='openEditModal($id, \"" . addslashes($c_name) . "\", \"" . addslashes($r_no) . "\", \"" . addslashes($items) . "\", $total, \"" . addslashes($pay_mode) . "\", \"" . addslashes($pay_status) . "\", \"" . addslashes($status) . "\")'>Edit</button><br>
                                        <a href='food_orders.php?delete_order=" . $id . "' class='btn-delete' onclick='return confirm(\"Delete this order?\")'>Delete</a>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8'>No food orders found!</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3>Update Order & Status</h3>
            <form action="" method="POST" autocomplete="off">
                <input type="hidden" name="order_id" id="edit_id">
                
                <div class="form-group">
                    <label>Customer Name</label>
                    <input type="text" name="customer_name" id="edit_name" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Room Number</label>
                    <input type="text" name="room_no" id="edit_room" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Items</label>
                    <input type="text" name="items" id="edit_items" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Total Amount (₹)</label>
                    <input type="number" step="0.01" name="total_amount" id="edit_total" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Payment Mode</label>
                    <select name="payment_mode" id="edit_pay_mode">
                        <option value="Cash">Cash / Pay to Room Bill</option>
                        <option value="Online">Online Payment (UPI/Card)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Payment Status</label>
                    <select name="payment_status" id="edit_pay_status">
                        <option value="Pending">Pending</option>
                        <option value="Paid">Paid</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Food Order Status</label>
                    <select name="status" id="edit_status">
                        <option value="Ordered">Ordered</option>
                        <option value="Preparing">Preparing</option>
                        <option value="Delivered">Delivered</option>
                    </select>
                </div>

                <div class="modal-btns">
                    <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" name="update_order" class="edit-save btn-save">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, name, room, items, total, pay_mode, pay_status, status) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_room').value = room;
            document.getElementById('edit_items').value = items;
            document.getElementById('edit_total').value = total;
            document.getElementById('edit_pay_mode').value = pay_mode;
            document.getElementById('edit_pay_status').value = pay_status;
            document.getElementById('edit_status').value = status;
            document.getElementById('editModal').style.display = 'flex';
        }
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
    </script>
</body>
</html>