<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include "connection.php";

// Get session username (fallback to Guest)
$username = $_SESSION['username'] ?? "Guest";

// Get POST data safely
$review = trim($_POST['review'] ?? '');
$rating = intval($_POST['rating'] ?? 0);
$booking_id = intval($_POST['booking_id'] ?? 0);
$passenger_id = intval($_POST['passenger_id'] ?? 0);
$username = $_SESSION['username'] ?? "Guest";
$review = $_POST['review'] ?? '';
$rating = $_POST['rating'] ?? '';
$booking_id = $_POST['booking_id'] ?? '';
$passenger_id = $_POST['passenger_id'] ?? '';

$inserted = false;
$error_message = "";

// Debugging (optional)
// echo "<pre>";
// print_r($_POST);
// echo "</pre>";

// Validate
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (empty($review) || !$rating || !$booking_id || !$passenger_id) {
        $error_message = "❌ Missing required fields.";
    } else {
        $stmt = $con->prepare("INSERT INTO Reviews (passenger_id, booking_id, rating, comment, created_at) VALUES (?, ?, ?, ?, NOW())");
        if ($stmt === false) {
            $error_message = "❌ SQL Error: " . $con->error;
        } else {
            $stmt->bind_param("iiis", $passenger_id, $booking_id, $rating, $review);
            if ($stmt->execute()) {
                $inserted = true;
            } else {
                $error_message = "❌ Failed to submit review. Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }

// Insert the review into the database if form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST" && $review && $rating && $booking_id && $passenger_id) {
    // Use $conn or $con depending on your connection file
    $stmt = $con->prepare("INSERT INTO Reviews (username, comment, rating, booking_id, passenger_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiii", $username, $review, $rating, $booking_id, $passenger_id);

    if ($stmt->execute()) {
        $inserted = true;
    } else {
        $error_message = "❌ Failed to submit review. Please try again later.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Review Submitted</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            padding: 30px;
            text-align: center;
        }

        h2 {
            color: #0077b6;
        }

        .message {
            color: green;
            font-size: 18px;
            margin-bottom: 20px;
        }

        table {
            margin: 0 auto;
            border-collapse: collapse;
            width: 60%;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px 16px;
        }

        th {
            background-color: #0077b6;
            color: white;
            text-align: left;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .back-btn {
            display: inline-block;
            margin-top: 25px;
            padding: 10px 20px;
            background-color: #0077b6;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .back-btn:hover {
            background-color: #005f87;
        }
    </style>
</head>
<body>

    <h2><?= $inserted ? "✅ Review Submitted Successfully" : "❌ Review Submission Failed" ?></h2>

    <?php if ($inserted): ?>
        <div class="message" style="color:green;">Thank you for your feedback, <strong><?= htmlspecialchars($username); ?></strong>!</div>
        <table>
            <tr><th>Username</th><td><?= htmlspecialchars($username) ?></td></tr>
            <tr><th>Review</th><td><?= htmlspecialchars($review) ?></td></tr>
            <tr><th>Rating</th><td><?= str_repeat('⭐', $rating) ?></td></tr>
            <tr><th>Booking ID</th><td><?= htmlspecialchars($booking_id) ?></td></tr>
            <tr><th>Passenger ID</th><td><?= htmlspecialchars($passenger_id) ?></td></tr>
        </table>
    <?php else: ?>
        <div class="message" style="color:red;"><?= $error_message ?></div>
    <h2>✅ Review Submitted Successfully</h2>

    <?php if ($review && $rating): ?>
        <div class="message">Thank you for your feedback, <strong><?php echo htmlspecialchars($username); ?></strong>!</div>

        <table>
            <tr>
                <th>Username</th>
                <td><?php echo htmlspecialchars($username); ?></td>
            </tr>
            <tr>
                <th>Review</th>
                <td><?php echo htmlspecialchars($review); ?></td>
            </tr>
            <tr>
                <th>Rating</th>
                <td><?php echo str_repeat('⭐', (int)$rating); ?></td>
            </tr>
            <tr>
                <th>Booking ID</th>
                <td><?php echo htmlspecialchars($booking_id ?: 'N/A'); ?></td>
            </tr>
            <tr>
                <th>Passenger ID</th>
                <td><?php echo htmlspecialchars($passenger_id ?: 'N/A'); ?></td>
            </tr>
        </table>
    <?php else: ?>
        <div class="message" style="color:red;">❌ No review data submitted.</div>
    <?php endif; ?>

    <a class="back-btn" href="userAccount.php">⬅ Back to Account</a>

<?php include 'footer.php'; ?>

</body>
</html>

