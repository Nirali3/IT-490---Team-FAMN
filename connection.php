<?php
$host = "100.69.138.84";
$password = "490AFMNprojectFinal";
$database = "project490_db";
$username = "backend_user";

$con = mysqli_connect($host,$username,$password,$database);
	
if(mysqli_connect_errno()){
	echo "Failed to connect: " . mysqli_connect_error();
}
//else{
//	echo "Connection Successful";
//}
?>
