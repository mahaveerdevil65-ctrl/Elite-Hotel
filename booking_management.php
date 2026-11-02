<?php
session_start();
include('db.php');

if (!isset($_SESSION['admin']) && !isset($_SESSION['staff_email'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['update_booking'])) {
    $b_id = (int)$_POST['booking_id'];
    $user_name = $_POST['user_name'];
    $room_id = (int)$_POST['room_id'];
    $phone = $_POST['phone'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $total_price = $_POST['total_price'];
    $payment_method = $_POST['payment_method'];
    $payment_status = $_POST['payment_status'];

    $stmt = $conn->prepare("UPDATE room_bookings SET user_name = ?, room_id = ?, phone = ?, check_in = ?, check_out = ?, total_price = ?, payment_method = ?, payment_status = ? WHERE id = ?");
    $stmt->bind_param("sisssdssi", $user_name, $room_id, $phone, $check_in, $check_out, $total_price, $payment_method, $payment_status, $b_id);
    $stmt->execute();
    header("Location: booking_management.php");
    exit();
}

if (isset($_GET['delete_booking'])) {
    $b_id = (int)$_GET['delete_booking'];
    $conn->query("DELETE FROM room_bookings WHERE id = $b_id");
    header("Location: booking_management.php");
    exit();
}

$search_query = isset($_GET['search']) ? trim($_GET['search']) : "";
$status_filter = isset($_GET['status_filter']) ? trim($_GET['status_filter']) : "";
$filter_date = isset($_GET['filter_date']) ? trim($_GET['filter_date']) : "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Bookings - Hotel Elite</title>
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
            max-width: 1150px;
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
            padding: 12px 8px;
            text-align: center;
            font-size: 0.88rem;
        }

        th {
            background: #007bff;
            color: #fff;
        }

        .badge-confirmed {
            background: #28a745;
            color: #fff;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.75rem;
            display: inline-block;
        }

        .badge-cancelled {
            background: #dc3545;
            color: #fff;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.75rem;
            display: inline-block;
        }

        .badge-pending {
            background: #ffc107;
            color: #000;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.75rem;
            display: inline-block;
        }

        .btn-delete {
            background: #dc3545;
            color: #fff;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.8rem;
            display: inline-block;
            margin-top: 4px;
            font-weight: bold;
        }

        .btn-edit {
            background: #ffc107;
            color: #212529;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            font-size: 0.8rem;
            cursor: pointer;
            display: inline-block;
            margin-top: 4px;
            font-weight: bold;
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
        }

        .form-group input, 
        .form-group select {
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
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
        <div class="logo">Hotel Elite - Bookings</div>
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
                <h2>Customer Room Bookings</h2>
                <a href="dashboard.php" class="btn-back">&larr; Back to Dashboard</a>
            </div>

            <div class="card">
                <h3>Bookings List</h3>
                
                <form method="GET" action="" class="filter-form" autocomplete="off">
                    <input type="text" name="search" placeholder="Search name, phone, room type..." value="<?php echo htmlspecialchars($search_query); ?>" autocomplete="off">
                    
                    <select name="status_filter">
                        <option value="">-- Filter by Status --</option>
                        <option value="Confirmed" <?php if ($status_filter == 'Confirmed') echo 'selected'; ?>>Confirmed</option>
                        <option value="Cancelled" <?php if ($status_filter == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                        <option value="Pending (Pay at Hotel)" <?php if ($status_filter == 'Pending (Pay at Hotel)') echo 'selected'; ?>>Pending (Pay at Hotel)</option>
                        <option value="Paid Successfully" <?php if ($status_filter == 'Paid Successfully') echo 'selected'; ?>>Paid Successfully</option>
                    </select>

                    <input type="date" name="filter_date" value="<?php echo htmlspecialchars($filter_date); ?>">
                    <button type="submit">Apply Filter</button>
                    <?php if (!empty($search_query) || !empty($status_filter) || !empty($filter_date)): ?>
                        <a href="booking_management.php">Reset</a>
                    <?php endif; ?>
                </form>

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer Name</th>
                            <th>Room No</th>
                            <th>Room Type</th>
                            <th>Phone</th>
                            <th>Check-In & Check-Out</th>
                            <th>Amount</th>
                            <th>Payment Mode</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $b_query = "SELECT rb.*, r.room_no, r.room_type, r.id AS r_id FROM room_bookings rb LEFT JOIN rooms r ON rb.room_id = r.id WHERE 1=1";
                        
                        if (!empty($search_query)) {
                            $safe_search = mysqli_real_escape_string($conn, $search_query);
                            $b_query .= " AND (rb.user_name LIKE '%$safe_search%' OR r.room_type LIKE '%$safe_search%' OR r.room_no LIKE '%$safe_search%' OR rb.phone LIKE '%$safe_search%' OR rb.payment_method LIKE '%$safe_search%')";
                        }
                        if (!empty($status_filter)) {
                            $safe_status = mysqli_real_escape_string($conn, $status_filter);
                            $b_query .= " AND rb.payment_status = '$safe_status'";
                        }
                        if (!empty($filter_date)) {
                            $safe_date = mysqli_real_escape_string($conn, $filter_date);
                            $b_query .= " AND rb.check_in LIKE '$safe_date%'";
                        }

                        $b_query .= " ORDER BY rb.id DESC";
                        $b_result = $conn->query($b_query);

                        if ($b_result && $b_result->num_rows > 0) {
                            while ($row = $b_result->fetch_assoc()) {
                                $id = $row['id'];
                                $name = $row['user_name'] ?? "N/A";
                                $room_id = $row['room_id'] ?? 0;
                                $room_no = $row['room_no'] ?? "N/A";
                                $room_type = $row['room_type'] ?? "N/A";
                                $phone = $row['phone'] ?? "N/A";
                                $in = $row['check_in'] ?? "N/A";
                                $out = $row['check_out'] ?? "N/A";
                                $price = $row['total_price'] ?? 0;
                                $pay = $row['payment_method'] ?? "Cash";
                                $status = $row['payment_status'] ?? "Confirmed";

                                echo "<tr>";
                                echo "<td><b>#" . $id . "</b></td>";
                                echo "<td>" . $name . "</td>";
                                echo "<td><b>" . $room_no . "</b></td>";
                                echo "<td>" . $room_type . "</td>";
                                echo "<td>" . $phone . "</td>";
                                echo "<td><small>" . $in . "<br>to<br>" . $out . "</small></td>";
                                echo "<td><b>₹" . number_format($price, 2) . "</b></td>";
                                echo "<td>" . $pay . "</td>";
                                echo "<td>";
                                if ($status == 'Confirmed' || $status == 'Paid Successfully') {
                                    echo "<span class='badge-confirmed'>" . $status . "</span>";
                                } elseif ($status == 'Cancelled') {
                                    echo "<span class='badge-cancelled'>" . $status . "</span>";
                                } else {
                                    echo "<span class='badge-pending'>" . $status . "</span>";
                                }
                                echo "</td>";
                                echo "<td>
                                        <button class='btn-edit' onclick='openEditModal($id, \"" . addslashes($name) . "\", $room_id, \"" . addslashes($phone) . "\", \"" . addslashes($in) . "\", \"" . addslashes($out) . "\", $price, \"" . addslashes($pay) . "\", \"" . addslashes($status) . "\")'>Edit</button><br>
                                        <a href='booking_management.php?delete_booking=" . $id . "' class='btn-delete' onclick='return confirm(\"Delete booking?\")'>Delete</a>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='10'>No room bookings found!</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="editBookingModal" class="modal">
        <div class="modal-content">
            <h3>Edit Room Booking</h3>
            <form action="" method="POST" autocomplete="off">
                <input type="hidden" name="booking_id" id="edit_b_id">
                
                <div class="form-group">
                    <label>Customer Name</label>
                    <input type="text" name="user_name" id="edit_b_name" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Room Type & No</label>
                    <select name="room_id" id="edit_b_room" required>
                        <?php
                        $room_dropdown = $conn->query("SELECT * FROM rooms ORDER BY id ASC");
                        while ($rd = $room_dropdown->fetch_assoc()) {
                            echo "<option value='" . $rd['id'] . "'>Room No: " . $rd['room_no'] . " - " . $rd['room_type'] . " (₹" . $rd['price'] . "/night)</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" id="edit_b_phone" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Check-In Date & Time</label>
                    <input type="text" name="check_in" id="edit_in" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Check-Out Date & Time</label>
                    <input type="text" name="check_out" id="edit_out" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Total Amount (₹)</label>
                    <input type="number" step="0.01" name="total_price" id="edit_b_price" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Payment Method</label>
                    <select name="payment_method" id="edit_b_pay">
                        <option value="Cash">Cash</option>
                        <option value="Paytm / UPI">Paytm / UPI</option>
                        <option value="Credit / Debit Card">Credit / Debit Card</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="payment_status" id="edit_b_status">
                        <option value="Confirmed">Confirmed</option>
                        <option value="Cancelled">Cancelled</option>
                        <option value="Pending (Pay at Hotel)">Pending (Pay at Hotel)</option>
                        <option value="Paid Successfully">Paid Successfully</option>
                    </select>
                </div>

                <div class="modal-btns">
                    <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" name="update_booking" class="btn-save">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, name, roomId, phone, checkIn, checkOut, price, pay, status) {
            document.getElementById('edit_b_id').value = id;
            document.getElementById('edit_b_name').value = name;
            document.getElementById('edit_b_room').value = roomId;
            document.getElementById('edit_b_phone').value = phone;
            document.getElementById('edit_in').value = checkIn;
            document.getElementById('edit_out').value = checkOut;
            document.getElementById('edit_b_price').value = price;
            document.getElementById('edit_b_pay').value = pay;
            document.getElementById('edit_b_status').value = status;
            document.getElementById('editBookingModal').style.display = 'flex';
        }
        function closeEditModal() {
            document.getElementById('editBookingModal').style.display = 'none';
        }
    </script>
</body>
</html>