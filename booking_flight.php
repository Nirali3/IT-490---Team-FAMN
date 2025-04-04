<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

session_start();

include "connection.php";
require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable('/var/www/html/', 'api.env');
$dotenv->load();

$api_key = $_ENV['GOOGLE_API_KEY'];

// Validación de parámetros
if (!isset($_GET['price'], $_GET['airline'], $_GET['departureAirport'], $_GET['destinationAirport'], $_GET['departureDate'], $_GET['arrivalDate'])) {
    die("Error: Missing flight details");
}

// Asignación de variables
$price = htmlspecialchars($_GET['price']);
$airline = htmlspecialchars($_GET['airline']);
$departureAirport = htmlspecialchars($_GET['departureAirport']);
$destinationAirport = htmlspecialchars($_GET['destinationAirport']);
$departureDate = htmlspecialchars($_GET['departureDate']);
$arrivalDate = htmlspecialchars($_GET['arrivalDate']);
?>

<!DOCTYPE html>
<html lang="en"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flight Booking</title>
    <link rel="stylesheet" href="style-booking.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="add-passenger.js"></script>
</head>
<body>

    <!-- Navigation Bar -->
    <div class="navbar">
        <div>
            <a href="homepage.php">Home</a>
            <a href="userAccount.php">User Account</a>
            <a href="searchEvents.php">Search Events</a>
            <a href="indexSearchFlight.php">Search Flights</a>
            <a href="push_notifications.php">Notifications</a>
            <a href="recommendation.php">Recommendations</a>
        </div>
    </div>

    <h2>Flight Information</h2>
    <div id="flight-info">
        <p><strong>Airline:</strong> <?= $airline ?></p>
        <p><strong>Departure:</strong> <?= $departureAirport ?> at <?= $departureDate ?></p>
        <p><strong>Arrival:</strong> <?= $destinationAirport ?> at <?= $arrivalDate ?></p>
        <p id="flight-price" data-price="<?= $price ?>"><strong>Price per Ticket:</strong> $<?= $price ?></p>
    </div>

    <h2>Passenger Details</h2>
    <form method="POST" action="handle_booking.php">
	<input type="hidden" name="total_price" value="<?= $price ?>">
	<input type="hidden" name="airline" value="<?= $airline ?>">
	<input type="hidden" name="departureAirport" value="<?= $departureAirport ?>">
	<input type="hidden" name="destinationAirport" value="<?= $destinationAirport ?>">
	<input type="hidden" name="departureDate" value="<?= $departureDate ?>">
	<input type="hidden" name="arrivalDate" value="<?= $arrivalDate ?>">
        <div id="passenger-section">
            <div class="passenger">
                <label>First Name:</label>
                <input type="text" name="first_name[]" required>

                <label>Last Name:</label>
                <input type="text" name="last_name[]" required>

                <label>Date of Birth:</label>
                <input type="date" name="dob[]" required>

                <label>Cabin Class:</label>
                <select name="cabin_class[]" required>
                    <option>Choose a Cabin Class</option>
                    <option value="Economy Class">Economy Class</option>
                    <option value="Business Class">Business Class</option>
                    <option value="First Class">First Class</option>
                </select>

                <label>Passenger Age Group:</label>
                <select name="age_group[]" required>
                    <option>Choose an Age Group</option>
                    <option value="Infant">Infant</option>
                    <option value="Child">Child</option>
                    <option value="Adult">Adult</option>
                </select>
            </div>
        </div>

        <button type="button" id="add-passenger">Add Passenger</button>

        <h2>Payment Details:</h2>
	<div id="payment-section">
            <label>Card Number:</label>
            <input type="text" name="card_number" required pattern="\d{16}" title="Card number must be 16 digits">

            <label>Cardholder's Name:</label>
            <input type="text" name="cardholder_name" required>
        
            <label>Expiration Date:</label>
            <input type="month" name="expiration_date" required min="<?= date('Y-m') ?>" title="Expiration year and month">

            <label>CVC:</label>
            <input type="text" name="cvc" required pattern="\d{3}" title="CVC must be 3 digits">

            <button type="submit" name="submit">Book Tickets</button>
	</div>
    </form>

    <script>
        $(document).ready(function() {
            $("#add-passenger").click(function() {
                var passengerForm = $('.passenger').first().clone();
                $('#passenger-section').append(passengerForm);
            });
        });
    </script>

<?php include 'footer.php'; ?>

</body> 
</html>
