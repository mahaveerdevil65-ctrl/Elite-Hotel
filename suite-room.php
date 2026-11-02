<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', 1);

session_start();
include('db.php');

$room_id = 2; 

$review_success = "";
if (isset($_POST['submit_review'])) {
    $guest_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : mysqli_real_escape_string($conn, $_POST['guest_name']);
    $rating = (int)$_POST['rating'];
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);

    $conn->query("INSERT INTO room_reviews (room_id, guest_name, rating, comment) VALUES ($room_id, '$guest_name', $rating, '$comment')");
    $review_success = "Thank you! Your review has been submitted successfully.";
}

$reviews_query = $conn->query("SELECT * FROM room_reviews WHERE room_id = $room_id ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Elite - Executive Suite Details</title>
    <link rel="stylesheet" href="style.css">
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
            color: #ffc107;
            text-decoration: none;
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 18px;
            align-items: center;
            margin: 0;
            padding: 0;
            flex-wrap: wrap;
        }

        nav ul li a {
            color: #adb5bd;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: color 0.2s;
            white-space: nowrap;
        }

        nav ul li a:hover {
            color: #fff;
        }

        .back-btn-container {
            max-width: 1100px;
            margin: 20px auto 0 auto;
            padding: 0 20px;
        }

        .btn-back {
            display: inline-block;
            background: #0d1b2a;
            color: #fff;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            font-size: 0.95rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .btn-back:hover {
            background: #1b263b;
        }

        .room-header {
            background: linear-gradient(rgba(13, 27, 42, 0.8), rgba(13, 27, 42, 0.8)), url('https://images.unsplash.com/photo-1591088398332-8a7791972843');
            background-size: cover;
            background-position: center;
            color: #fff;
            padding: 50px 20px;
            text-align: center;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .room-header h2 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .badge {
            background: #f39c12;
            color: #fff;
            padding: 6px 15px;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 10px;
        }

        .price {
            font-size: 1.8rem;
            color: #f39c12;
            font-weight: bold;
            margin-top: 10px;
        }
        
        .room-info-highlight {
            background: rgba(255, 255, 255, 0.15);
            display: inline-block;
            padding: 8px 20px;
            border-radius: 6px;
            margin-top: 12px;
            font-size: 1rem;
            color: #fff;
            border: 1px solid rgba(243, 156, 18, 0.4);
        }

        .contact-box {
            background: rgba(13, 27, 42, 0.75);
            padding: 12px 20px;
            border-radius: 6px;
            margin-top: 15px;
            font-size: 0.9rem;
            color: #f8f9fa;
            display: inline-block;
            border: 1px solid rgba(255, 255, 255, 0.25);
            text-align: left;
            max-width: 600px;
        }

        .content-box {
            background: #fff;
            padding: 35px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
            text-align: center;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .gallery-grid img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .gallery-grid img:hover {
            transform: scale(1.03);
        }

        .amenities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 15px;
            margin-top: 20px;
            text-align: left;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .amenity-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.05rem;
            color: #444;
            background: #f9f9f9;
            padding: 10px 15px;
            border-radius: 5px;
        }

        .amenity-item span {
            color: #27ae60;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .review-card {
            background: #fdfdfe;
            border: 1px solid #e9ecef;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 12px;
            text-align: left;
        }

        .review-author {
            font-weight: bold;
            color: #0d1b2a;
            font-size: 0.95rem;
        }

        .review-rating {
            color: #f39c12;
            font-size: 0.9rem;
            margin: 4px 0;
        }

        .review-text {
            font-size: 0.9rem;
            color: #555;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
            font-size: 0.9rem;
            color: #333;
        }

        .form-group input, 
        .form-group select, 
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 0.95rem;
        }

        .btn-submit-review {
            background: #28a745;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-submit-review:hover {
            background: #218838;
        }

        .success-msg {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-weight: bold;
        }

        footer {
            background: #0d1b2a;
            color: #aaa;
            text-align: center;
            padding: 20px;
            font-size: 0.9rem;
            margin-top: auto;
        }
    </style>
</head>
<body>

    <header>
        <a href="home.php" class="logo">Hotel Elite</a>
        <nav>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="rooms.php" style="color: #fff; font-weight: bold;">Rooms</a></li>
                <li><a href="food.php">Food</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
                
                <?php if (isset($_SESSION['user_email'])): ?>
                    <li><a href="my_bookings.php">My Booking</a></li>
                    <li><a href="my_orders.php">My Order</a></li>
                    <li><a href="logout.php" style="color: #e74c3c; font-weight: bold;">Logout</a></li>
                <?php else: ?>
                    <li><a href="index.php" style="color: #28a745; font-weight: bold;">Login</a></li>
                    <li><a href="signup.php" style="color: #ffc107; font-weight: bold;">Signup</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    
    <div class="back-btn-container">
        <a href="rooms.php" class="btn-back">&larr; Back to Rooms</a>
    </div>

    <div class="container" style="max-width: 1100px; margin: 20px auto; padding: 0 20px;">
        
        <div class="room-header">
            <span class="badge">Executive Suite</span>
            <h2>Ultimate Luxury & Comfort</h2>
            <p style="margin-bottom: 10px; font-size: 1.1rem; color: #ddd;">Room Size: 500+ sq. ft. (Living Area + Bedroom)</p>
            <div class="price">₹3,800 <span style="font-size: 1rem; color: #ccc;">/ night</span></div>
            
            <div class="room-info-highlight">
                🛏️ King Size Luxury Bed &nbsp;|&nbsp; 👥 Capacity: Max 3 Adults + 1 Child
            </div>

            <br>
            <div class="contact-box">
                📞 <b>Reception / Booking Helpdesk:</b> +91 98765 43210 / 079-23456789<br>
                ✉️ <b>Email:</b> support@hotelelite.com | 🕒 <b>Availability:</b> 24/7 Open
            </div>
        </div>

        <div class="content-box">
            <h3 style="color: #0d1b2a; margin-bottom: 10px; border-bottom: 2px solid #f39c12; display: inline-block; padding-bottom: 5px;">Room Gallery</h3>
            <div class="gallery-grid">
                <img src="https://images.unsplash.com/photo-1591088398332-8a7791972843" alt="Suite View 1">
                <img src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b" alt="Suite View 2">
                <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945" alt="Suite View 3">
                <img src="https://images.unsplash.com/photo-1611892440504-42a792e24d32" alt="Suite View 4">
            </div>
        </div>

        <div class="content-box">
            <h3 style="color: #0d1b2a; margin-bottom: 10px; border-bottom: 2px solid #f39c12; display: inline-block; padding-bottom: 5px;">Room Facilities & Amenities</h3>
            
            <div class="amenities-grid">
                <div class="amenity-item"><span>✓</span> King Size Luxury Bed (Orthopedic)</div>
                <div class="amenity-item"><span>✓</span> Private Jacuzzi & Bathtub</div>
                <div class="amenity-item"><span>✓</span> Unlimited High-Speed Wi-Fi</div>
                <div class="amenity-item"><span>✓</span> Espresso Coffee Machine</div>
                <div class="amenity-item"><span>✓</span> Large Smart TV with Channels</div>
                <div class="amenity-item"><span>✓</span> 24/7 Personal Butler Service</div>
                <div class="amenity-item"><span>✓</span> Centralized AC & Smart Heating</div>
                <div class="amenity-item"><span>✓</span> Luxury Bathrobes & Toiletries</div>
                <div class="amenity-item"><span>✓</span> Electronic Safe Locker</div>
                <div class="amenity-item"><span>✓</span> Scenic City Skyline View</div>
                <div class="amenity-item"><span>✓</span> Free Breakfast & Evening Snacks</div>
            </div>
        </div>

        <div class="content-box">
            <h3 style="color: #0d1b2a; margin-bottom: 10px; border-bottom: 2px solid #f39c12; display: inline-block; padding-bottom: 5px;">Guest Reviews & Feedback</h3>

            <?php if (!empty($review_success)): ?>
                <div class="success-msg"><?php echo $review_success; ?></div>
            <?php endif; ?>

            <div style="margin-bottom: 25px;">
                <?php
                if ($reviews_query && $reviews_query->num_rows > 0) {
                    while ($rev = $reviews_query->fetch_assoc()) {
                        $stars = str_repeat("★", (int)$rev['rating']) . str_repeat("☆", 5 - (int)$rev['rating']);
                        echo "<div class='review-card'>";
                        echo "<div class='review-author'>" . htmlspecialchars($rev['guest_name']) . "</div>";
                        echo "<div class='review-rating'>" . $stars . "</div>";
                        echo "<div class='review-text'>" . htmlspecialchars($rev['comment']) . "</div>";
                        echo "</div>";
                    }
                } else {
                    echo "<p style='color: #777; font-style: italic; padding: 10px 0;'>No reviews for this room yet. Be the first guest to share your review below!</p>";
                }
                ?>
            </div>

            <h4 style="color: #0d1b2a; margin-bottom: 15px; text-align: left;">Leave Your Review</h4>
            <form method="POST" action="" autocomplete="off">
                <?php if (!isset($_SESSION['user_name'])): ?>
                    <div class="form-group">
                        <label>Your Name</label>
                        <input type="text" name="guest_name" required placeholder="Enter your full name" autocomplete="off">
                    </div>
                <?php endif; ?>
                <div class="form-group">
                    <label>Rating</label>
                    <select name="rating" required>
                        <option value="5">★★★★★ (5/5 - Excellent)</option>
                        <option value="4">★★★★☆ (4/5 - Very Good)</option>
                        <option value="3">★★★☆☆ (3/5 - Good)</option>
                        <option value="2">★★☆☆☆ (2/5 - Fair)</option>
                        <option value="1">★☆☆☆☆ (1/5 - Poor)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Your Feedback / Comment</label>
                    <textarea name="comment" rows="3" required placeholder="Write your experience staying in this suite..." autocomplete="off"></textarea>
                </div>
                <button type="submit" name="submit_review" class="btn-submit-review">Submit Review</button>
            </form>
        </div>

        <div style="text-align: center; margin-bottom: 40px;">
            <a href="booking.php?room_id=2" style="padding: 15px 40px; font-size: 1.2rem; background: #f39c12; color: #fff; border-radius: 5px; text-decoration: none; font-weight: bold; box-shadow: 0 4px 10px rgba(243,156,18,0.3); display: inline-block;">Book This Room Now</a>
        </div>

    </div>

    <footer>
        <p>&copy; 2026 Hotel Elite. All Rights Reserved.</p>
    </footer>

</body>
</html>