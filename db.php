<?php
$host = "localhost";   // use 127.0.0.1 instead of localhost
$user = "root";        // default XAMPP user
$pass = "";            // default password is empty
$db   = "expense_tracker";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
