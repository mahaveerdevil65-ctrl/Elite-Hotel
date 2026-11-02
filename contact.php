<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', 1);

session_start();
include('db.php');

if (!isset($_SESSION['user_email']) || empty($_SESSION['user_email'])) {
    header("Location: index.php?msg=" . urlencode("Please login first to send a message!"));
    exit();
}

$success_msg = "";
$error_msg = "";

if (isset($_POST['send_message'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    if (!empty($name) && !empty($email) && !empty($message)) {
        if (preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i", $email)) {
            $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("sss", $name, $email, $message);
                $stmt->execute();
                $success_msg = "Thank you! Your message has been sent successfully. We will get back to you soon.";
            } else {
                $error_msg = "Database error! Please make sure 'contact_messages' table exists.";
            }
        } else {
            $error_msg = "Invalid email address! Please enter a valid email like name@gmail.com.";
        }
    } else {
        $error_msg = "Please fill in all the fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Elite - Contact Us</title>
    <link rel="stylesheet" href="style.css">
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

        .btn-nav-signup {
            background-color: #f59e0b;
            color: #fff;
            padding: 8px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
        }

        .contact-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            width: 100%;
            flex: 1;
        }

        .info-box {
            background: #111827;
            color: #fff;
            padding: 35px;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .info-box h3 {
            margin-top: 0;
            color: #f59e0b;
            font-size: 1.6rem;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .info-box p {
            line-height: 1.6;
            color: #cbd5e1;
            font-size: 0.95rem;
            margin-bottom: 12px;
        }

        .form-box {
            background: #fff;
            padding: 35px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .form-box h2 {
            color: #111827;
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 26px;
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
            font-size: 0.9rem;
        }

        .form-group input, 
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 0.95rem;
            outline: none;
        }

        .form-group input:focus, 
        .form-group textarea:focus {
            border-color: #f59e0b;
        }

        .form-group textarea {
            resize: vertical;
            height: 120px;
        }

        .btn-submit {
            background: #111827;
            color: #fff;
            border: none;
            padding: 12px;
            font-size: 1rem;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            transition: background 0.2s;
        }

        .btn-submit:hover {
            background: #1f2937;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 0.95rem;
            border: 1px solid #c3e6cb;
            text-align: center;
            font-weight: bold;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 0.95rem;
            border: 1px solid #f5c6cb;
            text-align: center;
            font-weight: bold;
        }

        footer {
            text-align: center;
            padding: 18px 20px;
            color: #fff;
            background: #111827;
            margin-top: auto;
            font-size: 0.95rem;
        }

        @media (max-width: 768px) {
            .contact-container {
                grid-template-columns: 1fr;
            }
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
                    <li><a href="contact.php" style="color: #f59e0b; font-weight: bold;">Contact</a></li>
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

    <div class="contact-container">
        <div class="info-box">
            <h3>Get In Touch</h3>
            <p>Have questions about rooms, dining, or bookings? Reach out to our front desk team anytime.</p>
            <br>
            <p><strong>Address:</strong><br>CG Road, Navrangpura, Ahmedabad, Gujarat</p>
            <p><strong>Phone:</strong><br>+91 98765 43210</p>
            <p><strong>Email:</strong><br>support@hotelelite.com</p>
        </div>

        <div class="form-box">
            <h2>Send Us a Message</h2>
            
            <?php if (!empty($success_msg)): ?>
                <div class="alert-success"><?php echo $success_msg; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($error_msg)): ?>
                <div class="alert-error"><?php echo $error_msg; ?></div>
            <?php endif; ?>

            <form action="contact.php" method="POST" autocomplete="off">
                <div class="form-group">
                    <label for="name">Your Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>" placeholder="Enter your full name" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="email">Your Email</label>
                    <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>" placeholder="Enter valid email (e.g., name@gmail.com)" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="message">Your Message</label>
                    <textarea id="message" name="message" placeholder="Write your message here..." required></textarea>
                </div>

                <button type="submit" name="send_message" class="btn-submit">Send Message</button>
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Hotel Elite, Ahmedabad. All Rights Reserved.</p>
    </footer>
</body>
</html>