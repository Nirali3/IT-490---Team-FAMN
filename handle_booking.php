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
        if (!isset(
            $_POST['first_name'], $_POST['last_name'], $_POST['dob'],
            $_POST['cabin_class'], $_POST['age_group'],
            $_POST['card_number'], $_POST['cardholder_name'],
            $_POST['expiration_date'], $_POST['cvc'],
            $_POST['price'], $_POST['airline'], $_POST['departure'],
            $_POST['arrival'], $_POST['departure_time'], $_POST['arrival_time']
        )) {
            echo "Error: Missing required fields.";
            exit;
        }

        // Retrieve form data
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
        $price = $_POST['price'];
        $airline = $_POST['airline'];
        $departure = $_POST['departure'];
        $arrival = $_POST['arrival'];
        $departure_time = $_POST['departure_time'];
        $arrival_time = $_POST['arrival_time'];

        // Insert into Bookings table
        $stmt = $con->prepare("INSERT INTO Bookings (airline, departureAirport, arrivalAirport, departureDate, arrivalDate, price) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssd", $airline, $departure, $arrival, $departure_time, $arrival_time, $price);
        $stmt->execute();
        $booking_id = $stmt->insert_id;
        $stmt->close();

        // Insert each passenger into Passengers table
        $stmt = $con->prepare("INSERT INTO Passengers (booking_id, first_name, last_name, dob, cabin_class, age_group, card_number, cardholder_name, expiration_date, cvc) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($first_names as $index => $first_name) {
            $stmt->bind_param(
                "isssssssss",
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

        // Redirect to confirmation page
        header("Location: confirmation_page.php");
        exit();

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request method.";
}
?>

