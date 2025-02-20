<?php
session_start();

include "connection.php";

if (isset($_POST['login'])){
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);


	$username = con->real_escape_string($username);
	$password = con->real_escape_string($password);

	$usenamesql = "Select * FROM Login where username = '$username'";
	$result = con->query($sql);

	$passwordsql = "SELECT * FROM Login where password = '$password'";
	$result = con->query($sql);
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

