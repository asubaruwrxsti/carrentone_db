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
        
        // filter data where rental_date is within the month and year
        // rental_date format: YYYY-MM-DD
        if (month != null && year != null) {
            month = parseInt(month);
            year = parseInt(year);
            data = data.filter(order => {
                let rentalDate = new Date(order.rental_date);
                console.log(rentalDate.getMonth())
                return (rentalDate.getMonth() + 1) == month && (rentalDate.getFullYear()) == year;
            });
        }
        return data;
    }
}