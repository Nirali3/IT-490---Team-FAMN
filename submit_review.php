<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$username = $_SESSION['username'] ?? "Guest";
$review = $_POST['review'] ?? '';
$rating = $_POST['rating'] ?? '';
$booking_id = $_POST['booking_id'] ?? '';
$passenger_id = $_POST['passenger_id'] ?? '';
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

</body>
</html>

