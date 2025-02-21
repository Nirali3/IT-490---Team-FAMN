<?php
$host = "100.69.138.84";
$password = "490AFMNprojectFinal";
$database = "project490_db";
$username = "backend_user";

$con = new mysqli($host, $username, $password, $database);
	
if ($con->connect_error){
	die("Failed to connect: " . $con->connect_error);
}
else{
	echo "Connection Successful";
}
?>

