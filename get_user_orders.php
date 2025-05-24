<?php
session_start();
if (!isset($_SESSION['username'])) {
    die(json_encode(['error' => 'Not logged in']));
}

$conn = new mysqli("localhost", "root", "", "recycle_management_db");
if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed']));
}

$username = $_SESSION['username'];
$query = "SELECT * FROM recycling_orders WHERE username = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

echo json_encode($orders);