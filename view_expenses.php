<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM expenses WHERE user_id='$user_id' ORDER BY date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
  <title>My Expenses</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h2>My Expenses</h2>
  <table border="1" width="80%" align="center">
    <tr>
      <th>Date</th><th>Category</th><th>Amount</th><th>Notes</th>
    </tr>
    <?php while($row = $result->fetch_assoc()) { ?>
    <tr>
      <td><?php echo $row['date']; ?></td>
      <td><?php echo $row['category']; ?></td>
      <td><?php echo $row['amount']; ?></td>
      <td><?php echo $row['notes']; ?></td>
    </tr>
    <?php } ?>
  </table>
</body>
</html>


