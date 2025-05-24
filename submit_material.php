<?php
session_start();
if (!isset($_SESSION['username'])) {
    die(json_encode(['success' => false, 'message' => 'Not logged in']));
}

$conn = new mysqli("localhost", "root", "", "recycle_management_db");
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

$username = $_SESSION['username'];
$material_type = $_POST['material'] ?? '';
$quantity = floatval($_POST['quantity'] ?? 0);

if ($quantity <= 0) {
    die(json_encode(['success' => false, 'message' => 'Invalid quantity']));
}

// Insert into recycling_orders
$stmt = $conn->prepare("INSERT INTO recycling_orders (username, material_type, quantity, status) VALUES (?, ?, ?, 'pending')");
$stmt->bind_param("ssd", $username, $material_type, $quantity);

if ($stmt->execute()) {
    // Also update recycled_materials for total tracking
    $stmt = $conn->prepare("INSERT INTO recycled_materials (username, material_type, quantity) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $username, $material_type, $quantity);
    $stmt->execute();
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to submit material']);
}