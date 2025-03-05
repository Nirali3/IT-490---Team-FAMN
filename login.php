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
			$_SESSION["username"] = $username;
			header("Location: homepage.php");
			exit();
		}else{
			$errors[] = $response['message'];
		}
	}

		/*$stmt = $con->prepare("SELECT password_hash FROM login WHERE username = ?");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$stmt->store_result();

		if($stmt->num_rows > 0) {
			$stmt->bind_result($password_hash);
			$stmt->fetch();
			
			if (password_verify($password, $password_hash)) {
				$_SESSION["username"] = $username;
				header("Location: homepage.php");
				exit();
			} else {
			     $errors[] = "Invalid password!.Please try again.";
			}
		}else{
			$errors[] = "USER DOES NOT EXIST";
		}

		$stmt->close();
}*/

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
</style>
</head>

<body>
<h1> User Login </h1>
<form method="POST" action="login.php" id="loginform">
	<p>
		<label>Username:</label>
		<input type="text" id="username" name="username" />
	</p>

	<p>
		<label>Password:</label>
		<input type="password" id="password" name="password" />
	</p>

	<input type="submit" name="register" value="Register"/>
	<input type="submit" name="login" value="Login"/>

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
