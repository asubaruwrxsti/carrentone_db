document.addEventListener('DOMContentLoaded', function() {
  var base_data = [];
  let car_labels;
  let order_count;

  /* Fetch data from API */
  async function fetchData() {
    try {
      const response = await fetch('/admin_dashboard/index.php/api/cars/', {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json'
        }
      });
      base_data = await response.json();
      car_labels = Object.values(base_data).map((item) => item.name);
      order_count = Object.values(base_data).map((item) => item.order_count);

    } catch (error) {
      console.error('Error:', error);
    }

    await new Promise(r => setTimeout(r, 1000));
    let loading = document.getElementById('loading');
    loading.style.display = 'none';

    initializeChart();
  }

  /* Initialize chart */
  function initializeChart() {
    const ctx = document.getElementById('myChart');

    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: car_labels,
        datasets: [{
          label: 'Perfomance of order/car',
          data: order_count,
          borderWidth: 1,
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true,
          }
        }
      }
    });
  }
  fetchData();
});