
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include "connection.php";
require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable('/var/www/html', 'api.env');
$dotenv->load();

$apiKey = $_ENV['GOOGLE_API_KEY'];  
if (!$apiKey) {
    die('<p class="error">API Key not found. Please check your .env file.</p>');
}

$airportsData = json_decode(file_get_contents('airports.json'), true);

function getIATA($location, $airportsData) {
    $location = strtolower(trim($location));

    foreach ($airportsData as $airport) {
        if (strpos(strtolower($airport['city']), $location) !== false || strpos(strtolower($airport['country']), $location) !== false) {
            return $airport['iata']; 
        }
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['origin'], $_GET['destination'], $_GET['departureDate'], $_GET['returnDate'])) {
    $originInput = $_GET['origin'];
    $destinationInput = $_GET['destination'];

    $originIATA = getIATA($originInput, $airportsData);
    $destinationIATA = getIATA($destinationInput, $airportsData);

    if (!$originIATA || !$destinationIATA) {
        die('<p class="error">No flight has been found to this airport in that city/country.</p>');
    }

    $departureDate = urlencode($_GET['departureDate']);
    $returnDate = urlencode($_GET['returnDate']);

    $apiUrl = "https://serpapi.com/search.json?engine=google_flights&departure_id=$originIATA&arrival_id=$destinationIATA&gl=us&hl=en&currency=USD&outbound_date=$departureDate&return_date=$returnDate&api_key=$apiKey";

    $response = file_get_contents($apiUrl);

    if ($response === FALSE) {
        die('<p class="error">Error retrieving data from the API.</p>');
    }

    $data = json_decode($response, true);
    $flights = $data['best_flights'] ?? [];
} else {
    $flights = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flight Search</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f8ff; /* Light blue background */
        }

        .navbar {
            background-color: #0077b6; /* Dark Blue */
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

        .logout-btn {
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
        }

        .logout-btn:hover {
            background-color: #cc0000;
        }

        .container {
            text-align: center;
            margin-top: 50px;
        }

        h1 {
            color: #0077b6;
        }
h2 {
    color: #007bff;
    margin-top: 30px;
    font-size: 28px;
    font-weight: 700;
}

.container {
    max-width: 600px;
    margin: 20px auto;
    padding: 30px;
    background: white;
    box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.1);
    border-radius: 15px;
    transition: box-shadow 0.3s ease-in-out;
}

.container:hover {
    box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.15);
}

form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

label {
    font-weight: 600;
    color: #0077b6;
    margin-bottom: 5px;
}

input, button {
    padding: 12px;
    border-radius: 10px;
    border: 1px solid #ccc;
    font-size: 16px;
    transition: border-color 0.3s, background-color 0.3s;
}

input:focus, button:focus {
    outline: none;
    border-color: #007bff;
    background-color: #eaf6ff;
}

button {
    background-color: #007bff;
    color: white;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.2s ease;
}

button:hover {
    background-color: #0056b3;
    transform: scale(1.05);
}

button:active {
    background-color: #004080;
}

.flights-container {
    margin-top: 40px;
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.flight-card {
    background: #fff;
    padding: 20px;
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
    border-radius: 15px;
    text-align: left;
    max-width: 800px;
    margin: auto;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.flight-card:hover {
    transform: scale(1.02);
    box-shadow: 0px 6px 20px rgba(0, 0, 0, 0.1);
}

.flight-card img {
    width: 80px;
    height: auto;
    border-radius: 50%;
    margin-right: 15px;
    transition: transform 0.3s ease;
}

.flight-card img:hover {
    transform: scale(1.1);
}

.flight-card div {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.flight-card strong {
    font-weight: 700;
}

.booking-button {
    background-color: #ff4d4d;
    color: white;
    padding: 10px 20px;
    font-weight: bold;
    border-radius: 10px;
    border: none;
    transition: background-color 0.3s, transform 0.2s ease;
}

.booking-button:hover {
    background-color: #cc0000;
    transform: scale(1.05);
}

.booking-button:active {
    background-color: #990000;
}

        p {
            font-size: 18px;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.5;
        }

        .image-section {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }

        .image-section img {
            width: 40%;
            height: auto;
            border-radius: 10px;
            margin: 10px;
        }

        .flights-container {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .flight-card {
            background: white;
            padding: 15px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            text-align: left;
            max-width: 600px;
            margin: auto;
        }

        .flight-card img {
            width: 80px;
        }

        .error {
            color: red;
            font-weight: bold;
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
 
         <!-- Logout Button -->
         <?php if (isset($_SESSION['username'])): ?>
             <form action="logout.php" method="post" style="margin: 0;">
                 <button type="submit" class="logout-btn">Logout</button>
             </form>
         <?php endif; ?>
     </div>
 
 <div class="container">
         <h1>Search Flights</h1>
         <form action="" method="GET">
             <label for="origin">Origin (City or Country)</label>
             <input type="text" id="origin" name="origin" required>
 
             <label for="destination">Destination (City or Country)</label>
             <input type="text" id="destination" name="destination" required>
 
             <label for="departureDate">Departure Date</label>
             <input type="date" id="departureDate" name="departureDate" required>
 
             <label for="returnDate">Return Date</label>
             <input type="date" id="returnDate" name="returnDate" required>
 
             <button type="submit">Search Flights</button>
         </form>
 
     <!-- Flights Section -->
     <div class="flights-container" id="flights-section">
         <?php if (!empty($flights)): ?>
             <h3>Available Flights:</h3>
             <?php foreach ($flights as $flight): ?>
                 <div class="flight-card">
                     <strong>Airline:</strong> <?php echo $flight['flights'][0]['airline']; ?><br>
                     <img src="<?php echo $flight['flights'][0]['airline_logo']; ?>" alt="Airline Logo"><br>
                     <strong>Price:</strong> $<?php echo $flight['price']; ?><br>
                     <strong>Total Duration:</strong> <?php echo $flight['total_duration']; ?> minutes<br>
                     <strong>Departure:</strong> <?php echo $flight['flights'][0]['departure_airport']['name']; ?> (<?php echo $flight['flights'][0]['departure_airport']['id']; ?>)<br>
                     <strong>Destination:</strong> 
                     <?php 
                     if (!empty($flight['flights'])) {
                         $lastFlight = end($flight['flights']); 
                         echo $lastFlight['arrival_airport']['name'] . " (" . $lastFlight['arrival_airport']['id'] . ")<br>";
                     } else {
                         echo "Not available<br>";
                     }
                     ?>
                     <strong>Departure Date & Time:</strong> <?php echo $flight['flights'][0]['departure_airport']['time']; ?><br>
                     <strong>Arrival Date & Time:</strong> 
                     <?php 
                     if (!empty($flight['flights'])) {
                         echo $lastFlight['arrival_airport']['time'] . "<br>";
                     } else {
                         echo "Not available<br>";
                     }
                     ?>
 
                     <!-- Book Now Button -->
                     <form action="booking_flight.php" method="GET">
                         <input type="hidden" name="airline" value="<?php echo $flight['flights'][0]['airline']; ?>">
                         <input type="hidden" name="price" value="<?php echo $flight['price']; ?>">
                         <input type="hidden" name="departureAirport" value="<?php echo $flight['flights'][0]['departure_airport']['name']; ?>">
                         <input type="hidden" name="destinationAirport" value="<?php echo $lastFlight['arrival_airport']['name']; ?>">
                         <input type="hidden" name="arrivalAirport" value="<?php echo $lastFlight['arrival_airport']['name']; ?>">
                         <input type="hidden" name="departureDate" value="<?php echo $flight['flights'][0]['departure_airport']['time']; ?>">
                         <input type="hidden" name="arrivalDate" value="<?php echo $lastFlight['arrival_airport']['time']; ?>">
                         <button type="submit" class="booking-button">Book Now</button>
                     </form>
                 </div>
             <?php endforeach; ?>
         <?php else: ?>
             <p>No flights found for the given parameters.</p>
         <?php endif; ?>
     </div>
 
 </body>
 </html>
