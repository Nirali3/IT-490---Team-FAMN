<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

include "connection.php";
require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable('/var/www/html', 'api.env');
$dotenv->load();

$api_key = $_ENV['GOOGLE_API_KEY'];

$recommendedEvents = "";
$location = "New York"; 

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['Location'])) {
    $location = htmlspecialchars($_POST['Location']);
}

$query = urlencode("trending events in " . $location);
$url = "https://serpapi.com/search?engine=google_events&q={$query}&hl=en&gl=us&api_key={$api_key}";

$response = file_get_contents($url);

if ($response === false) {
    die("Failed to fetch recommended events. Please try again.");
} else {
    $data = json_decode($response, true);

    if (!empty($data['events_results'])) {
        foreach ($data['events_results'] as $event) {
            $eventTitle = htmlspecialchars($event['title']);
            $eventDate = !empty($event['date']['when']) ? htmlspecialchars($event['date']['when']) : "TBA";
            $eventLink = htmlspecialchars($event['link']);
            $eventImage = !empty($event['thumbnail']) ? htmlspecialchars($event['thumbnail']) : "images/default-event.jpg";
            $rating = rand(4, 5);
            $stars = str_repeat("⭐", $rating);

            $recommendedEvents .= "
                <div class='event-card'>
                    <img src='$eventImage' alt='Event Image'>
                    <h3>$eventTitle</h3>
                    <p><strong>Date:</strong> $eventDate</p>
                    <p><strong>Rating:</strong> $stars</p>
                    <p><a href='$eventLink' target='_blank' class='more-info'>More Info</a></p>
                </div>
            ";
        }
    } else {
        $recommendedEvents = "<p style='color:red; font-size:18px;'>No trending events found for " . htmlspecialchars($location) . ".</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Top Event Recommendations</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f9;
        color: #333;
        text-align: center;
        margin: 0;
        padding: 0;
    }

    .navbar {
        background-color: #0077b6;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: flex-start;
        padding: 15px 20px;
        gap: 10px;
    }

    .navbar a {
        color: white;
        text-decoration: none;
        padding: 10px 20px;
        margin: 5px;
        background-color: #0096c7;
        border-radius: 6px;
        font-weight: bold;
        transition: background-color 0.3s ease;
        display: inline-block;
        white-space: nowrap;
    }

    .navbar a:hover {
        background-color: #005f87;
    }

    h1 {
        color: #0077b6;
    }

    .search-bar {
        display: flex;
        justify-content: center;
        margin-top: 20px;
        max-width: 600px;
        margin: 20px auto;
    }

    input[type="text"] {
        width: 100%;
        height: 50px;
        max-width: 500px;
        padding: 10px;
        font-size: 18px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    button[type="submit"] {
        background-color: #0077b6;
        color: white;
        padding: 12px 24px;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        margin-left: 10px;
    }

    button[type="submit"]:hover {
        background-color: #005f87;
    }

    .event-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        margin: 20px;
    }

    .event-card {
        background-color: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        margin: 10px;
        width: 300px;
        text-align: center;
    }

    .event-card img {
        width: 100%;
        height: auto;
        border-radius: 10px;
    }

    .more-info {
        text-decoration: none;
        color: #0077b6;
        font-weight: bold;
    }

    .more-info:hover {
        text-decoration: underline;
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
        <a href="push_notifications.php">Notification Center</a>
        <a href="recommendation.php">Recommendations</a>
    </div>
</div>

<h1>🔥 Top Recommended Events for You 🔥</h1>
<p>Discover highly rated events happening near you!</p>

<!-- Location Search -->
<div class="search-bar">
    <form method="POST" action="recommendation.php">
        <input type="text" name="Location" placeholder="Enter a location (e.g., New York)">
        <button type="submit">Find Events</button>
    </form>
</div>

<!-- Recommended Events Section -->
<div class="event-container">
    <?= $recommendedEvents ?>
</div>

<?php include 'footer.php'; ?>

</body>
</html>

