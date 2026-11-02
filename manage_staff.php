<?php
session_start();
include('db.php');

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM staff WHERE id = $id");
    header("Location: manage_staff.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_staff'])) {
    $id = (int)$_POST['staff_id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);
    $work_assigned = trim($_POST['work_assigned']);
    $status = trim($_POST['status']);

    $update_stmt = $conn->prepare("UPDATE staff SET name = ?, email = ?, role = ?, work_assigned = ?, status = ? WHERE id = ?");
    $update_stmt->bind_param("sssssi", $name, $email, $role, $work_assigned, $status, $id);
    $update_stmt->execute();
    
    header("Location: manage_staff.php");
    exit();
}

$search = trim($_GET['search'] ?? '');
$role_filter = trim($_GET['role'] ?? '');

$query = "SELECT * FROM staff WHERE 1=1";

if ($search !== '') {
    $search_safe = $conn->real_escape_string($search);
    $query .= " AND (name LIKE '%$search_safe%' OR email LIKE '%$search_safe%' OR role LIKE '%$search_safe%' OR work_assigned LIKE '%$search_safe%')";
}

if ($role_filter !== '') {
    $role_safe = $conn->real_escape_string($role_filter);
    $query .= " AND role = '$role_safe'";
}

$query .= " ORDER BY id ASC";
$staff_res = $conn->query($query);

$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_res = $conn->query("SELECT * FROM staff WHERE id = $edit_id");
    if ($edit_res && $edit_res->num_rows > 0) {
        $edit_data = $edit_res->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Details - Hotel Elite Admin</title>
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
            color: white; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            padding: 15px 40px; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
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
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
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
            color: white; 
            border-left: 5px solid #f39c12; 
        }

        .content { 
            flex: 1; 
            max-width: 1100px; 
            margin: 30px auto; 
            padding: 0 20px; 
            width: 100%;
        }

        .card { 
            background: #fff; 
            padding: 25px; 
            border-radius: 8px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
            margin-bottom: 25px; 
        }

        h2 { 
            color: #0d1b2a; 
            margin-bottom: 15px; 
            font-size: 1.8rem;
        }
        
        .filter-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-bar form {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
            width: 100%;
        }

        .filter-bar input[type="text"], 
        .filter-bar select {
            padding: 10px 12px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 0.9rem;
            outline: none;
            flex: 1;
            min-width: 180px;
        }

        .filter-bar input[type="text"]:focus, 
        .filter-bar select:focus {
            border-color: #007bff;
        }

        .btn-search {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-search:hover { 
            background: #0056b3; 
        }
        
        .btn-back {
            display: inline-block;
            background: #6c757d;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 0.9rem;
            margin-bottom: 20px;
            transition: background 0.2s;
        }

        .btn-back:hover { 
            background: #5a6268; 
        }

        .edit-form-box {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 25px;
            border: 1px solid #e9ecef;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }

        .edit-form-box h3 {
            color: #0d1b2a;
            margin-bottom: 15px;
        }

        .edit-form-box .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .edit-form-box label {
            display: block;
            font-weight: 600;
            font-size: 0.85rem;
            margin-bottom: 6px;
            color: #495057;
        }

        .edit-form-box input, 
        .edit-form-box select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 0.95rem;
            box-sizing: border-box;
        }

        .btn-update {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-update:hover { 
            background: #218838; 
        }

        .btn-cancel {
            background: #6c757d;
            color: white;
            padding: 10px 18px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 0.9rem;
            margin-left: 10px;
            display: inline-block;
            transition: background 0.2s;
        }

        .btn-cancel:hover {
            background: #5a6268;
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
            color: white; 
            font-weight: 600;
        }

        tr:nth-child(even) { 
            background: #fdfdfe; 
        }

        tr:hover {
            background: #f1f4f8;
        }

        .badge { 
            background: #28a745; 
            color: white; 
            padding: 5px 10px; 
            border-radius: 4px; 
            font-size: 0.75rem; 
            font-weight: bold; 
            display: inline-block;
        }

        .role-badge { 
            background: #007bff; 
            color: white; 
            padding: 5px 10px; 
            border-radius: 4px; 
            font-size: 0.8rem; 
            font-weight: bold; 
            display: inline-block;
        }

        .btn-edit { 
            background: #ffc107; 
            color: #212529; 
            padding: 6px 12px; 
            text-decoration: none; 
            border-radius: 4px; 
            font-weight: bold; 
            font-size: 0.8rem; 
            margin-right: 5px; 
            display: inline-block; 
            transition: background 0.2s;
        }

        .btn-edit:hover {
            background: #e0a800;
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
            transition: background 0.2s;
        }

        .btn-delete:hover {
            background: #c82333;
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
                <li><a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a></li>
                <li><a href="booking_management.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'booking_management.php' ? 'active' : ''; ?>">Bookings</a></li>
                <li><a href="room_management.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'room_management.php' ? 'active' : ''; ?>">Manage Rooms</a></li>
                <li><a href="food_orders.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'food_orders.php' ? 'active' : ''; ?>">Food Orders</a></li>
                <li><a href="food_menu.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'food_menu.php' ? 'active' : ''; ?>">Manage Menu</a></li>
                <li><a href="manage_staff.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_staff.php' ? 'active' : ''; ?>">Manage Staff</a></li>
                <li><a href="users.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">Users</a></li>
                <!-- અહીં બંને અલગ રિપોર્ટ્સની લિંક્સ સેટ કરી દીધી છે -->
                <li><a href="room_reports.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'room_reports.php' ? 'active' : ''; ?>">Room Reports</a></li>
                <li><a href="food_reports.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'food_reports.php' ? 'active' : ''; ?>">Food Reports</a></li>
            </ul>
        </aside>

        <div class="content">
            <a href="dashboard.php" class="btn-back">&larr; Back to Dashboard</a>

            <?php if ($edit_data): ?>
            <div class="edit-form-box">
                <h3>Edit Staff Member: <?php echo htmlspecialchars($edit_data['name']); ?></h3>
                <form method="POST" action="">
                    <input type="hidden" name="staff_id" value="<?php echo $edit_data['id']; ?>">
                    
                    <div class="form-row">
                        <div>
                            <label>Staff Name</label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($edit_data['name']); ?>" required>
                        </div>
                        <div>
                            <label>Email Address</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($edit_data['email']); ?>" required>
                        </div>
                        <div>
                            <label>Role / Designation</label>
                            <select name="role" required>
                                <option value="Manager" <?php if($edit_data['role'] == 'Manager') echo 'selected'; ?>>Manager</option>
                                <option value="Receptionist" <?php if($edit_data['role'] == 'Receptionist') echo 'selected'; ?>>Receptionist</option>
                                <option value="Kitchen Manager" <?php if($edit_data['role'] == 'Kitchen Manager') echo 'selected'; ?>>Kitchen Manager</option>
                                <option value="Room Service" <?php if($edit_data['role'] == 'Room Service') echo 'selected'; ?>>Room Service</option>
                                <option value="Housekeeping Lead" <?php if($edit_data['role'] == 'Housekeeping Lead') echo 'selected'; ?>>Housekeeping Lead</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div style="grid-column: span 2;">
                            <label>Assigned Work & Duties</label>
                            <input type="text" name="work_assigned" value="<?php echo htmlspecialchars($edit_data['work_assigned']); ?>" required>
                        </div>
                        <div>
                            <label>Status</label>
                            <select name="status" required>
                                <option value="Active" <?php if($edit_data['status'] == 'Active') echo 'selected'; ?>>Active</option>
                                <option value="Inactive" <?php if($edit_data['status'] == 'Inactive') echo 'selected'; ?>>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div style="margin-top: 15px;">
                        <button type="submit" name="update_staff" class="btn-update">Save Changes</button>
                        <a href="manage_staff.php" class="btn-cancel">Cancel</a>
                    </div>
                </form>
            </div>
            <?php endif; ?>

            <div class="card">
                <h2>All Staff Members Details</h2>
                <p style="color: #6c757d; margin-bottom: 20px;">Manage all hotel staff members, view or edit their designated roles, work responsibilities, and statuses.</p>

                <div class="filter-bar">
                    <form method="GET" action="">
                        <input type="text" name="search" placeholder="Search by name, email, role..." value="<?php echo htmlspecialchars($search); ?>">
                        
                        <select name="role">
                            <option value="">All Roles</option>
                            <option value="Manager" <?php if($role_filter == 'Manager') echo 'selected'; ?>>Manager</option>
                            <option value="Receptionist" <?php if($role_filter == 'Receptionist') echo 'selected'; ?>>Receptionist</option>
                            <option value="Kitchen Manager" <?php if($role_filter == 'Kitchen Manager') echo 'selected'; ?>>Kitchen Manager</option>
                            <option value="Room Service" <?php if($role_filter == 'Room Service') echo 'selected'; ?>>Room Service</option>
                            <option value="Housekeeping Lead" <?php if($role_filter == 'Housekeeping Lead') echo 'selected'; ?>>Housekeeping Lead</option>
                        </select>
                        
                        <button type="submit" class="btn-search">Filter</button>
                        <?php if($search !== '' || $role_filter !== ''): ?>
                            <a href="manage_staff.php" style="color: #dc3545; text-decoration: none; font-weight: bold; font-size: 0.9rem; padding: 10px;">Reset</a>
                        <?php endif; ?>
                    </form>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Staff Name</th>
                            <th>Email</th>
                            <th>Role / Designation</th>
                            <th>Assigned Duties & Work</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($staff_res && $staff_res->num_rows > 0) {
                            while ($s = $staff_res->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td><b>#" . $s['id'] . "</b></td>";
                                echo "<td>" . htmlspecialchars($s['name']) . "</td>";
                                echo "<td>" . htmlspecialchars($s['email']) . "</td>";
                                echo "<td><span class='role-badge'>" . htmlspecialchars($s['role']) . "</span></td>";
                                echo "<td style='text-align: left;'><small>" . htmlspecialchars($s['work_assigned']) . "</small></td>";
                                echo "<td><span class='badge'>" . htmlspecialchars($s['status']) . "</span></td>";
                                echo "<td>
                                        <a href='manage_staff.php?edit=" . $s['id'] . "' class='btn-edit'>Edit</a>
                                        <a href='manage_staff.php?delete=" . $s['id'] . "' class='btn-delete' onclick='return confirm(\"Are you sure you want to delete this staff member?\")'>Remove</a>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>No staff records found matching your criteria!</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>