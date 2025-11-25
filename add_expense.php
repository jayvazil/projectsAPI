<?php
session_start();

// Database connection
$host = "127.0.0.1";   // use 127.0.0.1 to avoid socket issues
$user = "root";
$pass = "";
$db   = "expense_tracker";

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Check if user is logged in
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    die("You must be logged in to add expenses. <a href='login.html'>Login</a>");
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and validate form inputs
    $amount   = $_POST['amount'] ?? '';
    $category = $_POST['category'] ?? '';
    $date     = $_POST['date'] ?? '';
    $notes    = $_POST['notes'] ?? '';

    if ($amount === '' || $category === '' || $date === '') {
        die("All fields except notes are required. <a href='add_expense.html'>Back</a>");
    }

    // Use prepared statements to avoid SQL injection
    $stmt = $conn->prepare("INSERT INTO expenses (user_id, amount, category, expense_date, description)
                            VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("idsss", $user_id, $amount, $category, $date, $notes);

    if ($stmt->execute()) {
        // Redirect back to dashboard
        header("Location: dashboard.html");
        exit();
    } else {
        echo "Error inserting expense: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
