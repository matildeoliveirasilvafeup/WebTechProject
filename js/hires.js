let CURRENT_HIRE_SERVICE_ID = null;
let CURRENT_HIRE_OWNER_ID = null;

document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('hires-toggle-btn');
    const closeBtn = document.getElementById('hires-close-btn');
    const hireModal = document.getElementById('hires-modal');

    toggleBtn.addEventListener('click', () => {
        hireModal.classList.toggle('hidden');
    });
    
    closeBtn.addEventListener('click', () => {
        hireModal.classList.toggle('hidden');
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
          hireModal.classList.add('hidden');
        }
    });
});

function acceptHire(hiringId) {
    // TODO: AJAX to backend
    alert(`Accepted hire #${hiringId}`);
}

function cancelHire(hiringId) {
    // TODO: AJAX to backend
    alert(`Cancelled hire #${hiringId}`);
}

function drawServiceClients(element) {
    const title = element.getAttribute('data-title');
    const clientsJSON = element.getAttribute('data-clients');

    let clients;
    try {
        clients = JSON.parse(clientsJSON);
    } catch (e) {
        console.error("Erro ao parsear clientes", e);
        return;
    }

    document.getElementById('hires-service-title').textContent = title;
    document.getElementById('hires-freelancer-name').textContent = `${clients.length} requests`;

    const body = document.getElementById('hires-body');
    body.innerHTML = clients.map(client => `
        <div class="client-hiring-card">
            <span class="client-username" id="client-username">${client.client_name}</span>
            <div class="hiring-actions">
                ${client.status === 'Pending' ? `
                    <button onclick="acceptHire(${client.hiring_id})" class="accept-btn">Accept</button>
                    <button onclick="rejectHire(${client.hiring_id})" class="reject-btn">Reject</button>
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

function drawOwnHiringRequest(ownerUsername, ownerId, hirings, serviceTitle) {
    const body = document.getElementById("hires-body");
    const headerName = document.getElementById("hires-freelancer-name");
    const headerTitle = document.getElementById("hires-service-title");

    console.log(ownerUsername);

    headerName.textContent = ownerUsername;
    headerName.style.cursor = 'pointer';
    headerName.onclick = () => {
        // window.location.href = `/pages/profile.php?id=${ownerId}`;
    };
    headerTitle.textContent = serviceTitle;

    let html = "";

    hirings.forEach(hire => {
        const status = hire.status;
        const showCancel = status === "Pending" || status === "Accepted";
        const statusBadge = `<span class="status-badge status-${status.toLowerCase()}">${status}</span>`;

        html += `
            <div class="client-hiring-card">
                <div class="card-header">
                    ${statusBadge}
                </div>
                ${showCancel ? `
                    <div class="hiring-actions">
                        <button onclick="cancelHire(${hire.id})" class="cancel-btn">Cancel</button>
                    </div>` : ""}
            </div>
        `;
    });

    body.innerHTML = html;
}

