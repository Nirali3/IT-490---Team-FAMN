<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include "connection.php";
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load .env securely
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$loggedIn = isset($_SESSION['username']);
$username = $loggedIn ? $_SESSION['username'] : "Guest";

function sendEmailNotification($email, $subject, $message) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USERNAME'];
        $mail->Password = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom($_ENV['SMTP_USERNAME'], 'Flight Notification Service');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
        return "✅ Notification sent successfully to <strong>$email</strong>.";
    } catch (Exception $e) {
        return "❌ Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

$notificationMessage = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["subscribe"])) {
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $notificationMessage = "<p style='color: red;'>Invalid email address.</p>";
    } else {
        $subject = "Subscription to Flight & Event Notifications";
        $message = "
            <h2>Thanks for Subscribing!</h2>
            <p>You will now receive:</p>
            <ul>
                <li>🔔 Flight delays, event cancellations, and weather alerts</li>
                <li>⏳ Reminders for flights and events</li>
                <li>💰 Promotions and deals</li>
                <li>📢 Engagement & loyalty updates</li>
            </ul>
        ";
        $notificationMessage = sendEmailNotification($email, $subject, $message);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
	    padding: 30px;
	    margin: 0;
	    padding: 0;
        }

        .navbar {
            background-color: #0077b6;
            overflow: hidden;
	    display: flex;
            justify-content: center;
	    flex-wrap: wrap;
	    align-items: center;
	    padding: 10px 20px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
	    padding: 10px 20px;
	    margin: 5px;
	    display: inline-block;
	    border-radius: 6px;
            transition: background-color 0.3s ease;
            font-weight: bold;
            background-color: #0096c7;
        }

        .navbar a:hover {
	    background-color: #005f87;
            border-radius: 5px;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        h1 {
            color: #0077b6;
        }

        .notification-box {
            background-color: #e3f2fd;
            border-left: 5px solid #0077b6;
            padding: 15px;
            margin: 15px 0;
            border-radius: 6px;
        }

        .email-form {
            background-color: #f9f9f9;
            padding: 20px;
            margin-top: 30px;
            border-radius: 6px;
        }

        input[type="email"] {
            width: 70%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            padding: 10px 15px;
            background-color: #0077b6;
            color: white;
            border: none;
            border-radius: 5px;
            margin-left: 10px;
            cursor: pointer;
        }

        button:hover {
            background-color: #005f87;
        }

        .message {
            margin-top: 20px;
            font-weight: bold;
	}

	@media (max-width: 768px){
		.navbar{
			overflow-x: auto;
			white-space: wrap;
			align-items: center;
			padding: 15px;
		}
		.container{
			margin: 20px auto;
			padding: 20px;
			max-width: 95%;
		}
	}

	@media (max-width: 480px){
		h1, h3 {
			font-size: 20px;
		}
}
    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
  <div>
    <a href="homepage.php">Home</a>
    <a href="userAccount.php">User Account</a>
    <a href="searchEvents.php">Search Events</a>
    <a href="indexSearchFlight.php">Search Flights</a>
    <a href="push_notifications.php">Notification Center</a>
    <a href="recommendation.php">Recommendations</a>
  </div>
</div>

<!-- Main Content -->
<div class="container">
    <h1>Notification Center</h1>
    <p>Stay updated with real-time alerts, reminders, and promotions.</p>

    <div class="notification-box">
        <h3>🔔 Alerts</h3>
        <p>Flight delays, event cancellations, and weather warnings.</p>
    </div>

    <div class="notification-box">
        <h3>⏳ Reminders</h3>
        <p>Upcoming flights, scheduled events, and confirmations.</p>
    </div>

    <div class="notification-box">
        <h3>💰 Promotions</h3>
        <p>Special deals on flights, event tickets, and loyalty offers.</p>
    </div>

    <div class="notification-box">
        <h3>📢 Engagements</h3>
        <p>Loyalty bonuses, tips, and personalized recommendations.</p>
    </div>

    <div class="email-form">
        <h3>📧 Subscribe for Email Notifications</h3>
        <form method="post">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit" name="subscribe">Subscribe</button>
        </form>
        <?php if (!empty($notificationMessage)) echo "<div class='message'>$notificationMessage</div>"; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>

