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
		$errors[] = "USERNAME CANNOT BE EMPTY. PLEASE INPUT A USERNAME";
	}
	
	if (empty($password)){
		$errors[]="PASSWORD CANNOT BE EMPTY. PLEASE INPUT A PASSWORD";
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
				$errors[] = "Invalid password!.Please try again.";
			}
		} else {
			$errors[] = "USER DOES NOT EXIST! PLEASE CREATE AN ACCOUNT FIRST.";
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
		$errors[] = "INVALID USERNAME. PLEASE ENTER AGAIN.";
	}
	else{
		echo "USERNAME VALID!";
	}
}

function validatePassword($password, $passwordRegex){
	if (!preg_match($passwordRegex, $password)){
		$errors[] = "INVALID PASSWORD. PASSWORD MUST HAVE A SPECIAL CHARACTER, NUMBER AND SHOULD BE 7 CHARACTERS LONG. PLEASE RE-ENTER";
	}
	else {
		echo "PASSWORD VALID!";
	}
}

if (empty($errors)){
	$hashed_password = password_hash($password);

	$stmt = $con->prepare("INSERT INTO login (username, password_hash) VALUES (?, ?)");
	$stmt->bind_param("ss", $username, $hashed_password);
	$stmt->execute();
	$stmt->close();

	header("Location: homepage.php");
	exit();
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
