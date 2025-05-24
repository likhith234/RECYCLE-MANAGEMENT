<?php
$conn = new mysqli("localhost", "root", "", "recycle_management_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_POST['username'];
$recycle_management = $_POST['gmail'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT);

$sql = "INSERT INTO users (username, recycle_management, password) VALUES ('$username', '$recycle_management', '$password')";

if ($conn->query($sql) === TRUE) {
    echo "Signup successful! <a href='login.html'>Login here</a>";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
