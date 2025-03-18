<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
 
session_start();

// Connection to Database
include "connection.php";

// Initialize variables 
if (!isset($first_name)) {$first_name = ''; }
if (!isset($last_name)) {$last_name = ''; }
if (!isset($email)) {$email = ''; }
if (!isset($username)) {$username = ''; }
if (!isset($password)) {$password = ''; }
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
}

// Error Message: First Name
if (empty($first_name)) {
    $errors[] = "Please input your first name.";
}

// Error Message: Last Name
if (empty($last_name)) {
    $errors[] = "Please input your last name.";
}

// Error Message: Email
if (empty($email)) {
    $errors[] = "Please input your email.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Please input a valid email.";
}

// Error Message: Username
if (empty($username)) {
    $errors[] = "Please input a username.";
} else {
    $emailStmt = $con->prepare("SELECT username FROM register WHERE username = ?");
    $emailStmt->bind_param("s", $username);
    $emailStmt->execute();
    $emailStmt->store_result();

    if ($emailStmt->num_rows > 0) {
        $errors[] = "Username already exists.";
    }
    $emailStmt->close();
}

// Error Message: Password
if (empty($password)) {
    $errors[] = "Please input a password.";
} elseif (strlen($password) < 7){
    $errors[] = "Password should be at least 7 characters long.";
} elseif (!preg_match('/[A-Z]/', $password)) {
    $errors[] = "Password must have at least one upper case letter.";
} elseif (!preg_match('/[0-9]/', $password)) {
    $errors[] = "Password must contin at least one number";
} elseif (!preg_match('/[\W]/', $password)) {
    $errors[] = "Password must have at least one special character.";
}

$stmt = $con->prepare("SELECT email FROM register WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
	$errors[] = "This email is already registered. Please use another email.";
}
$stmt->close();

if (empty($errors)) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $con->prepare("INSERT INTO register (firstName, lastName, email, username, password_hash) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $first_name, $last_name, $email, $username, $hashed_password);
    $stmt->execute();
    $stmt->close();

    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Registration</title>
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
		width: 10%;
		height: 30px;
		padding: 10 0px;
		margin: 20px auto;
		font-size: 18px;
	}

	input[type="password"]{
		width: 10%;
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
        <main>
            <form method="POST" action="register.php">
		<h1>Registration</h1>
		<label>First Name:</label>
                <input type="text" name="first_name" value="<?= htmlspecialchars($first_name) ?>">
                <br>
                <label>Last Name:</label>
                <input type="text" name="last_name" value="<?= htmlspecialchars($last_name) ?>">
                <br>
                <label>Email:</label>
                <input type="text" name="email" value="<?= htmlspecialchars($email) ?>">
                <br>
                <label>Username:</label>
                <input type="text" name="username" value="<?= htmlspecialchars($username) ?>">
                <br>
                <label>Password:</label>
                <input type="password" name="password" value="">
                <br>
                <button type="submit" value="Register">Register</button>
            </form>

	    <!-- Display Error Messages After Form Submission -->
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

            <div id="message">
                <h3>Password Criteria:</h3>
                <p>At least one upper case letter</p>
                <p>At least one number</p>
                <p>At least one special character</p>
            </div>
        </main>
    </body>
</html>
