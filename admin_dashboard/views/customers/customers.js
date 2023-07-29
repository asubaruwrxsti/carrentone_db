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

        revenue = revenue.filter((order) => {
            return order.customer_id == id;
        });
        revenue = revenue.slice(0, 3);

        carData = carData.filter((car) => {
            return revenue.some((order) => {
                return order.car_id == car.id;
            });
        });
        carData = carData.slice(0, 3);

        return {
            revenue: revenue,
            carData: carData
        };
    }

    async deleteCustomer(id) {
        const response = await fetch(`/admin_dashboard/index.php/api/customers/edit/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        const data = await response.json();
        return data;
    }

    async updateCustomer(id, data) {
        const response = await fetch(`/admin_dashboard/index.php/api/customers/edit/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        const res = await response.json();
        return res;
    }
}