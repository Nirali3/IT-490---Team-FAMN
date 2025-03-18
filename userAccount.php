<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include "connection.php";

// Check if user is logged in
$loggedIn = isset($_SESSION['username']);
$username = $loggedIn ? $_SESSION['username'] : "Guest";
$passenger_id = $_SESSION['passenger_id'] ?? null;

// Fetch user's purchased flights & events
$purchasedFlights = [];
$purchasedPassengers = [];

// Database query to get purchased flights
if ($loggedIn && $passenger_id) {
    $stmt = $con->prepare("SELECT * FROM Bookings WHERE passenger_id = ?");
    $stmt->bind_param("s", $_SESSION['passenger_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $purchasedFlights[] = $row;
    }
    $stmt->close();

    // Database query to get purchased events
    $stmt = $con->prepare("SELECT * FROM Passengers WHERE passenger_id = ?");
    $stmt->bind_param("s", $_SESSION['passenger_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $purchasedPassengers[] = $row;
    }
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Account - My Bookings & Reviews</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f8ff;
            color: #333;
            text-align: center;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background-color: #0077b6;
            overflow: hidden;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            display: inline-block;
        }

        .navbar a:hover {
            background-color: #005f87;
            border-radius: 5px;
        }

        .logout-btn {
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
        }

        .logout-btn:hover {
            background-color: #cc0000;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        h2 {
            color: #0077b6;
        }

        .booking-list {
            text-align: left;
            padding: 10px;
        }

        .review-form {
            margin-top: 20px;
        }

        textarea {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .submit-review {
            background: #0077b6;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .submit-review:hover {
            background: #005f87;
        }

        .notifications {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }

        .notification-item {
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }

        .rating {
            font-size: 20px;
            color: #0077b6;
        }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <div class="navbar">
        <div>
            <a href="homepage.php">Home</a>
            <a href="userAccount.php">User Account</a>
            <a href="searchEvents.php">Search Events</a>
            <a href="indexSearchFlight.php">Search Flights</a>
            <!-- <a href="booking_flight.php">Book a Flight</a> -->
           <!-- <a href="confirmation.php">Confirmation</a> -->
            <a href="push_notifications.php">Notification Center</a>
            <a href="recommendation.php">Recommendations</a>
        </div>

        <?php if ($loggedIn): ?>
            <form action="logout.php" method="post" style="margin: 0;">
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        <?php endif; ?>
    </div>

    <!-- User Account Content -->
    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
        <p>Here are your past bookings and reviews.</p>

        <!-- Purchased Flights Section -->
        <h3>My Purchased Flights</h3>
        <div class="booking-list">
            <?php if (!empty($purchasedFlights)): ?>
                <?php foreach ($purchasedFlights as $flight): ?>
                    <p><strong>Airline:</strong> <?php echo htmlspecialchars($flight['airline']); ?></p>
                    <p><strong>Flight:</strong> <?php echo htmlspecialchars($flight['flight_number']); ?></p>
                    <p><strong>From:</strong> <?php echo htmlspecialchars($flight['departure']); ?> → <strong>To:</strong> <?php echo htmlspecialchars($flight['arrival']); ?></p>
                    <p><strong>Flight:</strong> <?php echo htmlspecialchars($flight['flight_number']); ?></p>
                    <p><strong>Booking Date:</strong> <?php echo htmlspecialchars($flight['created_at']); ?></p>
                    <p><strong>Price:</strong> <?php echo htmlspecialchars($flight['price']); ?></p>
                    <hr>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No flights booked yet.</p>
            <?php endif; ?>
        </div>

        <!-- Purchased Passengers Section -->
        <h3>Passengers in my Booking</h3>
        <div class="booking-list">
            <?php if (!empty($purchasedPassengers)): ?>
                <?php foreach ($purchasedPassengers as $passenger): ?>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($passenger['first_name'] . " " . $passenger['last_name']); ?></p>
                    <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($passenger['dob']); ?></p>
                    <p><strong>Cabin Class:</strong> <?php echo htmlspecialchars($passenger['cabin_class']); ?></p>
                    <p><strong>Age Group:</strong> <?php echo htmlspecialchars($passenger['age_group']); ?></p>
                    <hr>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No passengers booked yet.</p>
            <?php endif; ?>
        </div>

        <!-- Review & Ratings Section -->
        <h3>Write a Review</h3>
        <form class="review-form" action="submit_review.php" method="post">
            <textarea name="review" placeholder="Write your review here..." required></textarea>
            <br>
            <input type="hidden" name="booking_id" value="<?php echo isset($purchasedFlights[0]['booking_id']) ? $purchasedFlights[0]['booking_id'] : ''; ?>">
            <input type="hidden" name="passenger_id" value="<?php echo isset($purchasedPassengers[0]['passenger_id']) ? $purchasedPassengers[0]['passenger_id'] : ''; ?>">
            <label>Rate: </label>
            <select name="rating" required>
                <option value="5">⭐⭐⭐⭐⭐</option>
                <option value="4">⭐⭐⭐⭐</option>
                <option value="3">⭐⭐⭐</option>
                <option value="2">⭐⭐</option>
                <option value="1">⭐</option>
            </select>
            <br>
            <button type="submit" class="submit-review">Submit Review</button>
        </form>

        <!-- Push Notifications Section -->
    <!--    <h3>Notifications</h3>
        <div class="notifications">
            <div class="notification-item">🔔 Flight update: Your flight from NYC to LA is delayed.</div>
            <div class="notification-item">📅 Reminder: Your concert in Chicago is tomorrow!</div>
            <div class="notification-item">💰 Special offer: 20% off your next flight booking!</div>
        </div>
    </div> 
        -->
</body>
</html>
