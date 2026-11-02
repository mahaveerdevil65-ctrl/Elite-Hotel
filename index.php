<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', 1);

session_start();
include('db.php');

$error = "";

if (isset($_GET['msg'])) {
    $error = htmlspecialchars($_GET['msg']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // 1. Staff Login Check
    $staff_result = $conn->query("SELECT * FROM staff WHERE email = '$email'");
    if ($staff_result && $staff_result->num_rows > 0) {
        $staff = $staff_result->fetch_assoc();
        if ($password === $staff['password'] || password_verify($password, $staff['password'])) {
            $_SESSION['staff_email'] = $staff['email'];
            $_SESSION['staff_name'] = $staff['name'];
            $_SESSION['staff_role'] = $staff['role'];
            header("Location: dashboard.php");
            exit();
        }
    }

    // 2. Admin Login Check
    $admin_result = $conn->query("SELECT * FROM admin");
    if ($admin_result && $admin_result->num_rows > 0) {
        while ($admin = $admin_result->fetch_assoc()) {
            $db_user = $admin['email'] ?? $admin['username'] ?? $admin['user'] ?? '';
            $db_pass = $admin['password'] ?? '';
            if ($email === $db_user && $password === $db_pass) {
                $_SESSION['admin'] = $db_user;
                header("Location: dashboard.php");
                exit();
            }
        }
    }

    // 3. User Login Check
    $user_result = $conn->query("SELECT * FROM users WHERE email = '$email'");
    if ($user_result && $user_result->num_rows > 0) {
        $user = $user_result->fetch_assoc();
        
        // Check password using direct match or hashed verification
        if ($password === $user['password'] || password_verify($password, $user['password'])) {
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name'];
            header("Location: home.php");
            exit();
        } else {
            $error = "Incorrect Password!";
        }
    } else {
        $error = "No account found with this email!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Hotel Elite</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f4f7f6;
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

        .login-card {
            background: #fff;
            padding: 40px 35px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 420px;
            text-align: center;
        }

        h2 {
            color: #111827;
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 25px;
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

        input[type="email"], 
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 0.95rem;
            outline: none;
        }

        input[type="email"]:focus, 
        input[type="password"]:focus {
            border-color: #f59e0b;
        }

        .btn-login {
            background: #111827;
            color: #fff;
            border: none;
            padding: 12px;
            width: 100%;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            margin-top: 5px;
            transition: background 0.2s;
        }

        .btn-login:hover {
            background: #1f2937;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 0.85rem;
            font-weight: bold;
        }

        .links {
            margin-top: 20px;
            font-size: 0.9rem;
            color: #555;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .links a {
            color: #f59e0b;
            text-decoration: none;
            font-weight: bold;
        }

        .links a:hover {
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
            <?php else: ?>
                <a href="index.php" class="btn-nav-login">Login</a>
                <a href="signup.php" class="btn-nav-signup">Sign Up</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="main-content">
        <div class="login-card">
            <h2>Login to Hotel Elite</h2>
            
            <?php if (!empty($error)) { echo "<div class='error'>$error</div>"; } ?>
            
            <form method="POST" action="" autocomplete="off">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="Enter email" autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Enter password" autocomplete="off">
                </div>
                <button type="submit" class="btn-login">Login</button>
            </form>
            
            <div class="links">
                <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Hotel Elite, Ahmedabad. All Rights Reserved.</p>
    </footer>

</body>
</html>