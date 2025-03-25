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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        if (!isset($_POST['first_name'], $_POST['last_name'], $_POST['dob'], $_POST['cabin_class'], $_POST['age_group'], $_POST['card_number'], $_POST['cardholder_name'], $_POST['expiration_date'], $_POST['cvc'] $_POST['price'], $_POST['airline'], $_POST['departure'], $_POST['arrival'], $_POST['departure_time'], $_POST['arrival_time'])) {
            echo "Error: Missing required fields.";
            exit;
        }

        $first_names = $_POST['first_name'];
        $last_names = $_POST['last_name'];
        $dobs = $_POST['dob'];
        $cabin_classes = $_POST['cabin_class'];
        $age_groups = $_POST['age_group'];
        $card_number = $_POST['card_number'];
        $cardholder_name = $_POST['cardholder_name'];
        $expiration_date = $_POST['expiration_date'];
        $cvc = $_POST['cvc'];

	$price = $_POST['price'];
        $airline = $_POST['airline'];
        $departure = $_POST['departureAirport'];
        $arrival = $_POST['arrivalAirport'];
        $departure_time = $_POST['departureDate'];
        $arrival_time = $_POST['arrivalDate'];

        // Start DB transaction
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO Bookings (airline, departureAiprort, arrivalAirport, departureDate, arrivalDate, price) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $airline, $departureAirport, $arrivalAirport, $departureDate, $arrivalDate, $price]);
	$stmt->execute();
        $booking_id = $con->lastInsertId();
	$stmt->close();

        $stmt = $con->prepare("INSERT INTO Passengers (booking_id, first_name, last_name, dob, cabin_class, age_group, card_number, cardholder_name, expiration_date, cvc) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        foreach ($first_names as $index => $first_name) {
            $stmt->bind_param("isssssssss",
                $booking_id, 
                $first_names[$index],
                $last_names[$index],
                $dobs[$index],
                $cabin_classes[$index],
                $age_groups[$index],
                $card_number,
                $cardholder_name,
                $expiration_date,
                $cvc
            );

	   $stmt->execute();
        }
	
	$stmt->close();
        
        $pdo->commit();
        echo "<h3 style='color:green;'>Booking successful!</h3>";
	echo "<a href='homepage.php'>Return to Home</a>"
    } catch (Exception $e) {
        $con->rollBack();
        echo "Error: " . $e->getMessage();
    }
} else {
	echo "<p> Invalid request method. Please submit the form.</p>";
}

?>

