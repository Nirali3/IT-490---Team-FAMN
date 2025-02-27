<?php
error_reporting(E_ALL); 
ini_set('display_errors', 1);

session_start();

include "connection.php";
require_once('rabbitMQLib.inc');

$errors = [];

$usernameRegex = '/^[a-zA-Z][a-zA-Z0-9]{3,10}$/';
$passwordRegex = '/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{7,}$/'; 

if ($_SERVER["REQUEST_METHOD"] == "POST"){
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);

	if (empty($username)){
		echo <script>alert("USERNAME CANNOT BE EMPTY")</script>;
	}

	if (empty($password)){
		echo <script>alert( "PASSWORD CANNOT BE EMPTY")</script>;
	}

	if (isset($_POST['login-button']) && empty($errors)){
		$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

		$request = array();
		$request['type' = "login";
		$request['username'] = $username;
		$request['password'] = $password;

		$response = $client->send_request($request);

		if($response === true){
			$_SESSION["username"] = $username;
			header("Location: homepage.php");
			exit();
		}

		if (empty($errors)){
			$stmt = $con->prepare("SELECT username, password_hash FROM login WHERE username = ?");
			$stmt->bind_param("s", $username);
			$stmt->execute();
			$stmt->store_result();
			
		if ($stmt->num_rows > 0) {
			$stmt->bind_result($username, $password_hash);
			$stmt->fetch();
			
			if (password_verify($password, $password_hash)) {
				$_SESSION["username"] = $username;
				header("Location: homepage.php");
				exit();
			}else {
				echo <script>alert("Invalid password!.Please try again")</script>;
			}
		} else {
			echo <script>alert("USER DOES NOT EXIST! PLEASE CREATE AN ACCOUNT FIRST")</script>;
		}
		$stmt->close();
	}
	} elseif (isset($_POST['register-button'])){
		header("Location: register.php");
		exit();
	}
}


$con->close();

//validation

function validateUsername($username, $usernameRegex){
	if (!preg_match($usernameRegex, $username)){
		echo "INVALID USERNAME. PLEASE ENTER A UNIQUE USERNAME";
	}
	else {
		echo "USERNAME VALID!";
	}
}

function validatePassword($password, $passwordRegex){
	if (!preg_match($passwordRegex, $password)){
		echo "INVALID PASSWORD. PASSWORD MUST HAVE A SPECIAL CHARACTER, NUMBER AND SHOULD BE 7 CHARACTERS LONG. PLEASE RE-ENTER";
	}
	else {
		echo "PASSWORD VALID!";
	}
}
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Page</title>
</head>

<body>
<h1> User Login </h1>
<form method="POST" action="login.php" id="loginform">
<p>
<label>Username:</label>
<input type="text" id="username" name="username"/>
</p>

<p>
<label>Password:</label>
<input type="password" id="password" name="password"/>
</p>

<input type="submit" name="register-button" value="Register"/>
<input type="submit" name="login-button" value="Login"/>

</form>
</body>
</html>
