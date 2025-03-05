#!/usr/bin/php
<?php

require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function doLogin($username,$password)
{
    $validUsers = [ 
	"test" => "Am12345."
	];

	if (isset($validUsers[$username]) && $validUsers[$username] === $password) {
		return ["success" => true, "message" => "Login successful"];
	}

    //return false if not valid

    		return ["success" => false, "message" => "Invalid credentials"];
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
