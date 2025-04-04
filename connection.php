<?php
$host = "100.69.138.84";
$username = "backend_user";
$password = "490AFMNprojectFinal";
$database = "project490_db";

$con = new mysqli($host, $username, $password, $database);

if ($con->connect_error){
    die("Failed to connect: " . $con->connect_error);
}

// Confirm current database
$dbName = $con->query("SELECT DATABASE()")->fetch_row()[0];
echo "Current Database: $dbName<br>";

// List all tables in DB
$allTables = $con->query("SHOW TABLES");
echo "<strong>Available Tables:</strong><br>";
while ($row = $allTables->fetch_row()) {
    echo $row[0] . "<br>";
}

// Specific table existence check (CASE-SENSITIVE)
$result = $con->query("SHOW TABLES LIKE 'Bookings'");
if ($result->num_rows > 0) {
    echo "✅ Bookings table exists!";
} else {
    echo "❌ Bookings table does not exist.";
}
?>

