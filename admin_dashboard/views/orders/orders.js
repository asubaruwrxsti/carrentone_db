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

        if (month != null || year != null) {
            data = data.filter(order => {
                let date = new Date(order.rental_date);
                if (month != null && year != null) {
                    return date.getMonth() == month && date.getFullYear() == year;
                } else if (month != null) {
                    return date.getMonth() == month;
                } else {
                    return date.getFullYear() == year;
                }
            });
        }

        return data;
    }
}