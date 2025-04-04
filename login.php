<?php
error_reporting(E_ALL & ~E_DEPRECATED); 
ini_set('display_errors', 1);

session_start();

include "connection.php";
require_once('rabbitMQLib.inc');

$errors = [];

$usernameRegex = '/^[a-zA-Z][a-zA-Z0-9]{3,10}$/';
$passwordRegex = '/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&.])[A-Za-z\d@$!%*?&.]{7,}$/'; 

if ($_SERVER["REQUEST_METHOD"] == "POST"){
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);

	if (empty($username)){
		$errors[] = "USERNAME CANNOT BE EMPTY";
	}

	if (empty($password)){
		$errors[] = "PASSWORD CANNOT BE EMPTY";
	}
	
	if (!empty($username) && !empty($password)){
		validateUsername($username, $usernameRegex);
		validatePassword($password, $passwordRegex);
	}

	if (isset($_POST['login']) && empty($errors)) {

		$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");


		$request = array(
			'type' => "login",
			'username' => $username,
			'password' => $password
		);

		$response = $client->send_request($request);

		if(isset($response['success']) && $response['success'] == 1){
			session_start();
			$_SESSION["username"] = $username;
			$_SESSION["passenger_id"] = $response["passenger_id"];

			$_SESSION["user_id"] = $response["user_id"];
			
			ob_end_clean();
			header("Location: homepage.php");
			exit();
		}else{
			$errors[] = $response['message'] ?? "Login failed.";
		}

	}


	if (isset($_POST['register'])){
		header("Location: register.php");
		exit();
	}
	
}

//validation

function validateUsername($username, $usernameRegex) {
	global $errors;
	if (!preg_match($usernameRegex, $username)) {
		$errors[] = "INVALID USERNAME. PLEASE ENTER AGAIN.";
	}
}

function validatePassword($password, $passwordRegex) {
	global $errors;
	if (!preg_match($passwordRegex, $password)) {
            $errors[] = "PASSWORD MUST BE 7 CHARACTERS LONG. IT SHOULD INCLUDE: AN UPPERCASE LETTER, A NUMBER, AND A SPECIAL CHARACTER. PLEASE RE-ENTER.";
      }
}
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Page</title>
<style>
	.error { color: red; }

	body{
		font-family: Arial, sans-serif;
		background-color: #f4f4f9;
		color: #333;
		text-align: center;
		margin: 0;
		padding: 0;
	}

	button[type="submit"]{
		background-color: #0077b6;
		color: white;
		padding: 12px 24px;
		border: none;
		border-radius: 5px;
		font-size: 18px;
		display: inline-block;
		width: auto;
	}

	button[type="submit"]:hover{
		background-color: #005f87;
	}

	input[type="text"]{
		width: 20%;
		height: 30px;
		padding: 10 0px;
		margin: 20px auto;
		font-size: 18px;
	}

	input[type="password"]{
		width: 20%;
		height: 30px;
		padding: 10 0px;
		margin: 20px auto;
		font-size: 18px;
	}

	h1, h2{
		font-family: Arial, Sans-serif;
		font-size: 24px;
		text-align: center;
		color: #0077b6;
	}

	label{
		font-size: 18px;
		font-family: Arial, sans-serif;
		margin-bottom: 5px;
	}

	#loginform{
		width: 100%;
	}
</style>
</head>

<body>
<h1> Real Time Flight Tracker </h1>
<h2> User Login </h2>
<form method="POST" action="login.php" id="loginform">
	<p>
		<label>Username:</label>
		<input type="text" id="username" name="username" />
	</p>

	<p>
		<label>Password:</label>
		<input type="password" id="password" name="password" />
	</p>

	<button type="submit" name="register" value="Register">Register</button>
	<button type="submit" name="login" value="Login">Login</button>

</form>

 <?php if (!empty($errors)): ?>
	<div class="error">
		<h3>Errors:</h3>
		<ul>
			<?php foreach($errors as $error): ?>
			    <li><?= htmlspecialchars($error) ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>
</body>
</html>
