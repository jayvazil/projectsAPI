
// Example data (later replace with PHP+MySQL data)
    const ctx = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctx, {
      type: 'pie',
      data: {
        labels: ['Food', 'Transport', 'Rent', 'Shopping'],
        datasets: [{
          label: 'Expenses by Category',
          data: [500, 200, 800, 150], // Replace with PHP dynamic data
        }]
      }
    });
