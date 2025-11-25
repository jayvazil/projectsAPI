<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: reports.html");
    exit();
}

$servername = "127.0.0.1";
$username   = "root";
$password   = "";
$database   = "expense_tracker";
$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$start_date = trim($_POST['start_date'] ?? '');
$end_date   = trim($_POST['end_date'] ?? '');
$category   = trim($_POST['category'] ?? '');

if ($start_date === '' || $end_date === '') {
    die("Start date and end date are required. <a href='reports.html'>Back</a>");
}

$sql = "SELECT expense_date, category, description, amount 
        FROM expenses 
        WHERE expense_date BETWEEN ? AND ?";
if ($category !== '') {
    $sql .= " AND category LIKE ?";
}
$sql .= " ORDER BY expense_date DESC";

$stmt = $conn->prepare($sql);
if ($category !== '') {
    $like = '%' . $category . '%';
    $stmt->bind_param("sss", $start_date, $end_date, $like);
} else {
    $stmt->bind_param("ss", $start_date, $end_date);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Expense Report</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f5f6f8;
        padding: 20px;
        margin: 0;
    }
    .container {
        background: #fff;
        max-width: 950px;
        margin: auto;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 0 8px rgba(0,0,0,0.1);
        position: relative;
    }
    h2 {
        margin-top: 0;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }
    th, td {
        border: 2px solid black;
        padding: 10px;
        text-align: left;
    }
    th {
        background: #007bff;
        color: white;
    }
    tr:nth-child(even) {
        background: #f9f9f9;
    }
    .export-btn {
        position: absolute;
        top: 25px;
        right: 25px;
        background: none;
        border: none;
        cursor: pointer;
    }
    .export-btn img {
        width: 28px;
        height: 28px;
        filter: invert(35%) sepia(90%) saturate(400%) hue-rotate(200deg);
        transition: 0.2s;
    }
    .export-btn img:hover {
        filter: invert(20%) sepia(100%) saturate(800%) hue-rotate(210deg);
        transform: scale(1.1);
    }
    .back-link {
        margin-top: 20px;
        display: inline-block;
        color: #007bff;
        text-decoration: none;
    }
    .back-link:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>
<div class="container">
    <h2>Expense Report</h2>
    <p><strong>Period:</strong> <?= htmlspecialchars($start_date) ?> → <?= htmlspecialchars($end_date) ?></p>
    <?php if ($category !== ''): ?>
        <p><strong>Category:</strong> <?= htmlspecialchars($category) ?></p>
    <?php endif; ?>

    <?php if ($result && $result->num_rows > 0): ?>
        <!-- ✅ Download icon button -->
        <button type="button" id="download-btn" class="export-btn" title="Download CSV">
            <img src="https://cdn-icons-png.flaticon.com/512/724/724933.png" alt="Download">
        </button>

        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php $total = 0.0; ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php $total += (float)$row['amount']; ?>
                    <tr>
                        <td><?= htmlspecialchars($row['expense_date']) ?></td>
                        <td><?= htmlspecialchars($row['category']) ?></td>
                        <td><?= htmlspecialchars($row['description']) ?></td>
                        <td><?= htmlspecialchars(number_format($row['amount'], 2)) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <p><strong>Total:</strong> <?= number_format($total, 2) ?></p>
    <?php else: ?>
        <p style="color:red;">No expenses found for the selected filters.</p>
    <?php endif; ?>

    <a href="reports.html" class="back-link">← Back to Reports Page</a>
</div>

<script>
document.getElementById('download-btn')?.addEventListener('click', async () => {
    // Collect the same data used in the report
    const startDate = '<?= $start_date ?>';
    const endDate = '<?= $end_date ?>';
    const category = '<?= $category ?>';

    // Log for debugging
    console.log('Exporting CSV with:', { startDate, endDate, category });

    // Prepare form data for POST
    const formData = new FormData();
    formData.append('start_date', startDate);
    formData.append('end_date', endDate);
    formData.append('category', category);

    try {
        // Send POST request to the same folder’s download_report.php
        const response = await fetch('./download_report.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            const errorText = await response.text();
            console.error('Server Error:', errorText);
            alert('❌ Download failed: ' + errorText);
            return;
        }

        // Create CSV blob and trigger download
        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'expense_report.csv';
        document.body.appendChild(a);
        a.click();
        a.remove();
        window.URL.revokeObjectURL(url);
    } catch (err) {
        console.error('⚠️ Fetch error:', err);
        alert('Network or fetch error: ' + err.message);
    }
});
</script>


</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
