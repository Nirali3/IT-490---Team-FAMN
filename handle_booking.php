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
        if (!isset($_POST['first_name'], $_POST['last_name'], $_POST['dob'], $_POST['cabin_class'], $_POST['age_group'], $_POST['card_number'], $_POST['cardholder_name'], $_POST['expiration_date'], $_POST['cvc'])) {
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

        if (!isset($_GET['price'], $_GET['airline'], $_GET['departure'], $_GET['arrival'], $_GET['departure_time'], $_GET['arrival_time'])) {
            echo "Error: Missing flight details.";
            exit;
        }

        // Flight Details
        $price = $_GET['price'];
        $airline = $_GET['airline'];
        $departure = $_GET['departure'];
        $arrival = $_GET['arrival'];
        $departure_time = $_GET['departure_time'];
        $arrival_time = $_GET['arrival_time'];

        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO bookings (airline, departure, arrival, departure_time, arrival_time, price) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$airline, $departure, $arrival, $departure_time, $arrival_time, $price]);

        $booking_id = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO passengers (booking_id, first_name, last_name, dob, cabin_class, age_group, card_number, cardholder_name, expiration_date, cvc) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        foreach ($first_names as $index => $first_name) {
            $stmt->execute([
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
            ]);
        }
        
        $pdo->commit();
        echo "Booking is successful";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
