<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: auth/adminlogin.html");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "recycle_management_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get total users
$users_query = $conn->query("SELECT username FROM users");
$total_users = $users_query ? $users_query->num_rows : 0;

// Get total recycled materials
$total_query = $conn->query("SELECT SUM(quantity) as total FROM recycled_materials");
$total_recycled = $total_query ? $total_query->fetch_assoc()['total'] : 0;

// Store the users result for table display
$result = $users_query;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - EcoRecycle</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/animations.css">
    <script src="https://kit.fontawesome.com/your-font-awesome-kit.js"></script>
</head>
<body class="bg-gradient-to-br from-gray-50 to-blue-50 min-h-screen">
    <nav class="bg-white shadow-md animate-fadeIn">
        <div class="container mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <span class="text-green-600 text-2xl font-bold">♻️ Admin Dashboard</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="login.html" class="text-red-500 hover:text-red-600 transition-colors">Sign Out</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto p-6">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 animate-slideIn">
            <div class="bg-white rounded-xl shadow-lg p-6 hover-scale">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold">Total Users</h3>
                    <span class="text-blue-600 text-3xl">👥</span>
                </div>
                <p class="text-3xl font-bold text-gray-700"><?php echo $total_users; ?></p>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-6 hover-scale">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold">Total Recycled</h3>
                    <span class="text-green-600 text-3xl">♻️</span>
                </div>
                <p class="text-3xl font-bold text-gray-700"><?php echo number_format($total_recycled, 2); ?> kg</p>
            </div>
        </div>

        <!-- User Management Table -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8 animate-slideIn">
            <h2 class="text-2xl font-bold mb-6">User Management</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recycle Info</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4"><?php echo htmlspecialchars($row['username']); ?></td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($row['recycle_management']); ?></td>
                            <td class="px-6 py-4 space-x-2">
                                <a href="view_user.php?username=<?php echo urlencode($row['username']); ?>" 
                                   class="text-blue-600 hover:text-blue-800 transition-colors">View</a>
                                <a href="edit_user.php?username=<?php echo urlencode($row['username']); ?>" 
                                   class="text-green-600 hover:text-green-800 transition-colors">Edit</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Orders Table -->
        <div class="bg-white rounded-xl shadow-lg p-6 animate-slideIn mb-8">
            <h2 class="text-2xl font-bold mb-6">Recent Recycling Orders</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Material</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        $orders_query = $conn->query("SELECT * FROM recycling_orders ORDER BY created_at DESC LIMIT 10");
                        while ($order = $orders_query->fetch_assoc()):
                        ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4"><?php echo htmlspecialchars($order['username']); ?></td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($order['material_type']); ?></td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($order['quantity']); ?> kg</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-sm 
                                    <?php
                                    switch($order['status']) {
                                        case 'pending': echo 'bg-yellow-100 text-yellow-800'; break;
                                        case 'processing': echo 'bg-blue-100 text-blue-800'; break;
                                        case 'completed': echo 'bg-green-100 text-green-800'; break;
                                        case 'rejected': echo 'bg-red-100 text-red-800'; break;
                                    }
                                    ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <select onchange="updateOrderStatus(<?php echo $order['id']; ?>, this.value)" 
                                        class="rounded border-gray-300 text-sm">
                                    <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="completed" <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="rejected" <?php echo $order['status'] == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                </select>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function updateOrderStatus(orderId, status) {
            fetch('update_order_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `order_id=${orderId}&status=${status}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error updating status: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating status');
            });
        }

        // Card hover effects
        document.querySelectorAll('.hover-scale').forEach(card => {
            card.addEventListener('mouseover', () => {
                card.style.transform = 'scale(1.02)';
                card.style.transition = 'transform 0.2s ease';
            });
            
            card.addEventListener('mouseout', () => {
                card.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>