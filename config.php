<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');  // Change this to your MySQL username
define('DB_PASSWORD', '');      // Change this to your MySQL password (leave empty if no password)
define('DB_NAME', 'personal_diary');

$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>