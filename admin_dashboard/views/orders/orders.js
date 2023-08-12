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

        let revenue = await fetch(`/admin_dashboard/index.php/api/revenue/`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        revenue = await revenue.json();
        revenue.forEach(order => {
            if (order.car_id == carId) {
                let orderRentalDate = new Date(order.rental_date);
                let orderReturnDate = new Date(orderRentalDate.getTime() + order.rental_duration * 24 * 60 * 60 * 1000);

                if (orderRentalDate <= new Date(rentalDate) && new Date(returnDate) <= orderReturnDate) {
                    busy = true;
                    return;
                }
            }
        });
        return busy;
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

    async addOrder(order) {
        order = JSON.stringify(Object.fromEntries(order));

        const response = await fetch(`/admin_dashboard/index.php/api/revenue/edit/`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: order
        });
        let data = await response.json();

        let car = await fetch(`/admin_dashboard/index.php/api/cars/${JSON.parse(order).car_id}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        car = await car.json();
        let update = await fetch(`/admin_dashboard/index.php/api/cars/edit/${JSON.parse(order).car_id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                name: car[0].name,
                price: car[0].price,
                description: car[0].description,
                travel_distance: car[0].travel_distance,
                transmission: car[0].transmission,
                available: car[0].available,
                next_order: new Date().getTime().setHours(0, 0, 0, 0),
                order_count: String(parseInt(car[0].order_count) + 1),
                created_at: car[0].created_at
            })
        });
        update = await update.json();
        console.log(update);
        return;
    }

    async insertCustomer(customer) {
        const response = await fetch(`/admin_dashboard/index.php/api/customers/edit/`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(customer[0])
        });
        let data = await response.json();

        let customerId = await fetch(`/admin_dashboard/index.php/api/customers/`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        customerId = await customerId.json();
        customerId = customerId[customerId.length - 1].id;
        return customerId;
    }
}