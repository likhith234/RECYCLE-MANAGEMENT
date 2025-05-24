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

// Get total recycled materials for the user
$total_query = $conn->prepare("SELECT SUM(quantity) as total FROM recycled_materials WHERE username = ?");
$total_query->bind_param("s", $username);
$total_query->execute();
$total_result = $total_query->get_result()->fetch_assoc();

// Get recent records
$records_query = $conn->prepare("SELECT material_type, quantity, status, created_at as date FROM recycling_orders WHERE username = ? ORDER BY created_at DESC");
$records_query->bind_param("s", $username);
$records_query->execute();
$result = $records_query->get_result();

$records = [];
while ($row = $result->fetch_assoc()) {
    $records[] = $row;
}

echo json_encode([
    'total' => $total_result['total'] ?? 0,
    'records' => $records
]);