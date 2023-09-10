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

    async isCarBusy(carId, rentalDate, returnDate, isUpdate = false, orderId = null) {
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

                if (orderRentalDate.getTime() <= new Date(returnDate).getTime() && orderReturnDate.getTime() >= new Date(rentalDate).getTime()) {
                    busy = true;
                }

                // if its an update, ignore the set date of the order being updated
                if (isUpdate && order.id == orderId) {
                    if (orderRentalDate.getTime() <= new Date(returnDate).getTime() && orderReturnDate.getTime() >= new Date(rentalDate).getTime()) {
                        busy = false;
                    }
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

        await fetch(`/admin_dashboard/index.php/api/cars/${JSON.parse(order).car_id}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        }).then(response => response.json()).then(car => {
            fetch(`/admin_dashboard/index.php/api/cars/edit/${JSON.parse(order).car_id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    order_count: String(parseInt(car[0].order_count) + 1)
                })
            });
        });
        
        const latestOrder = await fetch(`/admin_dashboard/index.php/api/revenue/`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        let latestOrderId = await latestOrder.json();
        latestOrderId = latestOrderId[latestOrderId.length - 1].id;
        return latestOrderId;
    }

    async updateOrder(order) {
        order = JSON.stringify(Object.fromEntries(order));

        const response = await fetch(`/admin_dashboard/index.php/api/revenue/edit/${JSON.parse(order).id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: order
        });
        let data = await response.json();
        return data;
    }

    async deleteOrder(orderId) {
        const response = await fetch(`/admin_dashboard/index.php/api/revenue/edit/${orderId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        let data = await response.json();
        return data;
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

    async availableCars(rentalDate = null, returnDate = null) {
        let cars = await fetch(`/admin_dashboard/index.php/api/cars/`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        cars = await cars.json();

        let revenue = await fetch(`/admin_dashboard/index.php/api/revenue/`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        revenue = await revenue.json();

        let unusedCars = [];
        if (rentalDate != null && returnDate != null) {
            cars.forEach(car => {
                let busy = false;
                revenue.forEach(order => {
                    if (order.car_id == car.id) {
                        let orderRentalDate = new Date(order.rental_date);
                        let orderReturnDate = new Date(orderRentalDate.getTime() + order.rental_duration * 24 * 60 * 60 * 1000);

                        if (orderRentalDate.getTime() <= new Date(returnDate).getTime() && orderReturnDate.getTime() >= new Date(rentalDate).getTime()) {
                            busy = true;
                        }
                    }
                });
                if (!busy) {
                    unusedCars.push(car);
                }
            });
        } else if (rentalDate !=null && returnDate == null) {
            cars.forEach(car => {
                let busy = false;
                revenue.forEach(order => {
                    if (order.car_id == car.id) {
                        let orderRentalDate = new Date(order.rental_date);
                        let orderReturnDate = new Date(orderRentalDate.getTime() + order.rental_duration * 24 * 60 * 60 * 1000);

                        if (orderRentalDate.getTime() <= new Date(rentalDate).getTime() && orderReturnDate.getTime() >= new Date(rentalDate).getTime()) {
                            busy = true;
                        }
                    }
                });
                if (!busy) {
                    unusedCars.push(car);
                }
            });
        } else {
            let month =  new Date(document.querySelector("#calendar > div.calendar-inner > table > tbody > tr:nth-child(1) > th").innerHTML).getMonth();
            let year = new Date(document.querySelector("#calendar > div.calendar-inner > table > tbody > tr:nth-child(1) > th").innerHTML).getFullYear();
            cars.forEach(car => {
                let busy = false;
                revenue.forEach(order => {
                    if (order.car_id == car.id) {
                        let orderRentalDate = new Date(order.rental_date);
                        if (orderRentalDate.getMonth() == month && orderRentalDate.getFullYear() == year) {
                            busy = true;
                        }
                    }
                });
                if (!busy) {
                    unusedCars.push(car);
                }
            });
        }
        return unusedCars;
    }
}