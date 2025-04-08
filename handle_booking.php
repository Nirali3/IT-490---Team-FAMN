<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    die("Please log in to book flight");
}
$user_id = $_SESSION['user_id'];
echo "UserID: " . $_SESSION['user_id'];

include "connection.php";
require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable('/var/www/html/', 'api.env');
$dotenv->load();

$api_key = $_ENV['GOOGLE_API_KEY'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate required fields
    $required_fields = [
        'first_name', 'last_name', 'dob', 'cabin_class', 'age_group',
        'card_number', 'cardholder_name', 'expiration_date', 'cvc',
        'airline', 'departureAirport', 'destinationAirport', 'departureDate', 'arrivalDate', 'total_price'
    ];
    
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field])) {
            die("Error: Missing required field: " . $field);
        }
    }

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

    $price = floatval($_POST['total_price']);
    $airline = $_POST['airline'];
    $departureAirport = $_POST['departureAirport'];
    $destinationAirport = $_POST['destinationAirport'];

    // ✅ Convert datetime strings to MySQL-compatible format
    $departureDateRaw = $_POST['departureDate'];
    $arrivalDateRaw = $_POST['arrivalDate'];

    $departureDate = date('Y-m-d H:i:s', strtotime($departureDateRaw));
    $arrivalDate = date('Y-m-d H:i:s', strtotime($arrivalDateRaw));

    // Optional Debug:
    // echo "DEBUG - Dep: $departureDate | Arr: $arrivalDate"; exit;

    // Store in session (optional)
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

        $stmt = $con->prepare("INSERT INTO Bookings (user_id, airline, departureAirport, destinationAirport, departureDate, arrivalDate, price, card_number, cardholder_name, expiration_date, cvc) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssdsssss", $user_id, $airline, $departureAirport, $destinationAirport, $departureDate, $arrivalDate, $price, $card_number, $cardholder_name, $expiration_date, $cvc);
        $stmt->execute();

        $booking_id = $con->insert_id;

        $stmt = $con->prepare("INSERT INTO Passengers (user_id, booking_id, first_name, last_name, dob, cabin_class, age_group) VALUES (?, ?, ?, ?, ?, ?, ?)");

        foreach ($first_names as $index => $first_name) {
            $stmt->bind_param("iisssss", $user_id, $booking_id, $first_names[$index], $last_names[$index], $dobs[$index], $cabin_classes[$index], $age_groups[$index]);
            $stmt->execute();
        }

        $con->commit();
        header("Location: confirmation_page.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        $con->rollback();
        die("MySQL Error: " . $e->getMessage());
    }
} else {
    echo "Error: Invalid request.";
}
?>
