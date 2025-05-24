import { formatDateTimeWithoutSeconds } from './chat_hiring_utils.js';

window.ChatState = {
    CURRENT_CONVERSATION_ID: null,
    CURRENT_SERVICE_ID: null,
    CURRENT_USER_ID: null,
    CURRENT_RECEIVER_ID: null
};

document.addEventListener('DOMContentLoaded', () => {
    checkUnreadMessages();

    window.drawMessages = drawMessages;
    window.sendMessage = sendMessage;

    const toggleBtn = document.getElementById('chat-toggle-btn');
    const closeBtn = document.getElementById('chat-close-btn');
    const modal = document.getElementById('chat-modal');

    if (toggleBtn && modal) {
        toggleBtn.addEventListener('click', () => {
            modal.classList.toggle('hidden');
            checkUnreadMessages();
        });
    }
    
    if (closeBtn && modal) {
        closeBtn.addEventListener('click', () => {
            modal.classList.add('hidden');
        });
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
          modal.classList.add('hidden');
        }
    });

    document.querySelectorAll('.chat-item').forEach(item => {
        item.addEventListener('click', () => {
            const conversationId = item.dataset.conversationId;
            const serviceId = item.dataset.serviceId;
            const userId = item.dataset.userId;

            drawMessages(conversationId, serviceId, userId);
            highlightSelectedChat(conversationId, serviceId);
        });
    });

    const fileInput = document.getElementById('chat-file');
    const fileDisplay = document.getElementById('file-name-display');

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            if (this.files.length > 0) {
                const fileName = this.files[0].name;

                fileDisplay.innerHTML = `
                    <div class="file-preview-chat">
                    <i class="fa-solid fa-file"></i> ${fileName}
                    <button type="button" id="cancel-file-btn" title="Remove file">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                    </div>
                `;

                document.getElementById('cancel-file-btn').addEventListener('click', () => {
                    fileInput.value = '';
                    fileDisplay.innerHTML = '';
                });
            } else {
                fileDisplay.innerHTML = '';
            }
        });
    }
    
    const openChat = localStorage.getItem('openChat');
    if (openChat) {
        try {
            const { conversation_id, service_id, user_id } = JSON.parse(openChat);
            modal.classList.remove('hidden');
            drawMessages(conversation_id, service_id, user_id);

            requestAnimationFrame(() => {
                highlightSelectedChat(conversation_id, service_id);
            });
        } catch (e) {
            console.error('Erro ao carregar chat após refresh:', e);
        } finally {
            localStorage.removeItem('openChat');
        }
    }

    const openHiring = localStorage.getItem('openHiring');
    if (openHiring === 'true') {
        document.getElementById('hirings-modal')?.classList.remove('hidden');
        localStorage.removeItem('openHiring');
    } 

    const customOrderBtn = document.getElementById('chat-custom-order');
    const customOrderModal = document.getElementById('custom-order-modal');
    const hiringsList = document.getElementById('hirings-list');
    const customOrderCloseBtn = document.getElementById('custom-order-close-btn');

    customOrderBtn?.addEventListener('click', () => {
        customOrderModal.classList.toggle('hidden');

        const serviceId = window.ChatState.CURRENT_SERVICE_ID;
        const userId = window.ChatState.CURRENT_USER_ID;
        const receiverId = window.ChatState.CURRENT_RECEIVER_ID;

        if (!serviceId || !userId || !receiverId) {
            hiringsList.innerHTML = "<p>No service or user selected.</p>";
            return;
        }

        hiringsList.innerHTML = "<p>Loading...</p>";

        fetch(`/actions/action_get_hirings_by_service.php?service_id=${serviceId}&user_id1=${userId}&user_id2=${receiverId}`)
            .then(res => res.json())
            .then(data => {
                if (!Array.isArray(data) || data.length === 0) {
                    hiringsList.innerHTML = "<p>No hirings found.</p>";
                    return;
                }

                hiringsList.innerHTML = '';

                data.forEach(hiring => {
                    const div = document.createElement('div');
                    div.classList.add('client-hiring-card');

                    div.innerHTML = `
                        <div class="card-header">
                            <span class="status-badge status-${hiring.status.toLowerCase()}">${hiring.status}</span>
                        </div>
                        <span class="createdAt-badge">${formatDateTimeWithoutSeconds(hiring.createdAt)}</span>
                    `;

                    div.style.cursor = 'pointer';
                    div.addEventListener('click', () => {
                        console.log("Hiring service clicked");
                        window.location.href = `/pages/custom_offer.php?hiring_id=${hiring.id}&user_id1=${userId}&user_id2=${receiverId}&service_id=${serviceId}`;
                    });

                    hiringsList.appendChild(div);
                });
            })
            .catch(err => {
                hiringsList.innerHTML = "<p>Error loading hirings.</p>";
                console.error(err);
            });
    });

    customOrderCloseBtn?.addEventListener('click', () => {
        customOrderModal.classList.add('hidden');
    });
});

function drawMessages(conversationId, serviceId, userId) {
    const chatMain = document.getElementById('chat-main');
    const chatBody = document.getElementById('chat-body');
    const usernameSpan = document.getElementById('chat-username');
    const serviceTitleSpan = document.getElementById('chat-service-title');

    chatMain.classList.remove('hidden');

    fetch(`/actions/action_get_messages.php?conversation_id=${conversationId}&service_id=${serviceId}&user_id=${userId}`)
        .then(res => res.json())
        .then(data => {
            
            window.ChatState.CURRENT_CONVERSATION_ID = conversationId;
            window.ChatState.CURRENT_SERVICE_ID = serviceId;
            window.ChatState.CURRENT_USER_ID = userId;
            window.ChatState.CURRENT_RECEIVER_ID = data.receiver_id;

            usernameSpan.textContent = data.receiver_username;
            usernameSpan.style.cursor = 'pointer';
            usernameSpan.onclick = () => {
                window.location.href = `/pages/profile.php?user=${data.receiver_username}`;
            };

            serviceTitleSpan.textContent = `═─ Go to service page ─═`;
            serviceTitleSpan.style.cursor = 'pointer';
            serviceTitleSpan.onclick = () => {
                window.location.href = `/pages/service.php?id=${serviceId}`;
            };

            chatBody.innerHTML = '';

            if (!data.messages || data.messages.length === 0) {
                chatBody.innerHTML = '<p><em>No messages yet.</em></p>';
                return;
            }

            let lastSenderId = null;
            let lastTime = null;
            let messageGroup = null;

            data.messages.forEach((msg, i) => {
                const currentSenderId = msg.sender_id;
                const currentIsoUTC = msg.message_created_at.replace(' ', 'T') + 'Z';
                const currentTimeUTC = new Date(currentIsoUTC);
                const isUser = currentSenderId === parseInt(userId, 10);

                if (
                    lastSenderId !== currentSenderId ||
                    !lastTime ||
                    (currentTimeUTC - lastTime) / (1000 * 60) >= 3
                ) {
                    if (messageGroup) {
                        chatBody.appendChild(messageGroup);
                    }

                    messageGroup = document.createElement('div');
                    messageGroup.classList.add('message-group', isUser ? 'message-out' : 'message-in');
                }

                const msgText = document.createElement('div');
                msgText.classList.add('msg-text');

                if (msg.message && msg.sub_message) {
                    const bubble = document.createElement('div');
                    bubble.classList.add('message-with-sub');

                    const mainText = document.createElement('p');
                    if (msg.hiring_id) {
                        mainText.textContent = msg.message.replace(/\b\w+!$/, '').trim();
                    } else {
                        mainText.textContent = msg.message;
                    }
                    bubble.appendChild(mainText);

                    const subText = document.createElement('p');
                    subText.textContent = msg.sub_message;
                    subText.classList.add('sub-message-bubble');

                    if (msg.status_class) {
                        bubble.classList.add(`status-${msg.status_class.toLowerCase()}`);
                        subText.classList.add(`status-${msg.status_class.toLowerCase()}`);
                    }
                    
                    subText.style.cursor = 'pointer';
                    subText.onclick = () => {
                        if (msg.hiring_id) {
                            window.location.href = `/pages/custom_offer.php?hiring_id=${msg.hiring_id}&user_id1=${userId}&user_id2=${window.ChatState.CURRENT_RECEIVER_ID}&service_id=${serviceId}`;
                        } else {
                            localStorage.setItem('openHiring', 'true');
                            location.reload();
                        }
                    };

                    bubble.appendChild(subText);
                    msgText.appendChild(bubble);
                } else if (msg.message) {

                    const textEl = document.createElement('p');
                    textEl.textContent = msg.message;
                    msgText.appendChild(textEl);
                }

                if (msg.file) {
                    const fileUrl = `/uploads/chat/${msg.file}`;
                    const fileExt = msg.file.split('.').pop().toLowerCase();

                    if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExt)) {
                        const img = document.createElement('img');
                        img.src = fileUrl;
                        img.alt = 'image';
                        img.classList.add('chat-image');
                        img.onload = () => scrollToBottom();
                        msgText.appendChild(img);
                    } else {
                        const fileLink = document.createElement('a');
                        fileLink.href = fileUrl;
                        fileLink.target = '_blank';
                        fileLink.innerHTML = '<i class="fa-solid fa-file"></i> ' + msg.file;
                        msgText.appendChild(fileLink);
                    }
                }

                messageGroup.appendChild(msgText);

                const nextMsg = data.messages[i + 1];
                let nextTimeUTC = null;
                if (nextMsg) {
                    const nextIsoUTC = nextMsg.message_created_at.replace(' ', 'T') + 'Z';
                    nextTimeUTC = new Date(nextIsoUTC);
                }

                const timeDiff = nextTimeUTC ? (nextTimeUTC - currentTimeUTC) / (1000 * 60) : null;

                if (!nextMsg || nextMsg.sender_id !== currentSenderId || timeDiff >= 3) {
                    const msgTime = document.createElement('div');
                    msgTime.classList.add('msg-time');
                    msgTime.textContent = currentTimeUTC.toLocaleTimeString('pt-PT', {
                        hour: '2-digit',
                        minute: '2-digit',
                        timeZone: 'Europe/Lisbon'
                    });
                    messageGroup.appendChild(msgTime);
                }

                lastSenderId = currentSenderId;
                lastTime = currentTimeUTC;
            });

            if (messageGroup) {
                chatBody.appendChild(messageGroup);
            }
            
            scrollToBottom();

        })
        .catch(error => {
            console.error("Failed to load messages:", error);
            chatBody.innerHTML = '<p class="error"><em>Error loading conversation.</em></p>';
        });

    fetch('/actions/action_mark_as_read.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `conversation_id=${conversationId}&service_id=${serviceId}`
    }).then(() => {
        checkUnreadMessages();
    });
}

function sendMessage(event) {
    event.preventDefault();

    const input = document.getElementById('chat-input');
    const fileInput = document.getElementById('chat-file');
    const fileDisplay = document.getElementById('file-name-display');
    const message = input.value.trim();

    if (!message && (!fileInput || fileInput.files.length === 0)) return;

    const formData = new FormData();
    formData.append('conversation_id', window.ChatState.CURRENT_CONVERSATION_ID);
    formData.append('hiring_id', null);
    formData.append('service_id', window.ChatState.CURRENT_SERVICE_ID);
    formData.append('sender_id', window.ChatState.CURRENT_USER_ID);
    formData.append('receiver_id', window.ChatState.CURRENT_RECEIVER_ID);
    formData.append('sub_message', '');
    
    if (fileInput && fileInput.files.length > 0) {
        formData.append('file', fileInput.files[0]);
        formData.append('message', message ? message : '');
    } else if (message) {
        formData.append('message', message);
    }

    input.value = '';
    if (fileInput) fileInput.value = '';

    fetch('/actions/action_send_message.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            console.error('Error sending message:', data.error || 'Unknown error');
        } else {
            drawMessages(window.ChatState.CURRENT_CONVERSATION_ID, window.ChatState.CURRENT_SERVICE_ID, window.ChatState.CURRENT_USER_ID);
            fileInput.value = '';
            fileDisplay.innerHTML = '';
        }
    })
    .catch(err => {
        console.error('Error sending:', err);
    });
}

function highlightSelectedChat(conversationId, serviceId) {
    document.querySelectorAll('.chat-item').forEach(item => {
        item.classList.remove('active');
    });

    const selectedItem = document.querySelector(
        `.chat-item[data-conversation-id="${conversationId}"][data-service-id="${serviceId}"]`
    );

    if (selectedItem) {
        selectedItem.classList.add('active');
    }
}

function checkUnreadMessages() {
    fetch('/actions/action_get_unread_messages.php')
        .then(res => res.json())
        .then(data => {
            const badge = document.getElementById('chat-badge');
            const unreadMap = {};

            data.forEach(entry => {
                const key = `${entry.conversation_id}-${entry.service_id}`;
                unreadMap[key] = entry.unread_count;
            });

            const total = Object.keys(unreadMap).length;
            if (total > 0) {
                badge.textContent = total;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }

            document.querySelectorAll('.chat-item').forEach(item => {
                const conversationId = item.dataset.conversationId;
                const serviceId = item.dataset.serviceId;
                const key = `${conversationId}-${serviceId}`;
                
                const badgeId = `unread-badge-${conversationId}-${serviceId}`;
                const convBadge = document.getElementById(badgeId);

                if (unreadMap[key]) {
                    convBadge.textContent = unreadMap[key];
                    convBadge.classList.remove('hidden');
                } else if (convBadge) {
                    convBadge.classList.add('hidden');
                }
            });
        });
}

function scrollToBottom() {
    const chatBody = document.getElementById('chat-body');
    if (chatBody) {
        chatBody.scrollTop = chatBody.scrollHeight;
    }
}