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
        if (!isset($_POST['first_name'], $_POST['last_name'], $_POST['dob'], $_POST['cabin_class'], $_POST['age_group'], $_POST['card_number'], $_POST['cardholder_name'], $_POST['expiration_date'], $_POST['cvc'], $_POST['airline'], $_POST['departureAirport'], $_POST['destinationAirport'], $_POST['departureDate'], $_POST['arrivalDate'], $_POST['price'])) {
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

        // Flight Details
        $price = htmlspecialchars($_POST['price']);
        $airline = htmlspecialchars($_POST['airline']);
        $departureAirport = htmlspecialchars($_POST['departureAirport']);
        $destinationAirport = htmlspecialchars($_POST['destinationAirport']);
        $departureDate = htmlspecialchars($_POST['departureDate']);
        $arrivalDate = htmlspecialchars($_POST['arrivalDate']);

        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO Bookings (airline, departureAirport, destinationAirport, departureDate, arrivalDate, total_price, card_number, cardholder_name, expiration_date, cvc) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$airline, $departureAirport, $destinationAirport, $departureDate, $arrivalDate, $price, $card_number, $cardholder_name, $expiration_date, $cvc]);

        $booking_id = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO Passengers (booking_id, first_name, last_name, dob, cabin_class, age_group) VALUES (?, ?, ?, ?, ?, ?)");

        foreach ($first_names as $index => $first_name) {
            $stmt->execute([
                $booking_id, 
                htmlspecialchars($first_names[$index]),
                htmlspecialchars($last_names[$index]),
                htmlspecialchars($dobs[$index]),
                htmlspecialchars($cabin_classes[$index]),
                htmlspecialchars($age_groups[$index])
            ]);
        }

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
