<?php
session_start();
$conn = new mysqli("localhost", "root", "", "recycle_management_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Use prepared statement to prevent SQL injection
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username']; // Store username in session
            header("Location: userdashboard.html"); // Redirect to user dashboard
            exit();
        } else {
            echo "<script>alert('Invalid credentials!'); window.location.href='login.html';</script>";
        }
    } else {
        echo "<script>alert('User not found! Please sign up.'); window.location.href='signup.html';</script>";
    }

    $stmt->close();
}
$conn->close();
?>