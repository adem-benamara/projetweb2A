
let messagesDB = {
    admin: [],
    user1: [],
    user2: [],
    user3: []
  };
  
  class Message {
    constructor(sender, receiver, content) {
      this.id = Date.now();
      this.sender = sender;
      this.receiver = receiver;
      this.content = content;
      this.timestamp = new Date().toISOString();
      this.isEdited = false;
      this.isDeleted = false;
    }
  }
  
 
  let currentUser = 'user1';
  let currentContact = null;
  
  function selectContact(name) {
    currentContact = name;
    document.getElementById('chatHeader').innerText = name;
    loadChatHistory();
  }
  
  function loadChatHistory() {
    const chatBox = document.getElementById('chatBox');
    chatBox.innerHTML = '';
    
    const history = messagesDB[currentUser].filter(msg => 
      (msg.sender === currentUser && msg.receiver === currentContact) ||
      (msg.receiver === currentUser && msg.sender === currentContact)
    );
  
    history.forEach(msg => {
      if(!msg.isDeleted) {
        chatBox.appendChild(createMessageElement(msg));
      }
    });
    chatBox.scrollTop = chatBox.scrollHeight;
  }
  
  function createMessageElement(msg) {
    const isSent = msg.sender === currentUser;
    const messageElement = document.createElement('div');
    messageElement.className = `message ${isSent ? 'sent' : 'received'}`;
    messageElement.dataset.id = msg.id;
  
    // Message content
    const content = document.createElement('div');
    content.className = 'message-content';
    content.textContent = msg.content;
  
    // Time and status
    const time = document.createElement('div');
    time.className = 'message-time';
    time.textContent = new Date(msg.timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  
    // Status indicator
    const status = document.createElement('div');
    status.className = 'message-status';
    if (msg.isEdited) status.innerHTML = '✏️ Edited';
    if (msg.isDeleted) status.innerHTML = '🗑️ Deleted';
  
    // Action buttons (only for sent messages)
    if (isSent && !msg.isDeleted) {
      const actions = document.createElement('div');
      actions.className = 'message-actions';
      
      const editBtn = document.createElement('button');
      editBtn.className = 'message-action-btn edit';
      editBtn.innerHTML = '✏️';
      editBtn.title = 'Edit';
      editBtn.onclick = (e) => {
        e.stopPropagation();
        editMessage(msg.id);
      };
  
      const deleteBtn = document.createElement('button');
      deleteBtn.className = 'message-action-btn delete';
      deleteBtn.innerHTML = '🗑️';
      deleteBtn.title = 'Delete';
      deleteBtn.onclick = (e) => {
        e.stopPropagation();
        deleteMessage(msg.id);
      };
  
      actions.append(editBtn, deleteBtn);
      messageElement.append(actions);
    }
  
    messageElement.append(content, time, status);
    return messageElement;
  }
  
  function sendMessage() {
    const input = document.getElementById('messageInput');
    const content = input.value.trim();
    if (!content || !currentContact) return;
  
    const newMsg = new Message(currentUser, currentContact, content);
    messagesDB[currentUser].push(newMsg);
    messagesDB.admin.push(newMsg);
  
    document.getElementById('chatBox').appendChild(createMessageElement(newMsg));
    input.value = '';
    document.getElementById('chatBox').scrollTop = document.getElementById('chatBox').scrollHeight;
  }
  
  function editMessage(msgId) {
    const msg = messagesDB[currentUser].find(m => m.id === msgId);
    const messageElement = document.querySelector(`[data-id="${msgId}"]`);
    messageElement.classList.add('editing');
    
    const newContent = prompt('Edit your message:', msg.content);
    if (newContent && newContent !== msg.content) {
      msg.content = newContent;
      msg.isEdited = true;
      msg.timestamp = new Date().toISOString();
      
      const adminMsg = messagesDB.admin.find(m => m.id === msgId);
      if(adminMsg) adminMsg.content = newContent;
      
      messageElement.querySelector('.message-content').textContent = newContent;
      messageElement.querySelector('.message-status').innerHTML = '✏️ Edited';
    }
    
    messageElement.classList.remove('editing');
  }
  
  function deleteMessage(msgId) {
    if (!confirm('Are you sure you want to delete this message?')) return;
    
    const messageElement = document.querySelector(`[data-id="${msgId}"]`);
    
    // Animation start
    messageElement.style.transform = 'scale(0.9)';
    messageElement.style.opacity = '0.5';
    
    setTimeout(() => {
      // Update database
      messagesDB[currentUser] = messagesDB[currentUser].filter(m => m.id !== msgId);
      const adminMsg = messagesDB.admin.find(m => m.id === msgId);
      if (adminMsg) adminMsg.isDeleted = true;
      
      // Update UI
      messageElement.querySelector('.message-content').textContent = 'Message deleted';
      messageElement.querySelector('.message-status').innerHTML = '🗑️ Deleted';
      
      // Force right-alignment and styling
      messageElement.classList.add('sent');
      messageElement.classList.remove('received', 'editing');
      messageElement.dataset.deleted = "true";  // Mark as deleted
      
      // Visual styling
      messageElement.style.backgroundColor = '#f8f8f8';
      messageElement.style.color = '#999';
      messageElement.style.border = '1px dashed #ccc';
      messageElement.style.transform = '';
      messageElement.style.opacity = '1';
      
      // Remove action buttons
      const actions = messageElement.querySelector('.message-actions');
      if (actions) actions.remove();
      
    }, 300); // Match this duration with your CSS transitions
  }
  
  // Initialize with sample data
  document.addEventListener('DOMContentLoaded', () => {
    messagesDB.user1.push(new Message('user1', 'Alice', 'Hello Alice!'));
    messagesDB.user1.push(new Message('Alice', 'user1', 'Hi there!'));
    messagesDB.admin.push(...messagesDB.user1);
  
    // Enter key support
    document.getElementById('messageInput').addEventListener('keydown', (e) => {
      if (e.key === 'Enter') sendMessage();
    });
  });