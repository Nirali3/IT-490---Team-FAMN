<?php
error_reporting(E_ALL); 
ini_set('display_errors', 1);

session_start();

include "connection.php";

$errors = [];

$usernameRegex = '/^[a-zA-Z][a-zA-Z0-9]{3,10}$/';
$passwordRegex = '/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{7,}$/'; 

if ($_SERVER["REQUEST_METHOD"] == "POST"){
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);

	if (empty($username)){
		$errors[] = "USERNAME CANNOT BE EMPTY";
	}

	if (empty($password)){
		$errors[] = "PASSWORD CANNOT BE EMPTY";
	}
} else {

	$stmt = $con->prepare("SELECT username, password_hash FROM login WHERE username = ?");
    	$stmt->bind_param("s", $username);
    	$stmt->execute();
    	$stmt->store_result();
	
	if ($stmt->num_rows > 0) {
		$stmt->bind_result($username, $password);
		$stmt->fetch();
		
		if (password_verify($password)) {
			echo "Login successful!";
			$_SESSION["username"] = $username;
			header("Location: homepage.php");
			exit();
		} else {
			$errors[] = "Invalid password!";
		}
	} else {
		$errors[] = "USER DOES NOT EXIST! PLEASE CREATE AN ACCOUNT FIRST";
	}
	$stmt->close();
}

$con->close();

//validation

function validateUsername($username, $usernameRegex){
	if (!preg_match($usernameRegex, $username)){
		$errors[] = "INVALID USERNAME. PLEASE ENTER A UNIQUE USERNAME";
	}
	else {
		$errors[] = "VALID USERNAME!";
	}
}

function validatePassword($password, $passwordRegex){
	if (!preg_match($passwordRegex, $password)){
		$errors[] = "INVALID PASSWORD. PASSWORD MUST HAVE A SPECIAL CHARACTER, NUMBER AND SHOULD BE 7 CHARACTERS LONG. PLEASE RE-ENTER";
	}
	else {
		$errors[] = "VALID PASSWORD!";
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

<input type="button" name="register-button" value="Register"/>
<input type="button" name="login-button" value="Login"/>

</form>
</body>
</html>

