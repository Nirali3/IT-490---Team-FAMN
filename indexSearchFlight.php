<?php

// Cargar la librería dotenv
require_once 'vendor/autoload.php';

// Cargar el archivo .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Obtener la API Key desde el .env
$apiKey = getenv('API_KEY');

if (!$apiKey) {
    die('<p class="error">API Key not found. Please check your .env file.</p>');
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['origin'], $_GET['destination'], $_GET['departureDate'], $_GET['returnDate'])) {
    
    $origin = urlencode($_GET['origin']);
    $destination = urlencode($_GET['destination']);
    $departureDate = urlencode($_GET['departureDate']);
    $returnDate = urlencode($_GET['returnDate']);

    // Google Flights API URL (SerpApi)
    $apiUrl = "https://serpapi.com/search.json?engine=google_flights&departure_id=$origin&arrival_id=$destination&gl=us&hl=en&currency=USD&outbound_date=$departureDate&return_date=$returnDate&api_key=$apiKey";

    // Fetch API data
    $response = file_get_contents($apiUrl);

    // Handle API response error
    if ($response === FALSE) {
        die('<p class="error">Error retrieving data from the API.</p>');
    }

    // Decode JSON response
    $data = json_decode($response, true);

    // Check if flight data was received
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f9;
            color: #333;
            text-align: center;
            margin: 0;
            padding: 0;
        }

        h2 {
            color: #007bff;
            margin-top: 20px;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
        }

        input, button {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        button {
            background: #007bff;
            color: white;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            transition: 0.3s;
        }

        button:hover {
            background: #0056b3;
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

    <h2>Find Your Flight</h2>

    <div class="container">
        <form id="flightForm" method="GET">
            <label for="origin">Origin IATA Code:</label>
            <input type="text" id="origin" name="origin" placeholder="Example: CDG" required>
            
            <label for="destination">Destination IATA Code:</label>
            <input type="text" id="destination" name="destination" placeholder="Example: AUS" required>
            
            <label for="departureDate">Departure Date:</label>
            <input type="date" id="departureDate" name="departureDate" required>

            <label for="returnDate">Return Date:</label>
            <input type="date" id="returnDate" name="returnDate" required>
            
            <button type="submit">Search Flights</button>
        </form>
    </div>

    <div class="flights-container">
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
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No flights found for the given parameters.</p>
        <?php endif; ?>
    </div>

</body>
</html>
