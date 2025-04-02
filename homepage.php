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
            padding: 10px 20px;
	    flex-wrap: wrap;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
   	    margin: 5px;
            display: inline-block;
            margin: 5px;
	    background-color: #0096c7;
	    border-radius: 6px;
  	    transition: background-color 0.3s ease;
	    front-weight: bold;
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

        .hero {
            position: relative;
            width: 100%;
            height: 300px;
            background: url('images/flightimage.jpeg') no-repeat center center/cover;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
        }

        .hero-overlay {
            position: absolute;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5); /* Dark overlay */
        }

        .hero-content {
            position: relative;
            z-index: 2;
            font-size: 28px;
            font-weight: bold;
            padding: 20px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
        }

        /* CTA Button */
        .cta-button {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 25px;
            background-color: #ff6600;
            color: white;
            font-size: 20px;
            text-decoration: none;
            border-radius: 5px;
        }

        .cta-button:hover {
            background-color: #cc5500;
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
            gap: 20px;
            margin-top: 30px;
        }

        .image-section img {
            width: 40%;
            height: auto;
            border-radius: 10px;
            margin: 10px;
        }

	    /* Footer Styles */
        footer {
            background-color: #0077b6;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 60px;
            border-top: 5px solid #0096c7;
            font-size: 16px;
        }

        footer a {
            color: #ffd166;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }

	/* 🎯 Responsive Styling */
    @media screen and (max-width: 768px) {
      .navbar {
        flex-direction: column;
        align-items: stretch;
        text-align: center;
      }

      .navbar a {
        width: 100%;
        margin: 5px 0;
      }

      .hero {
        height: 200px;
      }

      .hero-content {
        font-size: 20px;
        padding: 15px;
      }

      .container p {
        font-size: 16px;
      }

      .image-section img {
        width: 90%;
      }
    }

    @media screen and (max-width: 480px) {
      .hero-content {
        font-size: 18px;
        padding: 10px;
      }

      .cta-button {
        font-size: 16px;
        padding: 10px 20px;
      }

      footer {
        font-size: 14px;
        padding: 15px;
      }
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

    <!-- Hero Section -->
    <div class="hero">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            Explore & Book Your Journey <br>
            <!--<a href="booking_flight.php" class="cta-button">Book Now</a> -->
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <h1>Welcome to our Events and Flight Booking Dashboard , <?php echo htmlspecialchars($username); ?>!</h1>
        <p>
            Explore events, find the perfect flights, and book your trips all in one place.
            Enjoy smart recommendations and real-time travel updates!
	    Discover trending concerts, festivals, and experiences happening near you or across the globe.
	    Track flights in real-time, compare prices, and book with ease.
	    Receive personalized alerts, seamless itinerary updates, and travel tips tailored just for you.
	    Whether you are planning a quick getaway or a dream vacation, we have got everything you need to make it memorable.
        </p>
    </div>

    <!-- Image Section -->
<!--     <div class="image-section">
        <img src="images/flightimages.jpeg" alt="Flights">
    </div>
 -->

<!-- Footer Section -->
<footer>
	&copy; <?php echo date("Y"); ?> Explore & Book | Built with 💙 by Team FAMN |
        <a href="mailto:info@example.com">Contact Us</a>
</footer>
</body>
</html>
