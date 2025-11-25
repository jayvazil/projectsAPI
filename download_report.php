<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "";
$db   = "expense_tracker";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get posted data
$start_date = $_POST['start_date'] ?? '';
$end_date   = $_POST['end_date'] ?? '';
$category   = $_POST['category'] ?? '';
$user_id    = $_SESSION['user_id'] ?? 1; // Adjust if needed

if ($start_date === '' || $end_date === '') {
    die("Start and End dates are required.");
}

// Build query
$sql = "SELECT expense_date, category, description, amount 
        FROM expenses 
        WHERE user_id = ? 
        AND expense_date BETWEEN ? AND ?";

if (!empty($category)) {
    $sql .= " AND category = ?";
}

$stmt = $conn->prepare($sql);

if (!empty($category)) {
    $stmt->bind_param("isss", $user_id, $start_date, $end_date, $category);
} else {
    $stmt->bind_param("iss", $user_id, $start_date, $end_date);
}

$stmt->execute();
$result = $stmt->get_result();

// CSV Headers
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="expense_report.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Date', 'Category', 'Description', 'Amount']);

// Write data rows
while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
exit;
?>
