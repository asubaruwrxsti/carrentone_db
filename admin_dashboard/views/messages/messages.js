class Messages {
    constructor() {}

    async fetchMessages() {
        const response = await fetch(`/admin_dashboard/index.php/api/messages/`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        const data = await response.json();
        return data;
    }

    async fetchMessage(id) {
        const response = await fetch(`/admin_dashboard/index.php/api/messages/${id}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        const data = await response.json();
        return data;
    }

    async isPreviousCustomer(sender) {
        const response = await fetch(`/admin_dashboard/index.php/api/customers/`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            },
        });
        const result = await response.json();
    
        for (const key of Object.keys(result)) {
            if (result[key].firstname === sender.name && result[key].lastname === sender.lastname) {
                return [{
                    id: result[key].id,
                    name: result[key].firstname,
                    lastname: result[key].lastname,
                    email: result[key].email,
                    phone: result[key].phone,
                }];
            }
        }
        return false;
    }

    async deleteMessage(id) {
        const response = await fetch(`/admin_dashboard/index.php/api/messages/edit/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        const data = await response.json();
        return data;
    }
}