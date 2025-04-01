<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

include "connection.php";

if (!isset($_SESSION['booking_info'])) {
    die("No booking data found.");
}

$booking_info = $_SESSION['booking_info'];
?>

<!DOCTYPE html>
<html lang="en"> 
<head>
    <meta charset="UTF-8">
    <title>Confirmation Page</title>
    <link rel="stylesheet" href="style-booking.css">
</head>
<body>

<div class="navbar">
    <a href="homepage.php">Home</a>
    <a href="userAccount.php">User Account</a>
    <a href="searchEvents.php">Search Events</a>
    <a href="indexSearchFlight.php">Search Flights</a>
    <a href="push_notifications.php">Notifications</a>
    <a href="recommendation.php">Recommendations</a>
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

<form method="POST">
    <button type="submit" name="confirm">Confirm</button>
</form>

</body>
</html>

