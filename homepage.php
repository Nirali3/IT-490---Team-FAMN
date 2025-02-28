<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

session_start();

include "connection.php";

if (!isset($_SESSION['username'])) {
	header("Location: login.php");
	exit();
}

$login_message = "";
if (isset($_SESSION['login_success']) && $_SESSION['login_success']) {
	$login_message = "<p style='color: green;'>Login successful!</p>";
	unset($_SESSION['login_success']);
}
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

    <?php echo $login_message; ?>
    <p>You are logged in successfully.</p>

    <form action="logout.php" method="post">
        <button type="submit">Logout</button>
    </form>
</body>
</html>

