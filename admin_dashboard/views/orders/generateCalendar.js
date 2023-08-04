$("#calendar").evoCalendar({
    'eventDisplayDefault': false,
    'sidebarDisplayDefault': false,
    'format': 'MM dd, yyyy',
    'todayHighlight': false,
    'firstDayOfWeek': 1
});

orders.forEach(order => {
    $("#calendar").evoCalendar('addCalendarEvent', [
        {
            id: order.orderId,
            name: order.renterName,
            type: 'Rental',
            date: order.startDate,
            color: generateUniqueColor(order.orderId)
        }
    ]);
});

function generateView() {
    document.querySelectorAll('.calendar-day').forEach(item => {
        let div = item.querySelector('div');
        let date = new Date(div.getAttribute('data-date-val'));
        let eventIndicator = div.querySelector('.event-indicator');
        if (eventIndicator) eventIndicator.style.display = 'none';

        orders.forEach(order => {
            let startDate = new Date(order.startDate);
            let endDate = new Date(order.endDate);
            
            if (date.getTime() >= startDate.getTime() && date.getTime() <= endDate.getTime()) {
                let newDiv = item.appendChild(document.createElement('div'));
                newDiv.className = 'day spanned-event';
                newDiv.style.margin = '5px 0';
                newDiv.style.height = '5px';
                newDiv.style.backgroundColor = generateUniqueColor(order.orderId);
                newDiv.style.boxShadow = '0 0 10px ' + generateUniqueColor(order.orderId);
                newDiv.style.width = '100%';

                if (startDate.getTime() == date.getTime()) {
                    newDiv.style.borderRadius = '20px 0 0 20px';
                } else if (endDate.getTime() == date.getTime()) {
                    newDiv.style.borderRadius = '0 20px 20px 0';
                } else {
                    newDiv.style.borderRadius = '0';
                }
                
                // add event listener on mouse hover and on click
                newDiv.addEventListener('mouseover', function() {
                    console.log(order);
                });

            } else {
                /* BACKLOG: Utilize order overlap 
                var overlappingOrders = orders.filter(o => {
                    let sD = new Date(o.startDate);
                    let eD = new Date(o.endDate);
                    return date.getTime() <= eD.getTime();
                });
                if (overlappingOrders.length > 1) {
                    let newDiv = item.appendChild(document.createElement('div'));
                    newDiv.className = 'day no-event';
                    newDiv.style.margin = '5px 0';
                    newDiv.style.height = '15%';
                }
                */

                let newDiv = item.appendChild(document.createElement('div'));
                newDiv.className = 'day no-event';
                newDiv.style.margin = '5px 0';
                newDiv.style.height = '15%';
            }
        });
    });
}
generateView();
$('#calendar').on('selectMonth', function(event, newDate, oldDate) {
    generateView();
});