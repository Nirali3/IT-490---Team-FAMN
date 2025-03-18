<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include "connection.php";

// Check if the user is logged in and get their name
$loggedIn = isset($_SESSION['username']);
$username = $loggedIn ? $_SESSION['username'] : "Guest";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore & Book | Your Event and Flight Tracker</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Light Blue Theme Styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f8ff; /* Light blue background */
        }

        .navbar {
            background-color: #0077b6; /* Dark Blue */
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
            text-align: center;
            margin-top: 50px;
        }

        h1 {
            color: #0077b6;
        }

        p {
            font-size: 18px;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.5;
        }

        .image-section {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }

        .image-section img {
            width: 40%;
            height: auto;
            border-radius: 10px;
            margin: 10px;
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
           <!-- <a href="booking_flight.php">Book a Flight</a>
            <a href="confirmation.php">Confirmation</a> -->
            <a href="push_notifications.php">Notification Center</a>
            <a href="recommendation.php">Recommendations</a>
        </div>

        <!-- Logout Button -->
        <?php if ($loggedIn): ?>
            <form action="logout.php" method="post" style="margin: 0;">
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        <?php endif; ?>
    </div>

    <!-- Main Content -->
    <div class="container">
        <h1>Welcome to our Events and Flight Booking Dashboard , <?php echo htmlspecialchars($username); ?>!</h1>
        <p>
            Explore events, find the perfect flights, and book your trips all in one place.
            Get personalized recommendations and track your flight in real time.
        </p>
    </div>

    <!-- Image Section -->
    <div class="image-section">
        <img src="images/events.jpg" alt="Events">
        <img src="images/flights.jpg" alt="Flights">
    </div>

</body>
</html>
