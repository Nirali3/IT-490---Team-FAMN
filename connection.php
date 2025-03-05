<?php
$host = "100.69.138.84";
$username = "backend_user";
$password = "490AFMNprojectFinal";
$database = "project490_db";

$con = new mysqli($host,$username,$password,$database);
	
if ($con->connect_error){
	die("Failed to connect: " . $con->connect_error);
}
else{
	echo "Connection Successful";
}
?>

