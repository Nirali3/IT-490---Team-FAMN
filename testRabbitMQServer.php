#!/usr/bin/php
<?php

require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('connection.php'); // ✅ Use the existing database connection

// ✅ Login function
function doLogin($username, $password) {
    global $con; // ✅ Use the connection from connection.php

    // Use a prepared statement to prevent SQL injection
    $stmt = $con->prepare("SELECT password_hash FROM register WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        return ["success" => false, "message" => "User not found"];
    }

    // Fetch stored hashed password
    $stmt->bind_result($storedPassword);
    $stmt->fetch();

    // Verify password
    if (password_verify($password, $storedPassword)) {
        return ["success" => true, "message" => "Login successful"];
    } else {
        return ["success" => false, "message" => "Invalid password"];
    }
}

// ✅ Registration function
function registerUser($first_name, $last_name, $email, $username, $password) {
    global $con; // ✅ Use the connection from connection.php

    // Check if the username already exists
    $stmt = $con->prepare("SELECT username FROM register WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        return ["success" => false, "message" => "Username already exists"];
    }
    $stmt->close();

    // Hash password before storing
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert user into the database
    $stmt = $con->prepare("INSERT INTO register (firstName, lastName, email, username, password_hash) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $first_name, $last_name, $email, $username, $hashedPassword);

    if ($stmt->execute()) {
        return ["success" => true, "message" => "Registration successful"];
    } else {
        return ["success" => false, "message" => "Error registering user"];
    }
}

// ✅ Session validation function
function doValidate($sessionId) {
    return ["success" => true, "message" => "Session is valid"];
}

// ✅ RabbitMQ request processor
function requestProcessor($request) {
    echo "Received request" . PHP_EOL;
    print_r($request);

    if (!isset($request['type'])) {
        return ["error" => "ERROR: unsupported message type"];
    }

    switch ($request['type']) {
        case "login":
            return doLogin($request['username'], $request['password']);
        case "register":
            return registerUser($request['first_name'], $request['last_name'], $request['email'], $request['username'], $request['password']);
        case "validate_session":
            return doValidate($request['sessionId']);
        default:
            return ["error" => "Unknown request type"];
    }
}

// ✅ Start RabbitMQ Server
$server = new rabbitMQServer("testRabbitMQ.ini", "testServer");

echo "testRabbitMQServer BEGIN" . PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END" . PHP_EOL;
exit();

