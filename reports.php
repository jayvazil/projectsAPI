<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM expenses WHERE user_id='$user_id' ORDER BY date DESC";
<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'];

// Total spent per category
$sql = "SELECT category, SUM(amount) as total FROM expenses WHERE user_id='$user_id' GROUP BY category";
$result = $conn->query($sql);

$categories = [];
$totals = [];

while($row = $result->fetch_assoc()) {
    $categories[] = $row['category'];
    $totals[] = $row['total'];
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Expense Reports</title>
  <script src="https:chart.js"></script>
</head>
<body>
  <h2>Expense Reports</h2>
  <canvas id="categoryChart" width="400" height="200"></canvas>
  
  <script>
    const ctx = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctx, {
      type: 'pie',
      data: {
        labels: <?php echo json_encode($categories); ?>,
        datasets: [{
          data: <?php echo json_encode($totals); ?>,
        }]
      }
    });
  </script>
</body>
</html>

