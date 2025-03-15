<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

session_start();

include "connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST"){
	$eventName = isset($_POST['eventName']);
}

?>

<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Book Events</title>
</head>

<body>
<h1> Book Events </h1>

<form method="POST" action="bookEvents.php" id="bookform">
<label> Event Title: </label>
<input type="text" id="eventName" name="eventName"/>

<label> Event Time: </label>
<input type="text" id="ime" name="time"/>

<label> Event Location: </label>
<input type="text" id="location" name="location"/>

<label> 

<input type="submit" name="book" value="Book Events"/>
</form>
</body>
</html>
