<?php
session_start();

$valid_passkeys = [
    "admin_1201", "admin_1202", "admin_1203", "admin_1204", "admin_1205",
    "admin_1206", "admin_1207", "admin_1208", "admin_1209", "admin_1210"
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $passkey = trim($_POST['passkey']);

    if (in_array($passkey, $valid_passkeys)) {
        $_SESSION['admin'] = $passkey;
        header("Location: adminpanel.php");
        exit();
    } else {
        echo "<script>alert('Invalid admin passkey!'); window.location.href='adminlogin.html';</script>";
    }
}
?>
