<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', 1);

include 'db.php';
session_start();

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($name) && !empty($email) && !empty($password)) {
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $error = "Email already registered! Please login.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hashed_password);

            if ($stmt->execute()) {
                $success = "Registration successful! <a href='index.php' style='color: #10b981; font-weight: bold; text-decoration: underline;'>Login here</a>";
            } else {
                $error = "Database Error: " . $stmt->error;
            }
            $stmt->close();
        }
        $checkStmt->close();
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Elite - Sign Up</title>
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

        .btn-nav-signup {
            background-color: #f59e0b;
            color: #fff;
            padding: 8px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
        }

        .main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .signup-card {
            background: #fff;
            padding: 40px 35px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 420px;
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
            margin-bottom: 8px;
            color: #333;
            font-weight: bold;
            font-size: 0.9rem;
        }

        input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 0.95rem;
            outline: none;
        }

        input:focus {
            border-color: #f59e0b;
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background-color: #111827;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            font-size: 1rem;
            margin-top: 5px;
            transition: background 0.2s;
        }

        .btn-submit:hover {
            background-color: #1f2937;
        }

        .alert-error {
            color: #721c24;
            background-color: #f8d7da;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            font-size: 0.85rem;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            font-size: 0.85rem;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #555;
            font-size: 0.9rem;
        }

        .login-link a {
            color: #f59e0b;
            text-decoration: none;
            font-weight: bold;
        }

        .login-link a:hover {
            text-decoration: underline;
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
                        <li><a href="my_orders.php">My Order</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>

        <div class="header-bottom">
            <?php if (isset($_SESSION['user_email'])): ?>
                <a href="logout.php" class="btn-nav-login" style="background: #e74c3c;">Logout</a>
            <?php else: ?>
                <a href="index.php" class="btn-nav-login">Login</a>
                <a href="signup.php" class="btn-nav-signup">Sign Up</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="main-content">
        <div class="signup-card">
            <h2>Sign Up for Hotel Elite</h2>
            
            <?php if ($error != "") { echo "<div class='alert-error'>$error</div>"; } ?>
            <?php if ($success != "") { echo "<div class='alert-success'>$success</div>"; } ?>
            
            <form method="POST" action="" autocomplete="off">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" required placeholder="Enter your name" autocomplete="off">
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="Enter your email" autocomplete="off">
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Enter your password" autocomplete="off">
                </div>
                
                <button type="submit" class="btn-submit">Sign Up</button>
            </form>

            <div class="login-link">
                Already have an account? <a href="index.php">Login</a>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Hotel Elite, Ahmedabad. All Rights Reserved.</p>
    </footer>
</body>
</html>