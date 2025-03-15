<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

session_start();

include "connection.php";


?>

<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Event Search</title>
<style>

	button[type="submit"]{
		background-color: #0077b6;
		color: white;
		padding: 12px 24px;
		border: none;
		border-radius: 5px;
		font-size: 16px;
		display: inline-block;
		width: auto;
	}

	button[type="submit"]:hover{
		background-color: #cc0000;
	}

	input[type="text"]{
		width: 100%;
		max-width: 300px;
		padding: 10 15px;
		font-size: 16px;
		border: 2px solid #0077b6;
		border-radius: 5px;
	}

	h1{
		color: #0077b6;
	}

	h4{
		color: #0077b6;
	}

	.navbar {
		background-color: #0077b6; /* Dark Blue */
        	overflow: hidden;
        	display: flex;
        	justify-content: space-between;
        	align-items: center;
        	padding: 15px;
	}
	.navbar a {
		color: white;
        	text-decoration: none;
        	padding: 10px 20px;
        	display: inline-block;
	}

	.navbar a:hover {
		background-color: #005f87;
        	border-radius: 5px;
	}

</style>
</head>

<body>
<h1> Search Events </h1>
<h4> Search Events By Location </h4>

<!-- Navigation Bar -->
    <div class="navbar">
        <div>
            <a href="homepage.php">Home</a>
	    <a href="userAccount.php">User Account</a>
	    <a href="searchEvents.php">Search Events</a>
	    <a href="indexSearchFlight.php">Search Flights</a>
	    <a href="booking_flight.php">Book a Flight</a>
            <a href="confirmation.php">Confirmation</a>
            <a href="recommendation.php">Recommendations</a>
	</div>

<form method="POST" action="searchEvents.php" id="searchform">
<input type="text" id="locationSearch" name="Location" placeholder="Enter any City"/>
<button type="submit" name="search" value="Search Events"</button>
</form>
</body>
</html>
