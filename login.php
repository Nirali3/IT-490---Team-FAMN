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

		// 💡 Add this for debugging
		echo "<pre>";
		print_r($response);
		echo "</pre>";

		if(isset($response['success']) && $response['success'] == 1){
			// 🔥 DO NOT start the session again here
			$_SESSION["username"] = $username;

		if (isset($response["user_id"])) {
			$_SESSION["user_id"] = $response["user_id"];
			} else {
				$errors[] = "Login succeeded but user_id was not returned!";
			}

		//if (isset($response["passenger_id"])) {
	//		$_SESSION["passenger_id"] = $response["passenger_id"];
	//}

			
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
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
	.error { color: red; }

	body{
		font-family: Arial, sans-serif;
		background-color: white;
		color: #333;
		text-align: center;
		align-items: center;
		justify-content: center;
		flex-direction: column;
		display: flex;
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

	.btn-login{
		background-color: #0077b6;
		color: white;
		padding: 12px 24px;
		border: none;
		border-radius: 5px;
		font-size: 18px;
		display: inline-block;
		width: auto;
	}
	
	.btn-login:hover{
		background-color: #005f87;
	}

	.btn-register{
		background-color: #0077b6;
		color: white;
		padding: 12px 24px;
		border: none;
		border-radius: 5px;
		font-size: 18px;
		display: inline-block;
		width: auto;
	}

	.btn-register:hover{
		background-color: #005f87;
	}

	.mb-3 {
		 margin-bottom: 15px;
	}

	.container{
		width: 90%;
		padding: 20px;
	}

	input[type="text"]{
		width: 90%;
		height: 30px;
		padding: 10 0px;
		margin: 20px auto;
		font-size: 18px;
		box-sizing: border-box;
	}

	input[type="password"]{
		width: 90%;
		height: 30px;
		padding: 10 0px;
		margin: 20px auto;
		font-size: 18px;
		box-sizing: border-box;
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
		font-weight: bold;
		margin-bottom: 5px;
	}

	.button-group{
		display: flex;
		justify-content: center;
		gap: 10px;
		margin-top: 20px;
	}

	#loginform{
		width: 100%;
	}

	/*@media (max-width: 768px) {
		input[type="text"], 
		input[type="password"] {
			width: 90%;
	}
}*/

	@media (max-width: 576px) {
		.btn-register, .btn-login {
			width: 100%;
		}
	}

</style>
</head>

<body>
<h1> Real Time Flight Tracker </h1>
<h2> User Login </h2>
<div class="container mt-4">
<div class="row justify-content-center">
<div class="col-md-6 col-sm-10 col-12">
<form method="POST" action="login.php" id="loginform">
<div class="mb-3">
	<label for="Username">Username:</label>
	<input type="text" class="form-control" id="username" name="username" />
</div>

<div class="mb-3">
	<label for="Password">Password:</label>
	<input type="password" class="form-control" id="password" name="password" />
</div>
<div class="button-group">
	<button type="submit" name="register" value="Register" class="btn btn-register mb-2">Register</button>
	<button type="submit" name="login" value="Login" class="btn btn-login mb-2">Login</button>
</div>
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
</div>
</div>
</div>
</body>
</html>
