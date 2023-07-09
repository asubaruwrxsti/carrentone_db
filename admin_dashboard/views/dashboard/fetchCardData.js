document.addEventListener('DOMContentLoaded', function() {
  var base_card_data = [];

  /* Fetch data from API */
  async function fetchData() {
    try {
      const response = await fetch('/admin_dashboard/index.php/api/cars/', {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json'
        }
      });
      base_card_data = await response.json();
      car_labels = Object.values(base_card_data).map((item) => item.name);
      order_count = Object.values(base_card_data).map((item) => item.order_count);

    } catch (error) {
      console.error('Error:', error);
    }

    await new Promise(r => setTimeout(r, 1000));
    let loading = document.getElementById('loading');
    loading.style.display = 'none';

    // initializeChart();
    enumerateCards();
  }

  function enumerateCards() {
    let cards = document.getElementsByClassName('card');
    for (let i = 0; i < cards.length; i++) {
      let card = cards[i];
      let canvas = card.querySelector('.card-body .card-title canvas');
      console.log(canvas);
    }
  }
  

  /* Initialize chart */
  function initializeChart(id) {
    const ctx = document.getElementById(id);

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