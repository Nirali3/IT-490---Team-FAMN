<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

require 'vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

session_start();

if (!isset($_COOKIE['session_key'])) {
	die("Error: Session key not set. Please login.");
	header("LOcation: login.php");
	exit();
}

$session_key = $_COOKIE['session_key'];

// Set up RabbitMQ connection
$rabbitmq_host = '10.0.2.15'; 
$rabbitmq_user = 'guest';
$rabbitmq_pass = 'guest';

try {
    $connection = new AMQPStreamConnection($rabbitmq_host, 5672, $rabbitmq_user, $rabbitmq_pass);
    $channel = $connection->channel();
    $channel->queue_declare('auth_requests', false, true, false, false);

    // Send session validation request
    $request = json_encode(['action' => 'validate_session', 'session_key' => $session_key]);
    $msg = new AMQPMessage($request);
    $channel->basic_publish($msg, '', 'auth_requests');

    // Declare response queue
    list($callback_queue, ,) = $channel->queue_declare("", false, false, true, false);
    $corr_id = uniqid();
    $response = null;

    $callback = function ($msg) use (&$response, $corr_id) {
        if ($msg->get('correlation_id') == $corr_id) {
            $response = json_decode($msg->body, true);
        }
    };

    $channel->basic_consume($callback_queue, '', false, true, false, false, $callback);

    // Wait for response
    while (!$response) {
        $channel->wait();
    }

    // Close the channel and connection
    $channel->close();
    $connection->close();

    // If session is invalid, redirect to login page
    if ($response['status'] !== "valid") {
        header("Location: login.php");
        exit();
    }

} catch (Exception $e) {
    echo "Error: Unable to connect to authentication service. Try again later.";
    exit();
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
    <p>You are logged in successfully.</p>

    <form action="logout.php" method="post">
        <button type="submit">Logout</button>
    </form>
</body>
</html>

