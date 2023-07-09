document.addEventListener('DOMContentLoaded', function() {
  var base_car_data = [];
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
      base_car_data = await response.json();
      car_labels = Object.values(base_car_data).map((item) => item.name);
      order_count = Object.values(base_car_data).map((item) => item.order_count);
    } catch (error) {
      alertify.warning(error);
    }

    await new Promise(r => setTimeout(r, 1000));
    const loadingElements = document.querySelectorAll("#loading");
    loadingElements.forEach(element => {
      element.remove();
    });

    let revenueDependencies = document.getElementById('revenueDependencies');
    revenueDependencies.style.display = 'flex';

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
          x: {
            beginAtZero: true,
            grid: {
              display: false
            }
          }
        }
      }
    });
  }
  fetchData();
});