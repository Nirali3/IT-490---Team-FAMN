<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

session_start();

if (!isset($_COOKIE['session_key'])) {
	die("Error: Session key not set. Please login.");
	header("Location: login.php");
	exit();
}

$session_key = $_COOKIE['session_key'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h2>Welcome to Your Dashboard</h2>
    <p>You are logged in successfully.</p>

    <form action="logout.php" method="post">
        <button type="submit">Logout</button>
    </form>
</body>
</html>

