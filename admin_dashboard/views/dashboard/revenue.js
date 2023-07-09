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
  
        if (year === targetMonth.slice(0, 4) && month === targetMonth.slice(5)) {
          if (!revenuePerMonth[monthKey]) {
            revenuePerMonth[monthKey] = 0;
          }
  
          revenuePerMonth[monthKey] += parseInt(price);
        }
      }
  
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
  
    uniqueCustomersMonth(targetMonth = null) {
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
  
    uniqueCustomers() {
      let uniqueCustomers = new Set();
      for (let rental of this.rentalData) {
        const { customer_id } = rental;
        uniqueCustomers.add(customer_id);
      }
      return uniqueCustomers.size;
    }
  }