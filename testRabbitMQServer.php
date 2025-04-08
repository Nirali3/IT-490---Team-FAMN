#!/usr/bin/php
<?php

require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
include "connection.php"; // assumes $con is your MySQLi connection

function doLogin($username, $password)
{
    global $con;

    // Prepare query to prevent SQL injection
    $stmt = $con->prepare("SELECT id, password_hash FROM register WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Use password_verify if you're storing hashed passwords (recommended)
        if (password_verify($password, $row['password_hash'])) {
            return [
                "success" => 1,
                "user_id" => $row['id'],
                "message" => "Login successful"
            ];
        } else {
            return ["success" => 0, "message" => "Incorrect password"];
        }
    } else {
        return ["success" => 0, "message" => "User not found"];
    }
}

function doValidate($sessionId)
{
    return ["success" => true, "message" => "Session is valid"];
}

function requestProcessor($request)
{
    echo "received request" . PHP_EOL;
    print_r($request);

    if (!isset($request['type'])) {
        return "ERROR: unsupported message type";
    }

    switch ($request['type']) {
        case "login":
            return doLogin($request['username'], $request['password']);
        case "validate_session":
            return doValidate($request['sessionId']);
        default:
            return ["error" => "Unknown request type"];
    }

    return ["returnCode" => '0', 'message' => "Server received request and processed"];
}

$server = new rabbitMQServer("testRabbitMQ.ini", "testServer");

echo "testRabbitMQServer BEGIN" . PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END" . PHP_EOL;
exit();
?>
