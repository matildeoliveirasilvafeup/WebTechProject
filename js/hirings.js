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
    if (openHiring) {
        try {
            hiringModal.classList.remove('hidden');
        } catch (e) {
            console.error('Erro ao reabrir modal:', e);
        } finally {
            localStorage.removeItem('openHiring');
        }
    }
});

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
        localStorage.setItem('openHiring', JSON.stringify({}));
        location.reload();
    })
    .catch(error => {
        console.error('Error requesting:', error);
        alert('Error accepting status change.');
    });
}

function drawServiceClients(element, serviceId) {
    CURRENT_SERVICE_ELEMENT = element;
    CURRENT_SERVICE_ID = serviceId;

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
                    <button onclick="updateHiringStatus(${client.hiring_id}, 'Accepted')" class="accept-btn">Accept</button>
                    <button onclick="updateHiringStatus(${client.hiring_id}, 'Rejected')" class="reject-btn">Reject</button>
                ` : client.status === 'Accepted' ? `
                    <button onclick="updateHiringStatus(${client.hiring_id}, 'Completed')" class="finish-btn">Finish</button>
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
    CURRENT_HIRINGS = hirings;
    CURRENT_HIRING_OWNER_USERNAME = ownerUsername;
    CURRENT_HIRING_OWNER_ID = ownerId;
    CURRENT_HIRING_SERVICE_ID = serviceId;
    CURRENT_HIRING_SERVICE_TITLE = serviceTitle;

    const body = document.getElementById("hirings-body");
    const headerName = document.getElementById("hirings-freelancer-name");
    const headerTitle = document.getElementById("hirings-service-title");

    console.log(ownerUsername);

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
                        <button onclick="updateHiringStatus(${hiring.id}, 'Cancelled')" class="cancel-btn">Cancel</button>
                    </div>` : ""}
            </div>
        `;
    });

    body.innerHTML = html;
}