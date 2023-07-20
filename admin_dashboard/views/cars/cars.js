class Cars {
    constructor(){};

    async getCarById(id) {
        const response = await fetch(`/admin_dashboard/index.php/api/cars/${id}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        const data = await response.json();
        return data;
    }

    async getRevenueById(id) {
        const response = await fetch(`/admin_dashboard/index.php/api/revenue/`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        const data = await response.json();
        const filteredData = data.filter((item) => {
            return item.car_id == id;
        }).reduce((acc, item) => {
            return acc + parseInt(item.price);
        }, 0);
        return this.caclulateGrowRate(filteredData, data);
    }

    caclulateGrowRate(filteredData, data) {
        const lastMonth = new Date();
        lastMonth.setMonth(lastMonth.getMonth() - 1);
        const lastMonthData = data.filter((item) => {
            return new Date(item.rental_date) > lastMonth;
        }).reduce((acc, item) => {
            return acc + parseInt(item.price);
        }, 0);
        let growRate = ((filteredData - lastMonthData) / lastMonthData) * 100;
        if (isNaN(growRate) || growRate == Infinity) growRate = 0;
        return {'revenue': filteredData, 'growRate': growRate};
    }

    async calculateIsAvailable(id) {
        const response = await fetch(`/admin_dashboard/index.php/api/revenue/`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        const data = await response.json();
        // filter data by car id, get the latest rental_date and determine if the the rental_date + rental_duration is in the future
        const filteredData = data.filter((item) => {
            return item.car_id == id;
        }).sort((a, b) => {
            return new Date(b.rental_date) - new Date(a.rental_date);
        });

        if (filteredData.length == 0) return true;
        const latestRentalDate = new Date(filteredData[0].rental_date);
        const rentalDuration = parseInt(filteredData[0].rental_duration);
        const returnDate = new Date(latestRentalDate);

        returnDate.setDate(returnDate.getDate() + rentalDuration);
        return returnDate < new Date();
    }

    async getLatestOrders(id) {
        const response = await fetch(`/admin_dashboard/index.php/api/revenue/`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        const data = await response.json();
        const filteredData = data.filter((item) => {
            return item.car_id == id;
        }).sort((a, b) => {
            return new Date(b.rental_date) - new Date(a.rental_date);
        });
        return filteredData.slice(0, 3);
    }
}