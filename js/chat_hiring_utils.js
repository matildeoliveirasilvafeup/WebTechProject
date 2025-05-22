window.startConversation = startConversation;

export function startConversation(serviceId, user1Id, user2Id, flag) {
    if (!serviceId || !user1Id || !user2Id) {
        window.location.href = `/pages/login.php`;
    }

    if (user1Id == user2Id) {
        console.warn('Cannot start a conversation with yourself.');
        return Promise.resolve();
    }

    const formData = new FormData();
    formData.append('service_id', serviceId);
    formData.append('user1_id', user1Id);
    formData.append('user2_id', user2Id);

    return fetch('/actions/action_start_conversation.php', {
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
            if (flag == 'true') {
                location.reload();
            }
        } else {
            throw new Error(data.error || 'Unknown error');
        }
    });
}

export function createHiring(serviceId, clientId, ownerId) {
    return fetch('/actions/action_create_hiring.php', {
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
        if (!data.success) {
            throw new Error(data.message || 'Error creating hiring.');
        }
    })
    .catch(error => {
        console.error('Error hiring:', error);
        throw error;
    });
}

export function sendStatusMessage(status, senderId, receiverId, serviceId, serviceTitle) {
    
    let message = null;
    if (status === 'Reopened') {
        status = 'Pending';
        message = `The hiring '${serviceTitle}' has been reopened!`;
    } else {
        message = `The hiring '${serviceTitle}' has been updated to ${status}!`;
    }
    const subMessage = `Click to see details`;

    const ids = [senderId, receiverId].sort((a, b) => a - b);
    const conversationId = `${ids[0]}_${ids[1]}`;

    const formData = new FormData();
    formData.append('conversation_id', conversationId);
    formData.append('service_id', serviceId);
    formData.append('sender_id', senderId);
    formData.append('receiver_id', receiverId);
    formData.append('message', message);
    formData.append('sub_message', subMessage);

    setTimeout(() => {}, 2000);

    return fetch('/actions/action_send_message.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            throw new Error(data.error || 'Unknown error sending message.');
        }
    })
    .catch(err => {
        console.error('Error sending:', err);
        throw err;
    });
}
