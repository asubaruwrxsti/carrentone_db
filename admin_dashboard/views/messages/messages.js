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
                return true;
            }
        }
        return false;
    }    
}