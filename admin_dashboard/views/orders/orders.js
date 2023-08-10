class Orders {
    constructor() {}

    async getOrders(month = null, year = null) {
        const response = await fetch(`/admin_dashboard/index.php/api/revenue/`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        let data = await response.json();

        if (month != null && year != null) {
            month = parseInt(month) + 1;
            year = parseInt(year);
            data = data.filter(order => {
                let rentalDate = new Date(order.rental_date);
                return (rentalDate.getMonth() + 1 == month && rentalDate.getFullYear() == year);
            });
        }
        return data;
    }

    async getCar(carId) {
        const response = await fetch(`/admin_dashboard/index.php/api/cars/${carId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        let data = await response.json();
        return data;
    }

    async isCarBusy(carId, rentalDate, returnDate) {
        let busy = false;
		let car = await this.getCar(carId);

        let revenue = await fetch(`/admin_dashboard/index.php/api/revenue/`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        revenue = await revenue.json();

        return revenue.some(order => {
            let orderRentalDate = new Date(order.rental_date);
            let orderReturnDate = orderRentalDate.setDate(orderRentalDate.getDate() + order.rental_duration);
			return (order.car_id == carId && orderRentalDate <= rentalDate && orderReturnDate >= returnDate);
        });
    }

    async searchCustomer(customerId) {
        const response = await fetch(`/admin_dashboard/index.php/api/customers/${customerId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        let data = await response.json();
        return data;
    }
}