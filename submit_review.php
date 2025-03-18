<?php

session_start();

include "connection.php";

// Check if user is logged in
$loggedIn = isset($_SESSION['username']);
$username = $loggedIn ? $_SESSION['username'] : "Guest";

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $review_message = trim($_POST['review_message']);

    if (empty($review_message)) {
        $errors[] = "Please write a review";
        header("Location: userAccount.php");
	exit();
    }

}

if ($loggedIn) {
	$stmt = $con->prepare("INSERT INTO reviews (review, user) VALUES(?,?)");
	$stmt->bind_param("ss", $review_message, $username);
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
