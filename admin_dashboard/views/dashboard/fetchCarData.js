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
      base_car_data.sort((a, b) => b.order_count - a.order_count);
      base_car_data = base_car_data.slice(0, 10);

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
    const chart = document.getElementById('myChart');
    const ctx = chart.getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 350);
    gradient.addColorStop(0, 'rgba(250,174,50,1)');   
    gradient.addColorStop(1, 'rgba(250,174,50,0)');

    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: car_labels,
        datasets: [{
          backgroundColor: gradient,
          fill: "start",
          data: order_count,
          borderWidth: 1,
        }]
      },
      options: {
        scales: {
          x: {
            grid: {
              display: false
            }
          },
        },
        plugins: {
          legend: {
            display: false
          }
        }
      }
    });
  }
  fetchData();
});