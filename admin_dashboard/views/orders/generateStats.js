function generateCarMapping(id) {
    let thisMonth = new Date();
    let color = generateUniqueColor(id);
    let element = document.getElementById('carMapping');

    let monthMapping = orders.filter(order => {
        let date = new Date(order.rental_date);
        return date.getMonth() == thisMonth.getMonth(), date.getFullYear() == thisMonth.getFullYear();
    });

    
}