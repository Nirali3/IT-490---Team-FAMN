<?php
session_start();
include "connection.php";

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm'])) {
    header('Location: userAccount.php');
    exit();
}

// Retrieve booking information from session
if (!isset($_SESSION['booking_info'])) {
    die("No booking data found.");
}

$booking_info = $_SESSION['booking_info'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Confirmation</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f8ff;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .navbar {
            background-color: #0077b6;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            padding: 15px 20px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            background-color: #0096c7;
            border-radius: 8px;
            transition: background 0.3s ease;
        }

        .navbar a:hover {
            background-color: #005f87;
        }

        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 25px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        h2 {
            color: #0077b6;
            margin-bottom: 20px;
            border-bottom: 2px solid #0096c7;
            padding-bottom: 10px;
        }

        #flight-info, #passenger-info {
            margin-bottom: 30px;
        }

        #flight-info p, #passenger-info ul {
            font-size: 16px;
            line-height: 1.6;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        ul li {
            background: #eaf6ff;
            margin-bottom: 8px;
            padding: 10px;
            border-radius: 6px;
        }

        form {
            text-align: center;
        }

        button[type="submit"] {
            background-color: #0077b6;
            color: white;
            padding: 12px 25px;
            border: none;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #005f87;
        }

        .confirmation-heading {
            text-align: center;
            margin-bottom: 40px;
        }

        .confirmation-heading h1 {
            font-size: 28px;
            color: #0077b6;
        }

        .confirmation-heading p {
            font-size: 18px;
            color: #333;
        }
    </style>
</head>
<body>

<!-- Navigation -->
<div class="navbar">
    <a href="homepage.php">Home</a>
    <a href="userAccount.php">User Account</a>
    <a href="searchEvents.php">Search Events</a>
    <a href="indexSearchFlight.php">Search Flights</a>
    <a href="push_notifications.php">Notifications</a>
    <a href="recommendation.php">Recommendations</a>
</div>

<!-- Main Container -->
<div class="container">
    <div class="confirmation-heading">
        <h1>🎉 Booking Confirmation</h1>
        <p>Thank you! Please review your flight and passenger details below before confirming.</p>
    </div>

    <h2>Flight Information</h2>
    <div id="flight-info">
        <p><strong>Airline:</strong> <?= htmlspecialchars($booking_info['airline']) ?></p>
        <p><strong>Departure:</strong> <?= htmlspecialchars($booking_info['departureAirport']) ?> at <?= htmlspecialchars($booking_info['departure']) ?></p>
        <p><strong>Arrival:</strong> <?= htmlspecialchars($booking_info['destinationAirport']) ?> at <?= htmlspecialchars($booking_info['arrival']) ?></p>
        <p><strong>Price per Ticket:</strong> $<?= htmlspecialchars($booking_info['price']) ?></p>
    </div>

    <h2>Passenger Information</h2>
    <div id="passenger-info">
        <?php foreach ($booking_info['passengers']['first_name'] as $index => $fname): ?>
            <p><strong>Passenger <?= $index + 1 ?>:</strong></p>
            <ul>
                <li><strong>Name:</strong> <?= htmlspecialchars($fname) . ' ' . htmlspecialchars($booking_info['passengers']['last_name'][$index]) ?></li>
                <li><strong>Date of Birth:</strong> <?= htmlspecialchars($booking_info['passengers']['dob'][$index]) ?></li>
                <li><strong>Cabin Class:</strong> <?= htmlspecialchars($booking_info['passengers']['cabin_class'][$index]) ?></li>
                <li><strong>Age Group:</strong> <?= htmlspecialchars($booking_info['passengers']['age_group'][$index]) ?></li>
            </ul>
        <?php endforeach; ?>
    </div>

    <form method="POST" action="">
        <button type="submit" name="confirm">✅ Confirm Booking</button>
    </form>
</div>

<!-- Reusable Footer -->
<?php include 'footer.php'; ?>

</body>
</html>

