<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $user_id = $_SESSION['user_id'];
    $amount  = $_POST['amount'];
    $category= $_POST['category'];
    $date    = $_POST['date'];
    $notes   = $_POST['notes'];

    $sql = "INSERT INTO expenses (user_id, amount, category, date, notes) 
            VALUES ('$user_id','$amount','$category','$date','$notes')";

    if ($conn->query($sql) === TRUE) {
        header("Location: view_expenses.php");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>


