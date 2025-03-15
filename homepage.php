<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include "connection.php";

$loggedIn = isset($_SESSION['username']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real-Time Flight Tracker</title>
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

        <!-- Logout Button -->
        <?php if ($loggedIn): ?>
            <form action="logout.php" method="post" style="margin: 0;">
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        <?php endif; ?>
    </div>

    <!-- Main Content -->
    <div class="container">
        <h1>Welcome to Real-Time Flight Tracker</h1>
        <p>
            Track real-time flight status, search for global events, and book your flights with ease.
            Get personalized recommendations based on your travel history and receive instant notifications.
        </p>
    </div>

</body>
</html>
