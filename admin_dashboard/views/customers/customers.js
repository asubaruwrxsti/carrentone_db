class Customer {
    constructor() {}

    async loadCustomer(id) {
        const response = await fetch(`/admin_dashboard/index.php/api/customers/${id}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        const data = await response.json();
        return data;
    }

    async loadOrders(id) {
        let revenue = await fetch(`/admin_dashboard/index.php/api/revenue/`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        let carData = await fetch(`/admin_dashboard/index.php/api/cars/`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        revenue = await revenue.json();
        carData = await carData.json();

        // filter where customer_id = id
        revenue = revenue.filter((order) => {
            return order.customer_id == id;
        });

        // filter where car_id = revenue.car_id
        carData = carData.filter((car) => {
            return revenue.some((order) => {
                return order.car_id == car.id;
            });
        });

        return {
            revenue: revenue,
            carData: carData
        };
    }
}