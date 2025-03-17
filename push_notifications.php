<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include "connection.php";

$loggedIn = isset($_SESSION['username']);
$username = $loggedIn ? $_SESSION['username'] : "Guest";

// Sample email notification function (You need to configure mail settings)
function sendEmailNotification($email, $subject, $message) {
    $headers = "From: noreply@yourwebsite.com\r\n";
    $headers .= "Reply-To: noreply@yourwebsite.com\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    if (mail($email, $subject, $message, $headers)) {
        return "Notification sent successfully to $email.";
    } else {
        return "Failed to send notification.";
    }
}

// Handling subscription form submission
$notificationMessage = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["subscribe"])) {
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $notificationMessage = "<p style='color: red;'>Invalid email address.</p>";
    } else {
        $subject = "Subscription to Real-Time Flight & Event Notifications";
        $message = "<p>Thank you for subscribing to our notifications! You will receive alerts for flight updates, event notifications, promotions, and reminders.</p>";
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
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f8ff;
        }

        .navbar {
            background-color: #0077b6;
            overflow: hidden;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            display: inline-block;
        }

        .navbar a:hover {
            background-color: #005f87;
            border-radius: 5px;
        }

        .container {
            text-align: center;
            margin: 50px auto;
            max-width: 800px;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        h1 {
            color: #0077b6;
        }

        .notification-box {
            text-align: left;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            background-color: #e3f2fd;
            border-left: 5px solid #0077b6;
        }

        .email-form {
            margin-top: 20px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }

        input[type="email"] {
            width: 70%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: #0077b6;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #005f87;
        }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <div class="navbar">
        <div>
            <a href="homepage.php">Home</a>
            <a href="userAccount.php">User Account</a>
            <a href="searchEvents.php">Search Events</a>
            <a href="indexSearchFlight.php">Search Flights</a>
            <a href="booking_flight.php">Book a Flight</a>
            <a href="confirmation.php">Confirmation</a>
            <a href="recommendation.php">Recommendations</a>
            <a href="push_notifications.php">Notifications</a>
        </div>
    </div>

    <!-- Notifications Section -->
    <div class="container">
        <h1>Notification Center</h1>
        <p>Stay updated with real-time alerts, reminders, and promotions.</p>

        <div class="notification-box">
            <h3>🔔 Alerts</h3>
            <p>Get instant notifications for **flight delays, event cancellations, and severe weather warnings**.</p>
        </div>

        <div class="notification-box">
            <h3>⏳ Reminders</h3>
            <p>Receive reminders for **upcoming flights, scheduled events, and booking confirmations**.</p>
        </div>

        <div class="notification-box">
            <h3>💰 Promotions</h3>
            <p>Get exclusive deals on **discounted flights, event tickets, and special offers**.</p>
        </div>

        <div class="notification-box">
            <h3>📢 Engagements</h3>
            <p>Stay connected with updates, **loyalty program bonuses, and travel suggestions**.</p>
        </div>

        <!-- Email Notification Subscription -->
        <div class="email-form">
            <h3>Subscribe for Email Notifications 📧</h3>
            <form method="post">
                <input type="email" name="email" placeholder="Enter your email" required>
                <button type="submit" name="subscribe">Subscribe</button>
            </form>
            <?php if (!empty($notificationMessage)) echo "<p>$notificationMessage</p>"; ?>
        </div>

    </div>

</body>
</html>
