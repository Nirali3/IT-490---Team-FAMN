<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

session_start();

include "connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST"){
	$location = isset($_POST['Location']);
}
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Event Search</title>
</head>

<body>
<h1> Search Events </h1>
<h2> Search Events through Location or by Date </h2>

<form method="POST" action="searchEvents.php" id="searchform">
<input type="text" id="locationSearch" name="Location" placeholder="Enter any City"/>
<input type="text" id="dateSearch" name="Date" placeholder="Enter date to search events"/>

<input type="submit" name="search" value="Search Events"/>
</form>
</body>
</html>
