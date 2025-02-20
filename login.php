<?php
session_start();

con = mysql.connector.connect(
	host = "100.69.138.84";
	user = "backend_user";
	password = "490AFMNprojectFinal";
	database = "project490_db";
)

print("Connection is successful");

if (isset($_POST["username"])){
	$username = $_POST["username"];
}

if (isset($_POST["password"])){
	$password = $_POST["password"];
}
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Login</title>
</head>

<body>
<form method="POST" action="login.php" id="loginform">
<p>
<label>Username:</label>
<input type="text" id="username" name="username"/>
</p>

<p>
<label>Password:</label>
<input type="text" id="password" name="password"/>
</p>

<input type="button" name="register-button" value="Register"/>
<input type="button" name="login-button" value="Login"/>

</form>
</body>
</html>
