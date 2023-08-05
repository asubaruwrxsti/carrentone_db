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

    async getCarName(carId) {
        const response = await fetch(`/admin_dashboard/index.php/api/cars/${carId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        let data = await response.json();
        return data;
    }
}