<?php
session_start();
include('db.php');

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['add_room'])) {
    $rno = mysqli_real_escape_string($conn, $_POST['room_no']);
    $floor = mysqli_real_escape_string($conn, $_POST['floor']);
    $rtype = mysqli_real_escape_string($conn, $_POST['room_type']);
    $rstatus = mysqli_real_escape_string($conn, $_POST['room_status']);
    $price = (float)$_POST['price'];

    $conn->query("INSERT INTO rooms (room_no, floor, room_type, room_status, price) VALUES ('$rno', '$floor', '$rtype', '$rstatus', $price)");
    header("Location: room_management.php");
    exit();
}

if (isset($_POST['update_room'])) {
    $id = (int)$_POST['room_id'];
    $rno = mysqli_real_escape_string($conn, $_POST['room_no']);
    $floor = mysqli_real_escape_string($conn, $_POST['floor']);
    $rtype = mysqli_real_escape_string($conn, $_POST['room_type']);
    $rstatus = mysqli_real_escape_string($conn, $_POST['room_status']);
    $price = (float)$_POST['price'];

    $conn->query("UPDATE rooms SET room_no='$rno', floor='$floor', room_type='$rtype', room_status='$rstatus', price=$price WHERE id=$id");
    header("Location: room_management.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM rooms WHERE id = $id");
    header("Location: room_management.php");
    exit();
}

$search_query = isset($_GET['search']) ? trim($_GET['search']) : "";
$status_filter = isset($_GET['status_filter']) ? trim($_GET['status_filter']) : "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Room Management - Hotel Elite Admin</title>
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
            background: #fff; 
            padding: 30px; 
            border-radius: 8px; 
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); 
        }

        .header-flex { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 25px; 
        }

        h2 { 
            color: #0d1b2a; 
            font-size: 1.8rem;
        }

        .back-link { 
            display: inline-block; 
            margin-bottom: 20px; 
            color: #6c757d; 
            text-decoration: none; 
            font-weight: bold; 
            font-size: 0.9rem; 
            transition: color 0.2s;
        }

        .back-link:hover {
            color: #0d1b2a;
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
            transition: background 0.2s;
        }

        .filter-form button:hover {
            background: #0056b3;
        }

        .filter-form a.reset-btn { 
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
            text-align: center; 
        }

        th, td { 
            padding: 12px 10px; 
            border: 1px solid #e9ecef; 
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

        .btn { 
            padding: 6px 12px; 
            border: none; 
            border-radius: 4px; 
            font-weight: bold; 
            cursor: pointer; 
            color: #fff; 
            text-decoration: none; 
            font-size: 0.8rem; 
            display: inline-block; 
            transition: background 0.2s;
        }

        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }

        .btn-warning { background: #ffc107; color: #212529; }
        .btn-warning:hover { background: #e0a800; }

        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }

        #addRoomForm { 
            display: none; 
            background: #f8f9fa; 
            padding: 25px; 
            border: 1px solid #e9ecef; 
            border-radius: 8px; 
            margin-bottom: 25px; 
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03);
        }

        #addRoomForm h3 {
            color: #0d1b2a;
            margin-bottom: 15px;
        }

        .form-group { 
            margin-bottom: 15px; 
            text-align: left; 
        }

        label { 
            display: block; 
            margin-bottom: 6px; 
            font-weight: 600; 
            font-size: 0.85rem; 
            color: #495057;
        }

        input, select { 
            width: 100%; 
            padding: 10px; 
            border: 1px solid #ced4da; 
            border-radius: 5px; 
            font-size: 0.95rem; 
        }

        input:focus, select:focus {
            border-color: #007bff;
            outline: none;
        }

        .modal { 
            display: none; 
            position: fixed; 
            z-index: 99; 
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
            position: relative; 
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.2);
        }

        .modal-content h3 {
            color: #0d1b2a;
            margin-bottom: 20px;
        }

        .close-modal { 
            position: absolute; 
            right: 20px; 
            top: 15px; 
            font-size: 22px; 
            cursor: pointer; 
            font-weight: bold; 
            color: #6c757d;
        }

        .close-modal:hover {
            color: #333;
        }
    </style>
</head>
<body>

<header>
    <div class="logo">Hotel Elite - Admin Panel</div>
    <div class="logout-box"><a href="logout.php">Logout</a></div>
</header>

<div class="main-wrapper">
    <aside>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="booking_management.php">Bookings</a></li>
            <li><a href="room_management.php" class="active">Manage Rooms</a></li>
            <li><a href="food_orders.php">Food Orders</a></li>
            <li><a href="food_menu.php">Manage Menu</a></li>
            <li><a href="manage_staff.php">Staff Details</a></li>
            <li><a href="users.php">Users</a></li>
            <li><a href="room_reports.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'room_reports.php' ? 'active' : ''; ?>">Room Reports</a></li>
            <li><a href="food_reports.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'food_reports.php' ? 'active' : ''; ?>">Food Reports</a></li>
        </ul>
    </aside>

    <div class="content">
        <a href="dashboard.php" class="back-link">&larr; Back to Dashboard</a>

        <div class="header-flex">
            <h2>Room Management</h2>
            <button class="btn btn-success" onclick="toggleRoomForm()" style="padding: 10px 18px; font-size: 0.9rem;">+ Add New Room</button>
        </div>

        <div id="addRoomForm">
            <h3>Add New Room</h3>
            <form method="POST" autocomplete="off" style="margin-top: 10px;">
                <div class="form-group">
                    <label>Room Number</label>
                    <input type="text" name="room_no" placeholder="e.g. 101" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Floor</label>
                    <input type="text" name="floor" placeholder="e.g. 1st Floor" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Room Type</label>
                    <input type="text" name="room_type" placeholder="e.g. Deluxe Room" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Room Status</label>
                    <select name="room_status">
                        <option value="Active">Active</option>
                        <option value="Clean & Ready">Clean & Ready</option>
                        <option value="Maintenance">Maintenance</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Price per Night (₹)</label>
                    <input type="number" step="0.01" name="price" placeholder="1800.00" required autocomplete="off">
                </div>
                <button type="submit" name="add_room" class="btn btn-success" style="width:100%; padding:12px; font-size:0.95rem;">Save Room</button>
            </form>
        </div>

        <form method="GET" action="" class="filter-form" autocomplete="off">
            <input type="text" name="search" placeholder="Search room no, type, floor..." value="<?php echo htmlspecialchars($search_query); ?>" autocomplete="off">
            
            <select name="status_filter">
                <option value="">-- Filter by Status --</option>
                <option value="Active" <?php if ($status_filter == 'Active') echo 'selected'; ?>>Active</option>
                <option value="Clean & Ready" <?php if ($status_filter == 'Clean & Ready') echo 'selected'; ?>>Clean & Ready</option>
                <option value="Maintenance" <?php if ($status_filter == 'Maintenance') echo 'selected'; ?>>Maintenance</option>
            </select>

            <button type="submit">Search</button>
            <?php if (!empty($search_query) || !empty($status_filter)): ?>
                <a href="room_management.php" class="reset-btn">Reset</a>
            <?php endif; ?>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Room No</th>
                    <th>Floor</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM rooms WHERE 1=1";

                if (!empty($search_query)) {
                    $safe_search = mysqli_real_escape_string($conn, $search_query);
                    $query .= " AND (room_no LIKE '%$safe_search%' OR room_type LIKE '%$safe_search%' OR floor LIKE '%$safe_search%')";
                }

                if (!empty($status_filter)) {
                    $safe_status = mysqli_real_escape_string($conn, $status_filter);
                    $query .= " AND room_status = '$safe_status'";
                }

                $query .= " ORDER BY id DESC";
                $rooms = $conn->query($query);

                if ($rooms && $rooms->num_rows > 0) {
                    while ($row = $rooms->fetch_assoc()) {
                        $status = isset($row['room_status']) ? $row['room_status'] : 'Active';
                        $floor = isset($row['floor']) ? $row['floor'] : '-';
                        $rtype = isset($row['room_type']) ? $row['room_type'] : '-';
                        $price = isset($row['price']) ? $row['price'] : '0.00';

                        echo "<tr>
                                <td><b>#" . $row['id'] . "</b></td>
                                <td><b>" . htmlspecialchars($row['room_no']) . "</b></td>
                                <td>" . htmlspecialchars($floor) . "</td>
                                <td>" . htmlspecialchars($rtype) . "</td>
                                <td>" . htmlspecialchars($status) . "</td>
                                <td><b>₹" . htmlspecialchars($price) . "</b></td>
                                <td>
                                    <button class='btn btn-warning' onclick='openEditModal(" . $row['id'] . ", \"" . addslashes($row['room_no']) . "\", \"" . addslashes($floor) . "\", \"" . addslashes($rtype) . "\", \"" . addslashes($status) . "\", \"" . addslashes($price) . "\")'>Edit</button>
                                    <a href='room_management.php?delete=" . $row['id'] . "' class='btn btn-danger' onclick='return confirm(\"Delete this room?\")'>Delete</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No rooms found matching your search!</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModal()">&times;</span>
        <h3>Edit Room Details</h3>
        <form method="POST" autocomplete="off" style="margin-top: 15px;">
            <input type="hidden" name="room_id" id="edit_id">
            <div class="form-group">
                <label>Room Number</label>
                <input type="text" name="room_no" id="edit_room_no" required autocomplete="off">
            </div>
            <div class="form-group">
                <label>Floor</label>
                <input type="text" name="floor" id="edit_floor" required autocomplete="off">
            </div>
            <div class="form-group">
                <label>Room Type</label>
                <input type="text" name="room_type" id="edit_room_type" required autocomplete="off">
            </div>
            <div class="form-group">
                <label>Room Status</label>
                <select name="room_status" id="edit_room_status">
                    <option value="Active">Active</option>
                    <option value="Clean & Ready">Clean & Ready</option>
                    <option value="Maintenance">Maintenance</option>
                </select>
            </div>
            <div class="form-group">
                <label>Price per Night (₹)</label>
                <input type="number" step="0.01" name="price" id="edit_price" required autocomplete="off">
            </div>
            <button type="submit" name="update_room" class="btn btn-success" style="width:100%; padding:12px; font-size:0.95rem;">Update Room</button>
        </form>
    </div>
</div>

<script>
    function toggleRoomForm() {
        var f = document.getElementById('addRoomForm');
        f.style.display = (f.style.display === 'none' || f.style.display === '') ? 'block' : 'none';
    }

    function openEditModal(id, rno, floor, rtype, status, price) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_room_no').value = rno;
        document.getElementById('edit_floor').value = floor;
        document.getElementById('edit_room_type').value = rtype;
        document.getElementById('edit_room_status').value = status;
        document.getElementById('edit_price').value = price;
        document.getElementById('editModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('editModal').style.display = 'none';
    }
</script>

</body>
</html>