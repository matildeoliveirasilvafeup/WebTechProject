import { sendStatusMessage, formatDateTimeWithoutSeconds } from './chat_hiring_utils.js';

document.addEventListener('DOMContentLoaded', () => {

    window.drawOwnHiringRequest = drawOwnHiringRequest;
    window.drawServiceClients = drawServiceClients;
    window.highlightSelectedHiring = highlightSelectedHiring;
    window.updateHiringStatus = updateHiringStatus;
    window.sendStatusMessage = sendStatusMessage;

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

    function updateHiringStatus(hiringId, newStatus, user1Id, user2Id, serviceId, serviceTitle) {
        const formData = new FormData();
        formData.append('id', hiringId);
        formData.append('status', newStatus);

        fetch('/actions/action_update_hiring_status.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error updating hiring status');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showToast(data.message || `Status updated to ${newStatus}`, 'success');
                sendStatusMessage(newStatus, user1Id, user2Id, serviceId, serviceTitle);
                // if (newStatus === 'Accepted' || newStatus === 'Rejected'|| newStatus === 'Completed') {
                // } else if (newStatus === 'Closed' || newStatus === 'Cancelled' || newStatus === 'Pending') {
                //     sendStatusMessage(newStatus, user2Id, user1Id, serviceId, serviceTitle);
                // }

            } else {
                showToast(data.message || 'Error updating hiring status', 'error');
            }
            localStorage.setItem('openHiring', 'true');
            setTimeout(() => location.reload(), 1500);
        })
        .catch(error => {
            console.error('Erro:', error);
            showToast(error.message || 'Unexpected error', 'error');
        });
    }

    function drawServiceClients(element, serviceId, serviceTitle) {
        
        const title = element.getAttribute('data-title');
        const clientsJSON = element.getAttribute('data-clients');

        let clients;
        try {
            clients = JSON.parse(clientsJSON);
        } catch (e) {
            console.error("Error parsing clients", e);
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
                <span class="createdAt-badge ">${formatDateTimeWithoutSeconds(client.created_at)}</span>
                <div class="hiring-actions">
                    ${client.status === 'Pending' ? `
                        <button onclick="updateHiringStatus(${client.hiring_id}, 'Accepted', ${client.owner_id}, ${client.client_id}, ${serviceId}, '${serviceTitle}')"
                            class="accept-btn">Accept
                        </button>
                        <button onclick="updateHiringStatus(${client.hiring_id}, 'Rejected', ${client.owner_id}, ${client.client_id}, ${serviceId}, '${serviceTitle}')"
                            class="reject-btn">Reject
                        </button>
                        ` : client.status === 'Accepted' ? `
                        <button onclick="updateHiringStatus(${client.hiring_id}, 'Completed', ${client.owner_id}, ${client.client_id}, ${serviceId}, '${serviceTitle}')"
                            class="finish-btn">Finish
                        </button>
                        ` : `<span class="status-label">${client.status}</span>`}
                </div>
            </div>
        `).join('');
    }

    function drawOwnHiringRequest(ownerUsername, hirings, serviceId, serviceTitle) {
        
        const body = document.getElementById("hirings-body");
        const headerName = document.getElementById("hirings-freelancer-name");
        const headerTitle = document.getElementById("hirings-service-title");
        
        headerName.textContent = ownerUsername;
        headerTitle.textContent = serviceTitle;
        headerTitle.style.cursor = 'pointer';
        headerTitle.onclick = () => {
            window.location.href = `/pages/service.php?id=${serviceId}`;
        };
        
        let html = "";
        
        hirings.forEach(hiring => {
            const status = hiring.status;
            const showCancel = status === "Pending" || status === "Accepted" || status === "Completed";
            const statusBadge = `<span class="status-badge status-${status.toLowerCase()}">${status}</span>`;
            const createdAt = hiring.created_at;
            const createdAtBadge = `<span class="createdAt-badge ">${formatDateTimeWithoutSeconds(createdAt)}</span>`;
            
            html += `
            <div class="client-hiring-card">
                <div class="card-header">
                    ${statusBadge}
                </div>
                ${createdAtBadge}
                ${showCancel ? `
                    <div class="hiring-actions">
                        ${status === 'Completed' ? `
                            <button onclick="updateHiringStatus(${hiring.id}, 'Closed', ${hiring.client_id}, ${hiring.owner_id}, ${serviceId}, '${serviceTitle}')" 
                                class="approve-btn">Approve
                            </button>
                            <button onclick="updateHiringStatus(${hiring.id}, 'Pending', ${hiring.client_id}, ${hiring.owner_id}, ${serviceId}, '${serviceTitle}')"
                                class="reopen-btn">Reopen Hiring
                            </button>
                            ` : `
                            <button onclick="updateHiringStatus(${hiring.id}, 'Cancelled', ${hiring.client_id}, ${hiring.owner_id}, ${serviceId}, '${serviceTitle}')"
                                class="cancel-btn">Cancel
                            </button>`}
                    </div>` : ""}
            </div>
            `;
        });

        body.innerHTML = html;
    }

    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.textContent = message;

        document.body.appendChild(toast);

        requestAnimationFrame(() => {
            toast.style.opacity = '1';
        });

        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }


});
