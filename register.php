<?php
$host = "localhost";   
$user = "root";        
$pass = "";            
$db   = "expense_tracker";

$conn = new mysqli($host, $user, $pass, $db);
header("Location: login.html");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $phone    = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, email,phone, password) VALUES ('$name','$email','$phone','$password')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Registration successful. <a href='login.html'>Login here</a>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

