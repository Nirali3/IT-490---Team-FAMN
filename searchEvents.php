<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

session_start();

include "connection.php";
require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable('/var/www/html', 'api.env');
$dotenv->load();

$api_key = $_ENV['GOOGLE_API_KEY'];

$EventResults = "";

if($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['Location'])){
	$location = isset($_POST['Location']) ? $_POST['Location'] : "";
	$query = urlencode("events in " . $location);
	$url = "https://serpapi.com/search?engine=google_events&q={$query}&hl=en&gl=us&api_key={$api_key}";
	
	$response = file_get_contents($url);

	if($response === false){
		die("Failed to fetch events. Please try again.");
	}
	else{
		$data = json_decode($response, true); 
		
		if (!empty($data['events_results'])) {
			foreach ($data['events_results'] as $event) {
				$EventResults .= "<h3>" . htmlspecialchars($event['title']) . "</h3>";
				if(!empty($event['date']['when'])){
					$EventResults .= "<p><strong>Date:</strong> " . htmlspecialchars($event['date']['when']) . "</p>";
				}
				if (!empty($event['address'])) {
					$address = is_array($event['address']) ? implode(", ", $event['address']) : $event['address'];
					$EventResults .= "<p><strong>Address:</strong> " . htmlspecialchars($address) . "</p>";
				}

				if(!empty($event['thumbnail'])){
					$EventResults .= "<img src='" . htmlspecialchars($event['thumbnail']) . "' alt='Event image' class='event-img' style='max-width:300px; display:block; margin:10px 15px;'/>";
				}
		
				$EventResults .= "<p><a href='" . htmlspecialchars($event['link']) . "' target='_blank'>More Info</a></p>";
				$EventResults .= "<hr>";
			}
		}
	
	       	else {
			$EventResults = "<p style='color:red; font-size:18px;'>No events found for " . htmlspecialchars($location) . ".</p>";
		}
	}
}

?>

<!DOCTYPE HTML>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Event Search</title>
<style>

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
		font-size: 16px;
		display: inline-block;
		width: auto;
	}

	button[type="submit"]:hover{
		background-color: #005f87;
	}

	input[type="text"]{
		width: 100%;
		height: 50px;
		max-width: 600px;
		display: block;
		padding: 10 15px;
		margin: 20px auto;
		font-size: 18px;
	}

	h1, h4{
		text-align: center;		
		color: #0077b6;
	}

	#searchform{
		width: 100%;
	}

	#event_results{
		width: 100%;
		max-width: 800px;
		margin: 30px auto;
		padding: 20px;
		font-size: 18px;
		font-family: Arial, sans-serif;
		background-color: #ffffff;
		border-radius: 10px;
		box-shadow: 0 4px 8px rgba(0,0,0,0.1);
		}
	
	#event_results a:hover{
		text-decoration: underline;
		color: #0077b6;
	}

	.event-img{
		width: 300px;
	}

	.search-bar{
		width: 100%;
		display: flex;
		justify-content: center;
		text-align: center;
		margin-top: 20px;
		max-width: 600px;
		margin: 20px auto;
		border-radius: 10px;
	}

	.navbar {
		background-color: #0077b6; /* Dark Blue */
        	overflow: hidden;
        	display: flex;
        	justify-content: space-between;
        	align-items: center;
		padding: 10px 20px;
		flex-wrap: wrap;
	}
	.navbar a {
		color: white;
        	text-decoration: none;
		padding: 10px 20px;
		margin: 5px;
		display: inline-block;
		background-color: #0096c7;
		border-radius: 6px;
		transition: background-color 0.3s ease;
		font-weight: bold;
	}

	.navbar a:hover {
		background-color: #005f87;
        	border-radius: 5px;
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

<h1> Search Google Events </h1>
<h4> Search Events By Location </h4>

<div class="search-bar">
	<form method="POST" action="searchEvents.php" id="searchform">
		<input type="text" id="locationSearch" name="Location" placeholder="Enter any location. eg- events in New York"/>
		<button type="submit" name="search" value="Search Events">Search Events</button>
	</form>
</div>

<div id="event_results">
	<?php echo $EventResults; ?>
</div>

<?php include 'footer.php'; ?>

</body>
</html>
