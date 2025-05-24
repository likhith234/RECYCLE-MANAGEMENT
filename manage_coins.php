// ... existing code ...
if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/adminlogin.html");
    exit();
}
// ... existing code ...

    <script>
    function updateCoins(event, username) {
        event.preventDefault();
        const coins = document.getElementById(`coins_${username}`).value;
        
        fetch('../api/update_coins.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `username=${encodeURIComponent(username)}&coins=${coins}`
        })
        // ... rest of the function remains the same ...
    }
    </script>
