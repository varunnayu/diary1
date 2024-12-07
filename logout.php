<?php
session_start();

// Destroy the session to log out the user
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Logout</title>
    <script>
        // Display a popup message and redirect to the login page
        alert("You have been successfully logged out.");
        window.location.href = "login.php";
    </script>
</head>
<body>
</body>
</html>
