let CURRENT_CONVERSATION_ID = null;
let CURRENT_SERVICE_ID = null;
let CURRENT_USER_ID = null;
let CURRENT_RECEIVER_ID = null;

document.addEventListener('DOMContentLoaded', () => {
    checkUnreadMessages();

    const toggleBtn = document.getElementById('chat-toggle-btn');
    const closeBtn = document.getElementById('chat-close-btn');
    const modal = document.getElementById('chat-modal');

    toggleBtn.addEventListener('click', () => {
        modal.classList.toggle('hidden');
    });
    
    closeBtn.addEventListener('click', () => {
        modal.classList.toggle('hidden');
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
          modal.classList.add('hidden');
        }
    });

    document.getElementById('chat-toggle-btn').addEventListener('click', () => {
        document.getElementById('chat-modal').classList.remove('hidden');
        checkUnreadMessages();
    });

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
            CURRENT_CONVERSATION_ID = conversationId;
            CURRENT_SERVICE_ID = serviceId;
            CURRENT_USER_ID = userId;
            CURRENT_RECEIVER_ID = data.receiver_id;

            usernameSpan.textContent = data.receiver_username;
            usernameSpan.style.cursor = 'pointer';
            usernameSpan.onclick = () => {
                // window.location.href = `/pages/profile.php?id=${data.receiver_id}`;
                console.log('Go to user profile TODO');
            };

            serviceTitleSpan.textContent = `Service #${data.service_id}`;
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
                const isUser = currentSenderId === userId;

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
                msgText.textContent = msg.message;
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


            chatBody.scrollTop = chatBody.scrollHeight;
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
    const message = input.value.trim();
    const chatBody = document.getElementById('chat-body');

    if (!message) return;

    input.value = '';

    const formData = new FormData();
    formData.append('conversation_id', CURRENT_CONVERSATION_ID);
    formData.append('service_id', CURRENT_SERVICE_ID);
    formData.append('sender_id', CURRENT_USER_ID);
    formData.append('receiver_id', CURRENT_RECEIVER_ID);
    formData.append('message', message);

    fetch('/actions/action_send_message.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            console.error('Error sending message:', data.error || 'Unknown error');
        } else {
            drawMessages(CURRENT_CONVERSATION_ID, CURRENT_SERVICE_ID, CURRENT_USER_ID);
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

function startConversation(serviceId, user1Id, user2Id) {
    const formData = new FormData();
    formData.append('service_id', serviceId);
    formData.append('user1_id', user1Id);
    formData.append('user2_id', user2Id);

    fetch('/actions/action_start_conversation.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            localStorage.setItem('openChat', JSON.stringify({
                conversation_id: data.conversation_id,
                service_id: data.service_id,
                user_id: user1Id
            }));
            location.reload();
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(err => {
        console.error('Failed to start conversation:', err);
    });
}

function checkUnreadMessages() {
    fetch('/actions/action_get_unread_messages.php')
        .then(res => res.json())
        .then(data => {
            const badge = document.getElementById('chat-badge');
            const unreadMap = {};

            data.forEach(entry => {
                unreadMap[entry.conversation_id] = entry.unread_count;
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
                const convBadge = document.getElementById(`unread-badge-${conversationId}`);

                if (unreadMap[conversationId]) {
                    convBadge.textContent = unreadMap[conversationId];
                    convBadge.classList.remove('hidden');
                } else {
                    convBadge.classList.add('hidden');
                }
            });
        });
}
