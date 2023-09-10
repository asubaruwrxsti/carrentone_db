$("#calendar").evoCalendar({
    'eventDisplayDefault': false,
    'sidebarDisplayDefault': false,
    'format': 'MM dd, yyyy',
    'todayHighlight': false,
    'firstDayOfWeek': 1,
    'eventListToggler': false
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

function deleteOrder(id) {
    fetch(`/admin_dashboard/index.php/orders/edit/${id}`, {
        method: 'DELETE',
    }).then(response => {
        if (response.status) {
            location.reload();
        }
    });
}

let ordersThisMonth = new Orders();

function generateView(newDate = null) {
    document.getElementById('carMapping').innerHTML = '';
    document.getElementById('availableCarMapping').innerHTML = '';
    if (newDate != null) {
        var active_date = '01 ' + newDate + new Date(document.querySelector("#calendar > div.calendar-sidebar > div.calendar-year > p").innerHTML).getFullYear();
    } else {
        var active_date = $('#calendar').evoCalendar('getActiveDate');
    }
    var active_date = new Date(active_date);
    ordersThisMonth.getOrders(active_date.getMonth(), active_date.getFullYear()).then((data) => {
        document.getElementById('ordersThisMonth').innerHTML = data.length;
        var carIds = [];
        data.forEach(order => {
            if (!carIds.includes(order.car_id)) carIds.push(order.car_id);
        });

        if (carIds.length == 0) {
            document.getElementById('carMapping').innerHTML = '<p>No data</p>';
        } else {
            carIds.forEach(carId => {
                ordersThisMonth.getCar(carId).then((car) => {
                    let newDiv = document.getElementById('carMapping').appendChild(document.createElement('div'));
                    newDiv.innerHTML = `
                        <i class="fas fa-circle" style="color: ${generateUniqueColor(carId)}"></i>
                        <span>${car[0].name}</span>
                    `;
                });
            });
        }
    });

    ordersThisMonth.availableCars().then((data) => {
        document.getElementById('availableCars').innerHTML = data.length;
        var carIds = [];
        data.forEach(order => {
            if (!carIds.includes(order.id)) carIds.push(order.id);
        });

        if (carIds.length == 0) {
            document.getElementById('availableCarMapping').innerHTML = '<p>No data</p>';
        } else {
            carIds.forEach(carId => {
                ordersThisMonth.getCar(carId).then((car) => {
                    let newDiv = document.getElementById('availableCarMapping').appendChild(document.createElement('div'));
                    newDiv.innerHTML = `
                        <i class="fas fa-circle" style="color: ${generateUniqueColor(carId)}"></i>
                        <span>${car[0].name}</span>
                    `;
                });
            });
        }
    });

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
                newDiv.style.backgroundColor = generateUniqueColor(order.carId);
                newDiv.style.boxShadow = '0 0 10px ' + generateUniqueColor(order.carId);
                newDiv.style.width = '100%';
                newDiv.id = "order-" + order.orderId;

                if (startDate.getTime() == date.getTime()) {
                    newDiv.style.borderRadius = '20px 0 0 20px';
                } else if (endDate.getTime() == date.getTime()) {
                    newDiv.style.borderRadius = '0 20px 20px 0';
                } else {
                    newDiv.style.borderRadius = '0';
                }
				
				newDiv.setAttribute('data-toggle', 'popover');
				newDiv.setAttribute('title', 'Order Details');
				newDiv.setAttribute('data-content', `
					<div class="row">
						<div class="col-lg-12">
							<p><b>Car:</b> ${order.car}</p>
							<p><b>Customer:</b> ${order.renterName}</p>
							<p><b>Date:</b> ${order.startDate} - ${order.endDate}</p>
						</div>
					</div>
					<div class="row">
						<div class="col">
							<a href="/admin_dashboard/index.php/orders/edit/${order.orderId}" class="btn btn-primary btn-sm"> <i class="fas fa-edit"></i> Edit</a>
						</div>
						<div class="col">
							<a href="/admin_dashboard/index.php/orders/delete/${order.orderId}" class="btn btn-danger btn-sm"> <i class="fas fa-trash"></i> Delete</a>
						</div>
					</div>
				`);

            } else {
                /* BACKLOG: Utilize order overlap, perhaps ?
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
               
                if (startDate.getMonth() == date.getMonth() && startDate.getFullYear() == date.getFullYear()) {
                    let newDiv = item.appendChild(document.createElement('div'));
                    newDiv.className = 'day no-event';
                    newDiv.style.margin = '5px 0';
                    newDiv.style.height = '15%';
                }
            }
        });
    });
}
generateView();
$('#calendar').on('selectMonth', function(event, newDate, oldDate) {
    generateView(newDate);
});