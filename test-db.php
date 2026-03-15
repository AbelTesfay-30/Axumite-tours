<?php
// DELETE THIS FILE after confirming the connection works
$conn = new mysqli('localhost', 'root', 'abeltesfay', 'axumite_tours');

if ($conn->connect_error) {
    die('FAILED: ' . $conn->connect_error);
}

echo 'Connected OK. ';

$result = $conn->query('SELECT COUNT(*) AS total FROM bookings');
$row    = $result->fetch_assoc();
echo 'Bookings in table: ' . $row['total'];

$conn->close();
