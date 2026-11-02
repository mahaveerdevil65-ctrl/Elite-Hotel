<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', 1);

session_start();
include('db.php');

if (!isset($_SESSION['user_email']) || empty($_SESSION['user_email'])) {
    header("Location: index.php");
    exit();
}

$success_msg = "";
$error_msg = "";
$edit_mode = false;
$booking_data = [];

$edit_id = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$selected_room_id = isset($_GET['room_id']) ? (int)$_GET['room_id'] : 0;

if ($edit_id > 0) {
    $edit_query = $conn->query("SELECT * FROM room_bookings WHERE id = $edit_id");
    if ($edit_query && $edit_query->num_rows > 0) {
        $booking_data = $edit_query->fetch_assoc();
        $edit_mode = true;
        $selected_room_id = $booking_data['room_id'];
    }
}

if (isset($_POST['book_room'])) {
    $user_name = isset($_POST['user_name']) ? mysqli_real_escape_string($conn, $_POST['user_name']) : '';
    $phone = isset($_POST['phone']) ? mysqli_real_escape_string($conn, $_POST['phone']) : '';
    $user_email = isset($_POST['user_email']) ? trim($_POST['user_email']) : '';
    $id_proof_type = isset($_POST['id_proof_type']) ? mysqli_real_escape_string($conn, $_POST['id_proof_type']) : '';
    $id_proof_number = isset($_POST['id_proof_number']) ? trim($_POST['id_proof_number']) : '';
    $room_id = isset($_POST['room_id']) ? (int)$_POST['room_id'] : 0;
    $check_in = isset($_POST['check_in']) ? mysqli_real_escape_string($conn, $_POST['check_in']) : '';
    $check_in_time = isset($_POST['check_in_time']) ? mysqli_real_escape_string($conn, $_POST['check_in_time']) : '12:00';
    $check_out = isset($_POST['check_out']) ? mysqli_real_escape_string($conn, $_POST['check_out']) : '';
    $check_out_time = isset($_POST['check_out_time']) ? mysqli_real_escape_string($conn, $_POST['check_out_time']) : '11:00';
    $adults = isset($_POST['adults']) ? (int)$_POST['adults'] : 1;
    $children = isset($_POST['children']) ? (int)$_POST['children'] : 0;
    $payment_method = isset($_POST['payment_method']) ? mysqli_real_escape_string($conn, $_POST['payment_method']) : 'Cash';

    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        $error_msg = "Please enter a valid 10-digit mobile number!";
    } elseif (empty($id_proof_type) || empty($id_proof_number)) {
        $error_msg = "Please select Government ID Proof and enter its number!";
    } else {
        $safe_email = mysqli_real_escape_string($conn, $user_email);
        $ci_datetime = "$check_in $check_in_time";
        $co_datetime = "$check_out $check_out_time";

        $room_query = $conn->query("SELECT * FROM rooms WHERE id = $room_id");
        if ($room_query && $room_query->num_rows > 0) {
            $room_data = $room_query->fetch_assoc();
            
            $room_no = $room_data['room_no'] ?? '';
            $base_price = $room_data['price'] ?? 1200;
            $max_adults = $room_data['max_adults'] ?? 1;
            $max_children = $room_data['max_children'] ?? 0;
            $extra_adult_fee = $room_data['extra_adult_charge'] ?? 400;
            $extra_child_fee = $room_data['extra_child_charge'] ?? 200;

            $earlier = new DateTime($check_in);
            $later = new DateTime($check_out);
            $days = $earlier->diff($later)->days;
            if ($days <= 0) $days = 1;

            $nightly_total = $base_price;
            if ($adults > $max_adults) {
                $nightly_total += (($adults - $max_adults) * $extra_adult_fee);
            }
            if ($children > $max_children) {
                $nightly_total += (($children - $max_children) * $extra_child_fee);
            }

            $total_price = $days * $nightly_total;
            $payment_status = ($payment_method == 'Cash') ? 'Pending (Pay at Hotel)' : 'Paid Successfully';

            if ($edit_mode && $edit_id > 0) {
                $update = "UPDATE room_bookings SET 
                           user_name = '$user_name', 
                           phone = '$phone', 
                           user_email = '$safe_email', 
                           id_proof_type = '$id_proof_type', 
                           id_proof_number = '$id_proof_number', 
                           room_id = $room_id, 
                           room_no = '$room_no', 
                           check_in = '$ci_datetime', 
                           check_out = '$co_datetime', 
                           total_price = '$total_price', 
                           payment_method = '$payment_method', 
                           payment_status = '$payment_status' 
                           WHERE id = $edit_id";

                if ($conn->query($update) === TRUE) {
                    $success_msg = "Booking Updated Successfully!<br>Room No: <b>$room_no</b> | Total Payable: <b>₹" . number_format($total_price) . "</b> | Status: <b style='color:#10b981;'>$payment_status</b>";
                    $edit_query = $conn->query("SELECT * FROM room_bookings WHERE id = $edit_id");
                    $booking_data = $edit_query->fetch_assoc();
                } else {
                    $error_msg = "Database Error: " . $conn->error;
                }
            } else {
                $insert = "INSERT INTO room_bookings (user_name, phone, user_email, id_proof_type, id_proof_number, room_id, room_no, check_in, check_out, total_price, payment_method, payment_status) 
                           VALUES ('$user_name', '$phone', '$safe_email', '$id_proof_type', '$id_proof_number', $room_id, '$room_no', '$ci_datetime', '$co_datetime', '$total_price', '$payment_method', '$payment_status')";
                
                if ($conn->query($insert) === TRUE) {
                    $_SESSION['active_room_no'] = $room_no;
                    $success_msg = "Booking Successful!<br>Room No: <b>$room_no</b> | Total Payable: <b>₹" . number_format($total_price) . "</b> | Status: <b style='color:#10b981;'>$payment_status</b>";
                } else {
                    $error_msg = "Database Error: " . $conn->error;
                }
            }
        } else {
            $error_msg = "Selected room not found in the database!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $edit_mode ? "Edit Booking" : "Book Room"; ?> - Hotel Elite</title>
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
            color: #fff;
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
            color: #fff;
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
            max-width: 650px;
            margin: 40px auto;
            background: #fff;
            padding: 35px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            width: 100%;
        }

        h2 {
            text-align: center;
            color: #111827;
            margin-bottom: 25px;
            font-size: 26px;
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
            font-size: 0.9rem;
        }

        input, select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 0.95rem;
            outline: none;
        }

        input:focus, select:focus {
            border-color: #f59e0b;
        }

        .row-group {
            display: flex;
            gap: 15px;
        }

        .row-group div {
            flex: 1;
        }

        .btn-submit {
            background: #111827;
            color: #fff;
            border: none;
            width: 100%;
            padding: 12px;
            font-size: 1rem;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 5px;
            transition: background 0.2s;
        }

        .btn-submit:hover {
            background: #1f2937;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 1.05rem;
            line-height: 1.6;
            border: 1px solid #c3e6cb;
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
            font-size: 0.95rem;
            border: 1px solid #f5c6cb;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 15px;
            color: #f59e0b;
            text-decoration: none;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        footer {
            background-color: #111827;
            color: #fff;
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
                        <li><a href="my_orders.php">My Order</a></li>
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
        <a href="my_bookings.php" class="back-link">&larr; Back to My Bookings</a>
        <h2><?php echo $edit_mode ? "Edit Room Booking (#" . $edit_id . ")" : "Room Booking Form"; ?></h2>
        
        <?php if (!empty($success_msg)) { echo "<div class='alert-success'>$success_msg</div>"; } ?>
        <?php if (!empty($error_msg)) { echo "<div class='alert-danger'>$error_msg</div>"; } ?>

        <form action="" method="POST" autocomplete="off">
            <div class="form-group">
                <label>Full Name:</label>
                <input type="text" name="user_name" value="<?php echo htmlspecialchars($edit_mode ? $booking_data['user_name'] : ($_SESSION['user_name'] ?? '')); ?>" required autocomplete="off">
            </div>

            <div class="form-group">
                <label>Mobile Number (10 Digits):</label>
                <input type="tel" name="phone" value="<?php echo htmlspecialchars($edit_mode ? $booking_data['phone'] : ''); ?>" pattern="[0-9]{10}" maxlength="10" required autocomplete="off">
            </div>

            <div class="form-group">
                <label>Email Address:</label>
                <input type="email" name="user_email" value="<?php echo htmlspecialchars($edit_mode ? $booking_data['user_email'] : ($_SESSION['user_email'] ?? '')); ?>" required autocomplete="off">
            </div>

            <div class="form-group row-group">
                <div>
                    <label>Government ID Proof Type:</label>
                    <select name="id_proof_type" required>
                        <?php $id_type = $edit_mode ? $booking_data['id_proof_type'] : ''; ?>
                        <option value="">-- Select ID Proof --</option>
                        <option value="Aadhar Card" <?php if($id_type=='Aadhar Card') echo 'selected'; ?>>Aadhar Card</option>
                        <option value="PAN Card" <?php if($id_type=='PAN Card') echo 'selected'; ?>>PAN Card</option>
                        <option value="Voter ID" <?php if($id_type=='Voter ID') echo 'selected'; ?>>Voter ID</option>
                        <option value="Driving License" <?php if($id_type=='Driving License') echo 'selected'; ?>>Driving License</option>
                        <option value="Passport" <?php if($id_type=='Passport') echo 'selected'; ?>>Passport</option>
                    </select>
                </div>
                <div>
                    <label>ID Proof Number:</label>
                    <input type="text" name="id_proof_number" value="<?php echo htmlspecialchars($edit_mode ? $booking_data['id_proof_number'] : ''); ?>" required autocomplete="off">
                </div>
            </div>

            <div class="form-group">
                <label>Select Room Type:</label>
                <select name="room_id" required>
                    <?php
                    $rooms_result = $conn->query("SELECT * FROM rooms ORDER BY id ASC");
                    if ($rooms_result && $rooms_result->num_rows > 0) {
                        while ($r = $rooms_result->fetch_assoc()) {
                            $sel = ($r['id'] == $selected_room_id) ? "selected" : "";
                            echo '<option value="' . $r['id'] . '" ' . $sel . '>' . $r['room_type'] . ' (Room No: ' . $r['room_no'] . ') - ₹' . $r['price'] . '/night</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group row-group">
                <div>
                    <label>Number of Adults:</label>
                    <select name="adults">
                        <?php for($i=1; $i<=4; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php if($edit_mode && isset($booking_data['adults']) && $booking_data['adults'] == $i) echo 'selected'; elseif(!$edit_mode && $i==2) echo 'selected'; ?>><?php echo $i; ?> Adult<?php echo $i>1?'s':''; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div>
                    <label>Number of Children:</label>
                    <select name="children">
                        <?php for($i=0; $i<=3; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php if($edit_mode && isset($booking_data['children']) && $booking_data['children'] == $i) echo 'selected'; ?>><?php echo $i; ?> Child<?php echo $i!=1?'ren':''; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Select Payment Method:</label>
                <select name="payment_method" required>
                    <?php $pm = $edit_mode ? $booking_data['payment_method'] : 'Cash'; ?>
                    <option value="Cash" <?php if($pm=='Cash') echo 'selected'; ?>>Cash (Status: Pending / Pay at Hotel)</option>
                    <option value="Paytm / UPI" <?php if($pm=='Paytm / UPI') echo 'selected'; ?>>Paytm / UPI (Status: Paid Successfully)</option>
                    <option value="Credit / Debit Card" <?php if($pm=='Credit / Debit Card') echo 'selected'; ?>>Credit / Debit Card (Status: Paid Successfully)</option>
                </select>
            </div>

            <div class="form-group">
                <label>Check-In Date & Time:</label>
                <div class="row-group">
                    <?php 
                    $ci_date = ""; $ci_time = "12:00";
                    if ($edit_mode && !empty($booking_data['check_in'])) {
                        $ci_parts = explode(" ", $booking_data['check_in']);
                        $ci_date = $ci_parts[0];
                        if(isset($ci_parts[1])) $ci_time = substr($ci_parts[1], 0, 5);
                    }
                    ?>
                    <input type="date" name="check_in" value="<?php echo $ci_date; ?>" required>
                    <input type="time" name="check_in_time" value="<?php echo $ci_time; ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Check-Out Date & Time:</label>
                <div class="row-group">
                    <?php 
                    $co_date = ""; $co_time = "11:00";
                    if ($edit_mode && !empty($booking_data['check_out'])) {
                        $co_parts = explode(" ", $booking_data['check_out']);
                        $co_date = $co_parts[0];
                        if(isset($co_parts[1])) $co_time = substr($co_parts[1], 0, 5);
                    }
                    ?>
                    <input type="date" name="check_out" value="<?php echo $co_date; ?>" required>
                    <input type="time" name="check_out_time" value="<?php echo $co_time; ?>" required>
                </div>
            </div>

            <button type="submit" name="book_room" class="btn-submit"><?php echo $edit_mode ? "Update Booking" : "Confirm Booking & Pay"; ?></button>
        </form>
    </div>

    <footer>
        <p>&copy; 2026 Hotel Elite, Ahmedabad. All Rights Reserved.</p>
    </footer>
</body>
</html>