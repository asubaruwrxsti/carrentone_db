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
}