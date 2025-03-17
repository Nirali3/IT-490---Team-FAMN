<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include "connection.php";

// Check if user is logged in
$loggedIn = isset($_SESSION['username']);
$username = $loggedIn ? $_SESSION['username'] : "Guest";

// Fetch user's purchased flights & events
$purchasedFlights = [];
$purchasedEvents = [];

// Database query to get purchased flights
if ($loggedIn) {
    $stmt = $conn->prepare("SELECT * FROM booked_flights WHERE username = ?");
    $stmt->bind_param("s", $_SESSION['username']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $purchasedFlights[] = $row;
    }
    $stmt->close();

    // Database query to get purchased events
    $stmt = $conn->prepare("SELECT * FROM booked_events WHERE username = ?");
    $stmt->bind_param("s", $_SESSION['username']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $purchasedEvents[] = $row;
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
            <a href="booking_flight.php">Book a Flight</a>
            <a href="confirmation.php">Confirmation</a>
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
                    <p><strong>Flight:</strong> <?php echo htmlspecialchars($flight['flight_number']); ?></p>
                    <p><strong>From:</strong> <?php echo htmlspecialchars($flight['departure']); ?> → <strong>To:</strong> <?php echo htmlspecialchars($flight['arrival']); ?></p>
                    <p><strong>Date:</strong> <?php echo htmlspecialchars($flight['date']); ?></p>
                    <hr>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No flights booked yet.</p>
            <?php endif; ?>
        </div>

        <!-- Purchased Events Section -->
        <h3>My Purchased Events</h3>
        <div class="booking-list">
            <?php if (!empty($purchasedEvents)): ?>
                <?php foreach ($purchasedEvents as $event): ?>
                    <p><strong>Event:</strong> <?php echo htmlspecialchars($event['event_name']); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($event['event_location']); ?></p>
                    <p><strong>Date:</strong> <?php echo htmlspecialchars($event['event_date']); ?></p>
                    <hr>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No events booked yet.</p>
            <?php endif; ?>
        </div>

        <!-- Review & Ratings Section -->
        <h3>Write a Review</h3>
        <form class="review-form" action="submit_review.php" method="post">
            <textarea name="review" placeholder="Write your review here..." required></textarea>
            <br>
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
        <h3>Notifications</h3>
        <div class="notifications">
            <div class="notification-item">🔔 Flight update: Your flight from NYC to LA is delayed.</div>
            <div class="notification-item">📅 Reminder: Your concert in Chicago is tomorrow!</div>
            <div class="notification-item">💰 Special offer: 20% off your next flight booking!</div>
        </div>
    </div>

</body>
</html>
