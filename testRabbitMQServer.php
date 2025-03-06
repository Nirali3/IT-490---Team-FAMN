#!/usr/bin/php
<?php

require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('connection.php');


function doLogin($username, $password)
{
    global $con; // Usamos la conexión global que viene del archivo connection.php
   $username = $con->real_escape_string($username);

    // Consultar el usuario y la contraseña en la base de datos
    $sql = "SELECT password_hash FROM register WHERE username = '$username' LIMIT 1";
    $result = $con->query($sql);

    if ($result->num_rows > 0) {
        // El usuario existe, obtenemos la contraseña almacenada
        $row = $result->fetch_assoc();
        $storedPassword = $row['password_hash'];

        // Verificar la contraseña usando password_verify si es un hash
        if (password_verify($password, $storedPassword)) {
            return ["success" => true, "message" => "Login successful"];
        }
    }
    
    // Si no encontramos el usuario o la contraseña no es válida
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
