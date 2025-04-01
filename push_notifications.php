<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include "connection.php";

$loggedIn = isset($_SESSION['username']);
$username = $loggedIn ? $_SESSION['username'] : "Guest";

function sendEmailNotification($email, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'your_gmail@gmail.com'; // Your Gmail
        $mail->Password   = 'your_app_password';    // Your App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('your_gmail@gmail.com', 'Flight Alerts');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return "<p style='color: green;'>✅ Email sent to $email</p>";
    } catch (Exception $e) {
        return "<p style='color: red;'>❌ Mailer Error: {$mail->ErrorInfo}</p>";
    }
}

$notificationMessage = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["subscribe"])) {
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $notificationMessage = "<p style='color: red;'>Invalid email address.</p>";
    } else {
        $subject = "Subscription to Flight & Event Notifications";
        $message = "<h3>Welcome!</h3><p>You will now receive alerts for:</p><ul>
                    <li>🔔 Flight delays, cancellations, and severe weather</li>
                    <li>⏰ Event reminders and bookings</li>
                    <li>💰 Promotions on flights & events</li>
                    <li>📢 Engagement and loyalty rewards</li>
                    </ul><p>Thank you for subscribing!</p>";
        $notificationMessage = sendEmailNotification($email, $subject, $message);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Push Notifications</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f0f8ff; margin: 0; padding: 0; }
        .navbar { background: #0077b6; padding: 15px; display: flex; justify-content: space-between; }
        .navbar a { color: white; padding: 10px; text-decoration: none; }
        .container { max-width: 800px; margin: 40px auto; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { color: #0077b6; }
        .notification-box { margin: 15px 0; padding: 15px; background: #e3f2fd; border-left: 5px solid #0077b6; }
        .email-form input { padding: 10px; width: 60%; border-radius: 5px; border: 1px solid #ccc; }
        .email-form button { padding: 10px 20px; background: #0077b6; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .email-form button:hover { background-color: #005f87; }
    </style>
</head>
<body>
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

    <div class="container">
        <h1>Notification Center</h1>
        <p>Stay updated with real-time alerts, reminders, and offers!</p>

        <div class="notification-box">
            <h3>🔔 Alerts</h3>
            <p>Flight delays, cancellations, and severe weather updates.</p>
        </div>
        <div class="notification-box">
            <h3>⏳ Reminders</h3>
            <p>Upcoming flights, booked events, and check-in reminders.</p>
        </div>
        <div class="notification-box">
            <h3>💰 Promotions</h3>
            <p>Discounted flight and event deals.</p>
        </div>
        <div class="notification-box">
            <h3>📢 Engagements</h3>
            <p>Loyalty program bonuses and travel tips.</p>
        </div>

        <div class="email-form">
            <h3>📧 Subscribe for Email Alerts</h3>
            <form method="post">
                <input type="email" name="email" placeholder="Enter your email" required>
                <button type="submit" name="subscribe">Subscribe</button>
            </form>
            <?php echo $notificationMessage; ?>
        </div>
    </div>
</body>
</html>
