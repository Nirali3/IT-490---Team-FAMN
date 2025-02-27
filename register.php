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
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
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
} elseif (!preg_match('/[\'^$%&*()}{@#~><>,|=_+\-]/', $password)) {
    $errors[] = "Password must have at least one special character.";
}

if (empty($errors)) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $con->prepare("INSERT INTO register (first_name, last_name, email, username, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $first_name, $last_name, $email, $username, $hashed_password);
    $stmt->execute();
    $stmt->close();
}

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Registration</title>
    </head>
    <body>
        <main>
            <form method="POST" action="register.php">
		<h1>Registration</h1>
		<label>First Name:</label>
                <input type="text" name="first_name" value="">
                <br>
                <label>Last Name:</label>
                <input type="text" name="last_name" value="">
                <br>
                <label>Email:</label>
                <input type="text" name="email" value="">
                <br>
                <label>Username:</label>
                <input type="text" name="username" value="">
                <br>
                <label>Password:</label>
                <input type="text" name="password" value="">
                <br>
                <input type="submit" value="Submit">
            </form>
            <div id="message">
                <h3>Password Criteria:</h3>
                <p>At least one upper case letter</p>
                <p>At least one number</p>
                <p>At least one special character</p>
            </div>
        </main>
    </body>
</html>
