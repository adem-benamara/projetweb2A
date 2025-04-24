document.querySelectorAll('.nav-item').forEach(item => {
    item.addEventListener('click', function () {
        // Highlight active contact
        document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
        item.classList.add('active');

        // Load messages for the selected contact
        const contactId = item.getAttribute('data-contact-id');
        loadMessages(contactId);
    });
});

function loadMessages(contactId) {
    // This function can send an AJAX request to fetch messages for the selected contact
    fetch(`/web/controller/controller2.php?contact_id=${contactId}`)
        .then(response => response.json())
        .then(messages => {
            // Update the message table with the fetched messages
            const tbody = document.querySelector('table tbody');
            tbody.innerHTML = '';  // Clear current messages

            messages.forEach(message => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${message.message_id}</td>
                    <td>${message.sender_username}</td>
                    <td>${message.receiver_username}</td>
                    <td id="msg-content-${message.message_id}">${message.content}</td>
                    <td>${message.timestamp}</td>
                    <td class="actions">
                        <button onclick="editMessage(${message.message_id})">✏️</button>
                        <button onclick="deleteMessage(${message.message_id})">🗑️</button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        });
}

// Function to handle editing a message
function editMessage(msgId) {
    const newContent = prompt('Edit message:', document.getElementById(`msg-content-${msgId}`).innerText);
    
    if (newContent) {
        fetch('/web/controller/update_message.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `message_id=${msgId}&content=${encodeURIComponent(newContent)}`
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if (data.status === 'success') {
                document.getElementById(`msg-content-${msgId}`).innerText = newContent;
            }
        })
        .catch(error => {
            alert('Network error while updating message.');
            console.error('Edit error:', error);
        });
    }
}

// Function to handle deleting a message
function deleteMessage(msgId) {
    if (!confirm('Are you sure you want to delete this message?')) return;

    fetch('/web/controller/delete_message.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `message_id=${msgId}`
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.status === 'success') {
            document.getElementById(`msg-row-${msgId}`).remove();
        }
    })
    .catch(error => {
        alert('Network error while deleting message.');
        console.error('Delete error:', error);
    });
}
