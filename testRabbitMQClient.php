#!/usr/bin/php
<?php
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

try {
	$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
	
	$username = isset($argv[1]) ? $argv[1] : "defaultUser";
	$password = isset($argv[2]) ? $argv[2] : "defaultPass";
	$msg = isset($argv[3]) ? $argv[3] : "test message";
	$request = array();
	$request['type'] = "login";
	$request['username'] = $username;
	$request['password'] = $password;
	$request['message'] = $msg;
	$response = $client->send_request($request);
//$response = $client->publish($request);

	if ($response === false) {
		echo "Error: No response from server.\n";
	} elseif (isset($response['error'])) {
		echo "Server Error: " . $response['error'] . "\n";
	} else {
		echo "Client received response: " . PHP_EOL;
		print_r($response);
		echo "\n\n";
	}
} catch (Exception $e) {
	echo "RabbitMQ Error: " . $e->getMessage() . "\n";
}
echo $argv[0]." END".PHP_EOL;

?>
