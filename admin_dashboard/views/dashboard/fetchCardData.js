document.addEventListener('DOMContentLoaded', function() {
  var base_card_data = [];
  var card_ids = [];

  /* Fetch data from API */
  async function fetchData() {
    try {
      const response = await fetch('/admin_dashboard/index.php/api/revenue/', {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json'
        }
      });
      base_card_data = await response.json();
    } catch (error) {
      console.error('Error:', error);
    }

    await new Promise(r => setTimeout(r, 1000));
    enumerateCards();
    populateCards();
  }

  /* Enumerate cards */
  function enumerateCards() {
    let cards = document.getElementsByClassName('card');
    for (let i = 0; i < cards.length; i++) {
      let card = cards[i];
      let canvas = card.querySelector('canvas');
      if (canvas) {
        card_ids.push(canvas.id);
      }

      let h3 = card.querySelector('h3');
      if (h3) {
        card_ids.push(h3.id);
      }
    }
  }

  function populateCards() {
    var revenue = new Revenue(base_card_data);
    let map = [{'revenueTotal': 'int', 'revenueMonth': 'chart', 'uniqueCustomersMonth': 'int', 'uniqueCustomers': 'int'}];

    for (let card_id of card_ids) {
      let element = document.getElementById(card_id);
      let data = revenue[card_id]();
      
      if (map[0][card_id] == 'chart') {
        const ctx = element.getContext('2d');
        new Chart(ctx, {
          type: 'line',
          data: {
            labels: Object.keys(data),
            datasets: [{
              label: 'Revenue',
              data: Object.values(data),
              tension: 0.4,
              borderWidth: 1,
            }]
          },
          options: {
            responsive: true,
            scales: {
              x: {
                grid: {
                  display: false
                }
              }
            }
          }
        });
      } else {
        element.innerHTML = data;
      }
    }

    revenue.revenueGrowth();
    revenue.customerGrowth();
    revenue.uniqueCustomersGrowth();
  }

  fetchData();
});