let CURRENT_HIRING_SERVICE_ID = null;
let CURRENT_HIRING_OWNER_ID = null;

document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('hirings-toggle-btn');
    const closeBtn = document.getElementById('hirings-close-btn');
    const hiringModal = document.getElementById('hirings-modal');

    toggleBtn.addEventListener('click', () => {
        hiringModal.classList.toggle('hidden');
    });
    
    closeBtn.addEventListener('click', () => {
        hiringModal.classList.toggle('hidden');
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
          hiringModal.classList.add('hidden');
        }
    });

    const openHiring = localStorage.getItem('openHiring');
    if (openHiring === 'true') {
        document.getElementById('hirings-modal')?.classList.remove('hidden');
        localStorage.removeItem('openHiring');
    }
});

function createHiring(serviceId, clientId, ownerId) {
    CURRENT_HIRING_SERVICE_ID = serviceId;
    
    fetch('/actions/action_create_hiring.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            serviceId: serviceId,
            client_id: clientId,
            owner_id: ownerId
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Hiring result:', data);
    })
    .catch(error => {
        console.error('Erro ao contratar:', error);
    });
}

function highlightSelectedHiring(id) {
    document.querySelectorAll('.hiring-service-group').forEach(item => {
        item.classList.remove('active');
    });

    const selectedItem = document.querySelector(
        `.hiring-service-group[data-id="${id}"]`
    );

    if (selectedItem) {
        selectedItem.classList.add('active');
    }
}

function updateHiringStatus(hiringId, newStatus) {
    const formData = new FormData();
    formData.append('id', hiringId);
    formData.append('status', newStatus);

    fetch('/actions/action_update_hiring_status.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error updating status');
        }
        return response.json();
    })
    .then(data => {
        localStorage.setItem('openHiring', 'true');
        location.reload();
    })
    .catch(error => {
        console.error('Error requesting:', error);
        alert('Error accepting status change.');
    });
}

function drawServiceClients(element, serviceId, serviceTitle) {
    CURRENT_HIRING_SERVICE_ID = serviceId;

    const title = element.getAttribute('data-title');
    const clientsJSON = element.getAttribute('data-clients');

    let clients;
    try {
        clients = JSON.parse(clientsJSON);
    } catch (e) {
        console.error("Erro ao parsear clientes", e);
        return;
    }

    document.getElementById('hirings-freelancer-name').textContent = `${clients.length} requests`;
    const hiringTitle = document.getElementById('hirings-service-title');
    hiringTitle.textContent = title;
    hiringTitle.style.cursor = 'pointer';
    hiringTitle.onclick = () => {
        window.location.href = `/pages/service.php?id=${serviceId}`;
    };

    const body = document.getElementById('hirings-body');
    body.innerHTML = clients.map(client => `
        <div class="client-hiring-card">
            <span class="client-username" id="client-username">${client.client_name}</span>
            <div class="hiring-actions">
                ${client.status === 'Pending' ? `
                    <button onclick="updateHiringStatus(${client.hiring_id}, 'Accepted');
                        sendStatusMessage(event, 'Accepted', ${client.owner_id}, ${client.client_id}, '${serviceTitle}')" 
                        class="accept-btn">Accept
                    </button>
                    <button onclick="updateHiringStatus(${client.hiring_id}, 'Rejected');
                        sendStatusMessage(event, 'Rejected', ${client.owner_id}, ${client.client_id}, '${serviceTitle}')"
                        class="reject-btn">Reject
                    </button>
                ` : client.status === 'Accepted' ? `
                    <button onclick="updateHiringStatus(${client.hiring_id}, 'Completed');
                        sendStatusMessage(event, 'Completed', ${client.owner_id}, ${client.client_id}, '${serviceTitle}')"
                        class="finish-btn">Finish
                    </button>
                ` : `<span class="status-label">${client.status}</span>`}
            </div>
        </div>
    `).join('');
    
    const usernameSpan = document.getElementById('client-username');
    usernameSpan.style.cursor = 'pointer';
    usernameSpan.onclick = () => {
        console.log('Go to user profile TODO');
        // window.location.href = `/pages/profile.php?id=${data.receiver_id}`;
    };
}

function drawOwnHiringRequest(ownerUsername, ownerId, hirings, serviceId, serviceTitle) {
    CURRENT_HIRING_SERVICE_ID = serviceId;
    CURRENT_HIRING_OWNER_ID = ownerId;

    const body = document.getElementById("hirings-body");
    const headerName = document.getElementById("hirings-freelancer-name");
    const headerTitle = document.getElementById("hirings-service-title");

    headerName.textContent = ownerUsername;
    headerName.style.cursor = 'pointer';
    headerName.onclick = () => {
        // window.location.href = `/pages/profile.php?id=${ownerId}`;
    };
    headerTitle.textContent = serviceTitle;
    headerTitle.style.cursor = 'pointer';
    headerTitle.onclick = () => {
        window.location.href = `/pages/service.php?id=${serviceId}`;
    };

    let html = "";

    hirings.forEach(hiring => {
        const status = hiring.status;
        const showCancel = status === "Pending" || status === "Accepted";
        const statusBadge = `<span class="status-badge status-${status.toLowerCase()}">${status}</span>`;

        html += `
            <div class="client-hiring-card">
                <div class="card-header">
                    ${statusBadge}
                </div>
                ${showCancel ? `
                    <div class="hiring-actions">
                        <button onclick="updateHiringStatus(${hiring.id}, 'Cancelled');
                            sendStatusMessage(event, 'Cancelled', ${hiring.client_id}, ${hiring.owner_id}, '${serviceTitle}')"
                            class="cancel-btn">Cancel
                        </button>
                    </div>` : ""}
            </div>
        `;
    });

    body.innerHTML = html;
}

function sendStatusMessage(event, status, senderId, receiverId, serviceTitle) {
    event.preventDefault();
    
    const message = `The hiring '${serviceTitle}' has been updated to ${status}!`;
    const subMessage = `Click to see details`;

    const ids = [senderId, receiverId].sort((a, b) => a - b);
    const conversationId = `${ids[0]}_${ids[1]}`;

    const formData = new FormData();
    formData.append('conversation_id', conversationId);
    formData.append('service_id', CURRENT_HIRING_SERVICE_ID);
    formData.append('sender_id', senderId);
    formData.append('receiver_id', receiverId);
    formData.append('message', message);
    formData.append('sub_message', subMessage);

    fetch('/actions/action_send_message.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            console.error('Error sending message:', data.error || 'Unknown error');
        } else {
        }
    })
    .catch(err => {
        console.error('Error sending:', err);
    });
}