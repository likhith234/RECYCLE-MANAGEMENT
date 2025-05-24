<?php
session_start();
if (!isset($_SESSION['admin'])) {
    die(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

$conn = new mysqli("localhost", "root", "", "recycle_management_db");
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

$order_id = $_POST['order_id'] ?? '';
$status = $_POST['status'] ?? '';

$stmt = $conn->prepare("UPDATE recycling_orders SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $order_id);

$result = $stmt->execute();
echo json_encode(['success' => $result]);