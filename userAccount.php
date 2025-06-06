<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include "connection.php";

$loggedIn = isset($_SESSION['user_id']);
$username = "Guest";
$user_id = $_SESSION['user_id'] ?? null;

if ($loggedIn && $user_id) {
    // Get username from Users table
    $stmt = $con->prepare("SELECT username FROM register WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $username = $row['username'];
    }
    $stmt->close();
}

$purchasedFlights = [];
$purchasedPassengers = [];

if ($loggedIn && $user_id) {
    // Get all bookings by this user
    $stmt = $con->prepare("SELECT * FROM Bookings WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $purchasedFlights[] = $row;
        $booking_id = $row['booking_id'];

        $stmt_inner = $con->prepare("SELECT * FROM Passengers WHERE booking_id = ?");
        $stmt_inner->bind_param("i", $booking_id);
        $stmt_inner->execute();
        $res_inner = $stmt_inner->get_result();
        while ($passenger = $res_inner->fetch_assoc()) {
            $purchasedPassengers[$booking_id][] = $passenger;
        }
        $stmt_inner->close();
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<title>Top Event Recommendations</title>
    <title>User Account - My Bookings</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f8ff;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .navbar {
            background-color: #0077b6;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            padding: 15px 20px;
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
            background-color: #0096c7;
            border-radius: 8px;
            transition: background 0.3s ease;
            white-space: nowrap;
        }

        .navbar a:hover {
            background-color: #005f87;
        }

        .logout-btn {
            background-color: #ff4d4d;
            border: none;
            padding: 10px 15px;
            color: white;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
        }

        .logout-btn:hover {
            background-color: #cc0000;
        }

        .container {
            max-width: 900px;
            margin: 30px auto;
            padding: 25px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        h2, h3 {
            color: #0077b6;
            margin-bottom: 15px;
        }

        .booking-card {
            background-color: #eaf6ff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .passenger-box {
            background-color: #ffffff;
            padding: 15px;
            margin-top: 15px;
            border-left: 5px solid #0077b6;
            border-radius: 8px;
        }

        textarea {
            width: 100%;
            padding: 10px;
            font-size: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
            resize: vertical;
        }

        .submit-review {
            background-color: #0077b6;
            color: white;
            border: none;
            padding: 10px 20px;
            margin-top: 10px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }

        .submit-review:hover {
            background-color: #005f87;
	}
@media (max-width:768px){
	.navbar{
		align-items: center;
		overflow-x: auto;
		white-space: wrap;
		padding: 15px;
	}
	.logout-btn {
        	width: 90%;
        	text-align: center;
        	margin-bottom: 8px;
    	}

    	.container {
        	padding: 15px;
        	margin: 15px 10px;
    	}

    	.booking-card, .passenger-box {
        	padding: 15px;
    	}

    	.submit-review {
        	width: 100%;
    	}
}
    </style>
</head>
<body>

<div class="navbar">
    <a href="homepage.php">Home</a>
    <a href="userAccount.php">User Account</a>
    <a href="searchEvents.php">Search Events</a>
    <a href="indexSearchFlight.php">Search Flights</a>
    <a href="push_notifications.php">Notifications</a>
    <a href="recommendation.php">Recommendations</a>
    <?php if ($loggedIn): ?>
        <form action="logout.php" method="post" style="margin: 0;">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    <?php endif; ?>
</div>

<div class="container">
    <h2>Welcome, <?= htmlspecialchars($username); ?> 👋</h2>
    <p>Here are your past bookings and passenger details:</p>

    <?php if (!empty($purchasedFlights)): ?>
        <?php foreach ($purchasedFlights as $flight): ?>
            <div class="booking-card">
                <h3>Flight Booking #<?= $flight['booking_id'] ?></h3>
                <p><strong>Airline:</strong> <?= htmlspecialchars($flight['airline']) ?></p>
                <p><strong>From:</strong> <?= htmlspecialchars($flight['departureAirport']) ?> → <strong>To:</strong> <?= htmlspecialchars($flight['destinationAirport']) ?></p>
                <p><strong>Departure:</strong> <?= htmlspecialchars($flight['departureDate']) ?></p>
                <p><strong>Arrival:</strong> <?= htmlspecialchars($flight['arrivalDate']) ?></p>
                <p><strong>Total Price:</strong> $<?= htmlspecialchars($flight['price']) ?></p>

                <?php if (!empty($purchasedPassengers[$flight['booking_id']])): ?>
                    <h4>Passengers:</h4>
                    <?php foreach ($purchasedPassengers[$flight['booking_id']] as $passenger): ?>
                        <div class="passenger-box">
                            <p><strong>Name:</strong> <?= htmlspecialchars($passenger['first_name'] . ' ' . $passenger['last_name']) ?></p>
                            <p><strong>DOB:</strong> <?= htmlspecialchars($passenger['dob']) ?></p>
                            <p><strong>Cabin:</strong> <?= htmlspecialchars($passenger['cabin_class']) ?></p>
                            <p><strong>Age Group:</strong> <?= htmlspecialchars($passenger['age_group']) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No bookings found.</p>
    <?php endif; ?>

    <h3>Write a Review</h3>
    <form class="review-form" action="submit_review.php" method="post">
        <textarea name="review" placeholder="Share your flight experience..." required></textarea>
        <br>

        <!-- Booking Dropdown -->
    <label>Booking:</label>
    <select name="booking_id" required>
        <?php foreach ($purchasedFlights as $flight): ?>
            <option value="<?= $flight['booking_id'] ?>">
                Flight #<?= $flight['booking_id'] ?> (<?= htmlspecialchars($flight['airline']) ?>: <?= htmlspecialchars($flight['departureAirport']) ?> → <?= htmlspecialchars($flight['destinationAirport']) ?>)
            </option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <!-- Passenger Dropdown -->
    <label>Passenger:</label>
    <select name="passenger_id" required>
        <?php foreach ($purchasedPassengers as $bookingPassengers): ?>
            <?php foreach ($bookingPassengers as $passenger): ?>
                <option value="<?= $passenger['passenger_id'] ?>">
                    <?= htmlspecialchars($passenger['first_name'] . ' ' . $passenger['last_name']) ?>
                </option>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </select>
    <br><br>
        
        <label>Rate your experience:</label>
        <select name="rating" required>
            <option value="5">⭐⭐⭐⭐⭐</option>
            <option value="4">⭐⭐⭐⭐</option>
            <option value="3">⭐⭐⭐</option>
            <option value="2">⭐⭐</option>
            <option value="1">⭐</option>
        </select>
        <br>
        <button type="submit" class="submit-review">Submit Review</button>
    </form>
</div>

<?php include 'footer.php'; ?>

</body>
</html>
