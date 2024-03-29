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
        let growRate = parseInt(((filteredData - lastMonthData) / lastMonthData) * 100);
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

    async hasNextOrder(id) {
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
        if (filteredData.length == 0) return false;
        const latestRentalDate = new Date(filteredData[0].rental_date);
        const rentalDuration = parseInt(filteredData[0].rental_duration);
        const returnDate = new Date(latestRentalDate + rentalDuration);
        return (returnDate > new Date()) ? latestRentalDate : false;
    }

    async updateData(car) {
        car = Object.fromEntries(car);
        delete car.image;
        const response = await fetch(`/admin_dashboard/index.php/api/cars/edit/${car.id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(car)
        });
        const data = await response.json();
        return data;
    }

    async insertData(car) {
        car = Object.fromEntries(car);
        const response = await fetch(`/admin_dashboard/index.php/cars/add/`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(car)
        });
        const data = await response.json();
        return data;
    }

    async deleteData(id) {
        const response = await fetch(`/admin_dashboard/index.php/api/cars/edit/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        const data = await response.json();
        return data;
    }

    async deleteImage(image) {
        image = Object.fromEntries(image);
        const response = await fetch(`/admin_dashboard/index.php/cars/images/${image.id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({'image_index': image.image_index})
        });
        const data = await response.json();
        return data;
    }

    async insertImage(data) {
        data = Object.fromEntries(data);
        const response = await fetch(`/admin_dashboard/index.php/cars/images/${data.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        const res = await response.json();
        return res;
    }
}