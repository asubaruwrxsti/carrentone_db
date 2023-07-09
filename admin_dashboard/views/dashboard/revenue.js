class Revenue {
    constructor(rentalData) {
      this.rentalData = rentalData;
    }
  
    revenueMonth(targetMonth = null) {
      let revenuePerMonth = {};
      if (targetMonth == null) {
        targetMonth = new Date().toISOString().slice(0, 7);
      }

      for (let rental of this.rentalData) {
        const { rental_date, price } = rental;
        const [year, month] = rental_date.split("-");
    
        const monthKey = `${year}-${month}`;
    
        if (!revenuePerMonth[monthKey]) {
          revenuePerMonth[monthKey] = 0;
        }
    
        revenuePerMonth[monthKey] += parseInt(price);
      }
      revenuePerMonth = Object.fromEntries(
        Object.entries(revenuePerMonth).sort()
      );
      return revenuePerMonth;
    }
  
    revenueTotal() {
      let totalRevenue = 0;
      for (let rental of this.rentalData) {
        const { price } = rental;
        totalRevenue += parseInt(price);
      }
      return totalRevenue;
    }

    revenueGrowth() {
      let revenuePerMonth = this.revenueMonth();
      let revenueGrowth = {};
      let previousRevenue = 0;
      for (let month in revenuePerMonth) {
        revenueGrowth[month] = revenuePerMonth[month] - previousRevenue;
        previousRevenue = revenuePerMonth[month];
      }
      let growth = document.getElementById('revenueGrowth');
      growth.innerHTML = Math.round((revenueGrowth[Object.keys(revenueGrowth)[Object.keys(revenueGrowth).length - 1]] / previousRevenue) * 100) + '%';
      if (growth.innerHTML.includes('-')) {
        growth.style.color = 'lightcoral';
      }
    }
  
    uniqueCustomersThisMonth(targetMonth = null) {
        let uniqueCustomers = new Set();
        if (targetMonth == null) {
          targetMonth = new Date().toISOString().slice(0, 7);
        }

        for (let rental of this.rentalData) {
          const { customer_id, rental_date } = rental;
          const [year, month] = rental_date.split("-");

          if (year === targetMonth.slice(0, 4) && month === targetMonth.slice(5)) {
              uniqueCustomers.add(customer_id);
          }
        }
        return uniqueCustomers.size;
    }

    customerGrowth() {
      let uniqueCustomers = this.uniqueCustomersThisMonth();
      let previousCustomers = this.uniqueCustomersThisMonth(new Date(new Date().setMonth(new Date().getMonth() - 1)).toISOString().slice(0, 7));
      let growth = document.getElementById('customerGrowth');
      growth.innerHTML = Math.round(((uniqueCustomers - previousCustomers) / previousCustomers) * 100) + '%';
      if (growth.innerHTML.includes('-')) {
        growth.style.color = 'lightcoral';
      }
      if (growth.innerHTML == '0%') {
        growth.style.color = 'gold';
      }
    }
  
    uniqueCustomers() {
      let uniqueCustomers = new Set();
      for (let rental of this.rentalData) {
        const { customer_id } = rental;
        uniqueCustomers.add(customer_id);
      }
      return uniqueCustomers.size;
    }

    uniqueCustomersGrowth() {
      let uniqueCustomers = this.uniqueCustomers();
      let previousCustomers = this.uniqueCustomers(new Date(new Date().setMonth(new Date().getMonth() - 1)).toISOString().slice(0, 7));
      let growth = document.getElementById('uniqueCustomerGrowth');
      growth.innerHTML = Math.round(((uniqueCustomers - previousCustomers) / previousCustomers) * 100) + '%';
      if (growth.innerHTML.includes('-')) {
        growth.style.color = 'lightcoral';
      }
      if (growth.innerHTML == '0%') {
        growth.style.color = 'gold';
      }
    }
  }