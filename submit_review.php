<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

include "connection.php";

// Check if user is logged in
$loggedIn = isset($_SESSION['username']);
$username = $loggedIn ? $_SESSION['username'] : "Guest";

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$review_message = isset($_POST['review_message']) ? trim($_POST['review_message']) : '';
	$booking_id = isset($_POST['booking_id']) ? trim($_POST['booking_id']) : '';
	$passenger_id = isset($_POST['passenger_id']) ? trim($_POST['passenger_id']) : '';
	
	if (empty($review_message)) {
		$errors[] = "Please write a review";
	}

}

if ($loggedIn) {
	$stmt = $con->prepare("INSERT INTO Reviews (comment, bookingid, passengerid) VALUES(?,?,?,?)");
	$stmt->bind_param("sss", $review_message, $booking_id, $passenger_id);
	$stmt->execute();
	$stmt->close();

	if($stmt->execute()){
		echo "Reveiw Submitted Successfully!";
		header("Location: homepage.php");
		exit();
	}
	else{
		echo "Please Write a Review: ";
		header("Location: userAccount.php");
		exit();
	}
}

?>

<!DOCTYPE html>
<html>
    <head>
        <style>
		.error { color: red; }
	</style>
</head>
</html>
	
