<?php
$host = "localhost";
$user = "j..api"; 
$pass = "";
$db   = "expense_tracker";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
