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

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate required fields
    $required_fields = ['first_name', 'last_name', 'dob', 'cabin_class', 'age_group', 'card_number', 'cardholder_name', 'expiration_date', 'cvc', 'airline', 'departureAirport', 'destinationAirport', 'departureDate', 'arrivalDate', 'total_price'];
    
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field])) {
            die("Error: Missing required fields." . $field);
        }
    }

   // $passenger_id = $_SESSION['passenger_id'] ?? null;
  //  if (!$passenger_id) {
  //      die("You must be logged in to book a flight.");

  //  }

 // $user_id = $_SESSION['user_id']
//if (!$user_id) {
//    die("You must be logged in to book a flight.");
//}

    
    // Assigning POST data
    $first_names = $_POST['first_name'];
    $last_names = $_POST['last_name'];
    $dobs = $_POST['dob'];
    $cabin_classes = $_POST['cabin_class'];
    $age_groups = $_POST['age_group'];
    $card_number = $_POST['card_number'];
    $cardholder_name = $_POST['cardholder_name'];
    $expiration_date = $_POST['expiration_date'];
    $cvc = $_POST['cvc'];

    $price = htmlspecialchars($_POST['total_price']);
    $airline = htmlspecialchars($_POST['airline']);
    $departureAirport = htmlspecialchars($_POST['departureAirport']);
    $destinationAirport = htmlspecialchars($_POST['destinationAirport']);
    $departureDate = htmlspecialchars($_POST['departureDate']);
    $arrivalDate = htmlspecialchars($_POST['arrivalDate']);

    // Save to session
    $_SESSION['booking_info'] = [
        'airline' => $airline,
        'departure' => $departureDate,
        'arrival' => $arrivalDate,
        'departureAirport' => $departureAirport,
        'destinationAirport' => $destinationAirport,
        'price' => $price,
        'passengers' => [
            'first_name' => $first_names,
            'last_name' => $last_names,
            'dob' => $dobs,
            'cabin_class' => $cabin_classes,
            'age_group' => $age_groups
        ]
    ];

    // Optional: insert into database (you can uncomment this part later if needed)
    
    try {
        $con->begin_transaction();
	$user_id = $_SESSION['user_id'];
	if (!$user_id) {
	    die("You must be logged in to book a flight.");
	}

        $stmt = $con->prepare("INSERT INTO Bookings (user_id, airline, departureAirport, destinationAirport, departureDate, arrivalDate, price, card_number, cardholder_name, expiration_date, cvc) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssdssss", $user_id, $airline, $departureAirport, $destinationAirport, $departureDate, $arrivalDate, $price, $card_number, $cardholder_name, $expiration_date, $cvc);
        $stmt->execute();

        $booking_id = $con->insert_id;

        $stmt = $con->prepare("INSERT INTO Passengers (user_id, booking_id, first_name, last_name, dob, cabin_class, age_group) VALUES (?, ?, ?, ?, ?, ?)");

        foreach ($first_names as $index => $first_name) {
            $stmt->bind_param("isssss", $user_id, $booking_id, $first_names[$index], $last_names[$index], $dobs[$index], $cabin_classes[$index], $age_groups[$index]);
            $stmt->execute();
        }

        $con->commit();
    } catch (mysqli_sql_exception $e) {
        $con->rollback();
        die("MySQL Error: " . $e->getMessage());
    }
    

    header("Location: confirmation_page.php");
    exit();
} else {
    echo "Error: Invalid request.";
}
?>
