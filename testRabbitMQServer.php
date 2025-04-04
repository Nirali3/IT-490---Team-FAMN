#!/usr/bin/php
<?php

require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

include "connection.php";

function doLogin($username, $password)
{
    include "connection.php"; // Use your actual DB connection

    // Check if user exists in the database
    $stmt = $con->prepare("SELECT user_id, passenger_id, password FROM Users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Optional: you can hash passwords, for now we'll assume plain text match
        if ($row['password'] === $password) {
            return [
                "success" => 1,
                "message" => "Login successful",
                "user_id" => $row['user_id'],
                "passenger_id" => $row['passenger_id']
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
  echo "received request".PHP_EOL;
  print_r($request);

  if(!isset($request['type']))
  {
    return "ERROR: unsupported message type";
  }
  switch ($request['type'])
  {
    case "login":
      return doLogin($request['username'],$request['password']);
    case "validate_session":
      return doValidate($request['sessionId']);
    default:
      return ["error" => "Unknown request type"];
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>
