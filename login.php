<?php
session_start();
$host = "localhost";   // use 127.0.0.1 instead of localhost
$user = "root";        // default XAMPP user
$pass = "";            // default password is empty
$db   = "expense_tracker";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            header("Location: dashboard.html");
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "User not found!";
    }
}
?>


