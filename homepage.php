<?php
require 'vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Check if session key exists
if (!isset($_COOKIE['session_key'])) {
    header("Location: login.php");
    exit();
}

$session_key = $_COOKIE['session_key'];

// Set up RabbitMQ connection
$rabbitmq_host = 'your_rabbitmq_server_ip'; // Change this to your RabbitMQ server IP
$rabbitmq_user = 'auth_user';
$rabbitmq_pass = 'auth_pass123';

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

