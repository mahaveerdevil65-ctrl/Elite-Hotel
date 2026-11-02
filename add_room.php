<?php
session_start();
include('db.php');

$success_msg = "";
$error_msg = "";

if (isset($_POST['add_room'])) {
    $room_no = mysqli_real_escape_string($conn, $_POST['room_no']);
    $room_type = mysqli_real_escape_string($conn, $_POST['room_type']);
    $price = (float)$_POST['price'];
    $bed_type = mysqli_real_escape_string($conn, $_POST['bed_type']);
    $image = mysqli_real_escape_string($conn, $_POST['image']);

    $query = "INSERT INTO rooms (room_no, room_type, price, bed_type, image) VALUES ('$room_no', '$room_type', '$price', '$bed_type', '$image')";
    
    if ($conn->query($query) === TRUE) {
        $success_msg = "Room successfully add thai gyo chhe!";
    } else {
        $error_msg = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Add New Room | Hotel Elite</title>
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
        }

        header {
            background: #0d1b2a;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 40px;
            flex-wrap: wrap;
        }

        .logo {
            font-size: 22px;
            font-weight: bold;
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        nav ul li a {
            color: #adb5bd;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            padding: 5px 8px;
            border-radius: 4px;
            transition: all 0.2s;
        }

        nav ul li a:hover, 
        nav ul li a.active {
            color: #fff;
            background: #007bff;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        h2 {
            text-align: center;
            color: #0d1b2a;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #444;
        }

        input[type="text"], 
        input[type="number"], 
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
        }

        input:focus, 
        select:focus {
            border-color: #f39c12;
            outline: none;
        }

        .btn-submit {
            background: #27ae60;
            color: #fff;
            border: none;
            width: 100%;
            padding: 14px;
            font-size: 1.1rem;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-submit:hover {
            background: #218838;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <header>
        <div class="logo">Hotel Elite - Admin</div>
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="booking_management.php">Bookings</a></li>
                <li><a href="room_management.php">Rooms</a></li>
                <li><a href="food_orders.php">Food Orders</a></li>
                <li><a href="food_menu.php">Menu</a></li>
                <li><a href="room_reports.php">Room Reports</a></li>
                <li><a href="food_reports.php">Food Reports</a></li>
                <li><a href="logout.php" style="color: #e74c3c;">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h2>Add New Room & Bed Type</h2>

        <?php if (!empty($success_msg)): ?>
            <div class="alert-success"><?php echo $success_msg; ?></div>
        <?php endif; ?>

        <?php if (!empty($error_msg)): ?>
            <div class="alert-danger"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <form action="" method="POST" autocomplete="off">
            <div class="form-group">
                <label>Room Number:</label>
                <input type="text" name="room_no" placeholder="e.g. 106" required autocomplete="off">
            </div>

            <div class="form-group">
                <label>Room Type Name:</label>
                <input type="text" name="room_type" placeholder="e.g. Luxury Suite" required autocomplete="off">
            </div>

            <div class="form-group">
                <label>Price per Night (₹):</label>
                <input type="number" name="price" placeholder="e.g. 5000" required autocomplete="off">
            </div>

            <div class="form-group">
                <label>Select Bed Type:</label>
                <select name="bed_type" required>
                    <option value="" disabled selected>-- Select Bed Type --</option>
                    <option value="Single Bed">Single Bed (₹1,200)</option>
                    <option value="Queen Size Bed">Queen Size Bed (₹1,800)</option>
                    <option value="Twin Beds">Twin Beds (₹2,500)</option>
                    <option value="King Size Bed">King Size Bed (₹3,800)</option>
                    <option value="King Size Luxury">King Size Luxury (₹9,500)</option>
                </select>
            </div>

            <div class="form-group">
                <label>Room Image URL:</label>
                <input type="text" name="image" placeholder="Paste Unsplash or image link here" required autocomplete="off">
            </div>

            <button type="submit" name="add_room" class="btn-submit">Add Room Now</button>
        </form>

        <a href="room_management.php" class="back-link">&larr; Back to Room Management</a>
    </div>

</body>
</html>