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
		echo "<script>alert('USERNAME CANNOT BE EMPTY. PLEASE INPUT A USERNAME')</script>";
	}

	if (empty($password)){
		echo "<script>alert('PASSWORD CANNOT BE EMPTY. PLEASE INPUT A PASSWORD')</script>";
	}

	if (isset($_POST['login']) && empty($errors)){
		$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

		$request = array();
		$request['type'] = "login";
		$request['username'] = $username;
		$request['password'] = $password;

		$response = $client->send_request($request);

		if($response === true){
			$_SESSION["username"] = $username;
			header("Location: homepage.php");
			exit();
		}

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
				echo "<script>alert('Invalid password!.Please try again')</script>";
			}
		} else {
			echo "<script>alert('USER DOES NOT EXIST! PLEASE CREATE AN ACCOUNT FIRST')</script>";
		}
		$stmt->close();
	} elseif (isset($_POST['register'])){
		header("Location: register.php");
		exit();
	}
}


$con->close();

//validation

function validateUsername($username, $usernameRegex){
	if (!preg_match($usernameRegex, $username)){
		echo "<script>alert('INVALID USERNAME. PLEASE ENTER A UNIQUE USERNAME')</script>";
	}
	else {
		echo "<script>alert('USERNAME VALID!')</script>";
	}
}

function validatePassword($password, $passwordRegex){
	if (!preg_match($passwordRegex, $password)){
		echo "<script>alert('INVALID PASSWORD. PASSWORD MUST HAVE A SPECIAL CHARACTER, NUMBER AND SHOULD BE 7 CHARACTERS LONG. PLEASE RE-ENTER')</script>";
	}
	else {
		echo "<script>alert('PASSWORD VALID!')</script>";
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

<input type="submit" name="register" value="Register"/>
<input type="submit" name="login" value="Login"/>

</form>
</body>
</html>
