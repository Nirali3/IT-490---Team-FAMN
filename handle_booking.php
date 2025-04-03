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
    $required_fields = ['first_name', 'last_name', 'dob', 'cabin_class', 'age_group', 'card_number', 'cardholder_name', 'expiration_date', 'cvc', 'airline', 'departureAirport', 'arrivalAirport', 'departureDate', 'arrivalDate', 'total_price'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field])) {
            die("Error: Missing required fields.");
        }
    }

    if (!isset($_SESSION['passenger_id'])) {
        die("User not logged in.");
    }

    $passenger_id = $_SESSION['passenger_id'];

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

    try {
        $con->begin_transaction();

        $stmt = $con->prepare("INSERT INTO bookings (passenger_id, airline, departureAirport, destinationAirport, departureDate, arrivalDate, total_price, card_number, cardholder_name, expiration_date, cvc) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssdssss", $passenger_id, $airline, $departureAirport, $destinationAirport, $departureDate, $arrivalDate, $price, $card_number, $cardholder_name, $expiration_date, $cvc);
        $stmt->execute();
        $booking_id = $con->insert_id;

        $stmt = $con->prepare("INSERT INTO passengers (booking_id, first_name, last_name, dob, cabin_class, age_group) VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($first_names as $index => $fname) {
            $stmt->bind_param("isssss", $booking_id, $fname, $last_names[$index], $dobs[$index], $cabin_classes[$index], $age_groups[$index]);
            $stmt->execute();
        }

        $con->commit();
        header("Location: confirmation_page.php");
        exit();
    } catch (Exception $e) {
        $con->rollback();
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Error: Invalid request.";
}
?>
