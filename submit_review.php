<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<pre>DEBUG START<br>";
print_r($_POST);
echo "</pre>";

session_start();
include "connection.php"; // Ensure this contains a valid $conn variable

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    echo "You need to log in to submit a review.";
    exit();
}

$username = $_SESSION['username'];
$success_message = "";
$error_message = "";
$review_message = "";
$rating = "";
$booking_id = "";
$passenger_id = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $review_message = trim($_POST['review']);
    $rating = trim($_POST['rating']);
    $booking_id = trim($_POST['booking_id']);
    $passenger_id = trim($_POST['passenger_id']);


if ($loggedIn) {
	$stmt = $con->prepare("INSERT INTO Reviews (comment, booking_id, passenger_id) VALUES(?,?,?)");

    if (!empty($review_message) && !empty($rating) && !empty($booking_id) && !empty($passenger_id)) {
        // Insert review into database
        $stmt = $conn->prepare("INSERT INTO Reviews (username, comment, rating, booking_id, passenger_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $review_message, $rating, $booking_id, $passenger_id);

        if ($stmt->execute()) {
            $success_message = "✅ Your review and rating have been recorded successfully!";
        } else {
            $error_message = "❌ Error submitting review. Please try again.";
        }
        $stmt->close();
    } else {
        $error_message = "❌ Please fill in all fields.";
    }
}

// Fetch all reviews from database
$allReviews = [];
$result = $conn->query("SELECT * FROM Reviews ORDER BY created_at DESC");
while ($row = $result->fetch_assoc()) {
    $allReviews[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submitted Reviews</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; text-align: center; }
        .container { max-width: 800px; margin: 20px auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); }
        h2 { color: #0077b6; }
        .message { font-size: 18px; color: green; }
        .error { font-size: 18px; color: red; }
        .review-list { text-align: left; padding: 10px; }
        .review-card { background: #fff; padding: 15px; margin: 10px 0; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .rating { color: #ffcc00; font-size: 20px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Review Submission</h2>

    <?php if ($success_message): ?>
        <p class="message"><?= $success_message ?></p>
        <div class="review-box">
            <p><strong>Review:</strong> <?= htmlspecialchars($review_message) ?></p>
            <p class="rating"><?= str_repeat("⭐", (int)$rating) ?></p>
            <p><strong>Submitted by:</strong> <?= htmlspecialchars($username) ?></p>
        </div>
    <?php elseif ($error_message): ?>
        <p class="error"><?= $error_message ?></p>
    <?php endif; ?>

    <h3>Your Submitted Reviews</h3>
    <p><strong>Review:</strong> <?php echo htmlspecialchars($review_message); ?></p>
    <p><strong>Rating:</strong> <?php echo str_repeat("⭐", $rating); ?></p>
    <p><strong>Submitted by:</strong> <?php echo htmlspecialchars($username); ?></p>

    <hr>

    <h2>All Reviews</h2>
    <div class="review-list">
        <?php foreach ($allReviews as $review) : ?>
            <div class="review-card">
                <p><strong><?php echo htmlspecialchars($review['username']); ?></strong> wrote:</p>
                <p><?php echo htmlspecialchars($review['comment']); ?></p>
                <p class="rating"><?php echo str_repeat("⭐", $review['rating']); ?></p>
                <p><em>Submitted on: <?php echo htmlspecialchars($review['created_at']); ?></em></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>	
=======
echo "Hello from review page!";
>>>>>>> 63774a88a09b70e0c3bc8db7d0d9192c90d679bc
