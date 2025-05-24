<?php
session_start();
if (!isset($_SESSION['admin'])) {
    echo "Access Denied!";
    exit();
}

$username = $_GET['username'] ?? "";

$conn = new mysqli("localhost", "root", "", "recycle_management_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT material_type, quantity, recycled_at FROM recycled_materials WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Recycled Materials</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <h2 class="text-2xl font-bold mb-4 text-center">Recycled Materials by <?php echo htmlspecialchars($username); ?></h2>
    <table class="min-w-full bg-white rounded shadow">
        <thead>
            <tr>
                <th class="border px-4 py-2">Material</th>
                <th class="border px-4 py-2">Quantity</th>
                <th class="border px-4 py-2">Recycled At</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td class="border px-4 py-2"><?php echo $row['material_type']; ?></td>
                    <td class="border px-4 py-2"><?php echo $row['quantity']; ?></td>
                    <td class="border px-4 py-2"><?php echo $row['recycled_at']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
