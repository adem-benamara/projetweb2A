<?php
// If someone opens view.php directly, run the controller instead
if (!isset($users)) {
    require __DIR__ . '/../controller/controller.php';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Chat Page</title>
  <style>
    body { margin:0; font-family:Arial,sans-serif; display:flex; height:100vh; flex-direction:column; }
    .top-nav { background:#3B82F6; display:flex; justify-content:space-between; align-items:center; padding:10px 20px; }
    .nav-links a { color:#fff; margin:0 15px; text-decoration:none; font-weight:bold; }
    .nav-picture img { height:40px; }
    .container { display:flex; height:100%; }
    .sidebar { width:250px; background:#2c3e50; color:#fff; padding:20px; box-sizing:border-box; overflow-y:auto; }
    .sidebar h2 { margin-top:0; }
    .contact { padding:10px; cursor:pointer; border-bottom:1px solid #34495e; }
    .contact:hover { background:#34495e; }
    .chat-container { flex:1; display:flex; flex-direction:column; padding:20px; }
    .chat-header { background:#3498db; color:#fff; padding:10px; font-size:18px; display:flex; justify-content:space-between; align-items:center; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.1); margin-bottom:20px; }
    .chat-header #chatTitle { font-size:20px; }
    .agree-buttons { display:inline-flex; gap:10px; float:right; }
    .agree-btn { padding:6px 12px; font-size:14px; background:#ddd; color:#333; border:none; border-radius:5px; cursor:pointer; transition:background-color .3s,transform .3s; }
    .agree-btn.active { background:#4CAF50; color:#fff; transform:scale(1.05); }
    .merged-agree-btn { background:#3B82F6!important; color:#fff!important; animation:pulse 1s infinite alternate; padding:6px 20px; font-weight:bold; border-radius:5px; }
    @keyframes pulse { 0%{transform:scale(1);}100%{transform:scale(1.1);} }
    .chat-box { flex:1; padding:10px; overflow-y:auto; background:#ecf0f1; scroll-behavior:smooth; }
    .message { position:relative; padding:12px 15px; margin:8px 0; border-radius:18px; max-width:70%; clear:both; transition:all .3s; transform-origin:center bottom; animation:bubbleIn .3s; }
    .message.sent[data-deleted="true"]{ background:#f8f8f8!important; color:#999!important; border:1px dashed #ccc!important; }
    .sent { background:#3B82F6; color:#fff; margin-left:auto; border-bottom-right-radius:4px; }
    .received { background:#f1f1f1; color:#333; margin-right:auto; border-bottom-left-radius:4px; }
    .message-actions { position:absolute; top:-15px; right:10px; display:flex; gap:5px; opacity:0; transform:translateY(10px); transition:all .3s; }
    .sent .message-actions { left:10px; right:auto; }
    .message:hover .message-actions { opacity:1; transform:translateY(0); }
    .message-action-btn { width:28px; height:28px; border-radius:50%; border:none; background:#fff; box-shadow:0 2px 5px rgba(0,0,0,0.2); cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all .2s; }
    .message-action-btn:hover { transform:scale(1.1); }
    .message-action-btn.edit { color:#3B82F6; }
    .message-action-btn.delete { color:#EF4444; }
    .message-status { font-size:11px; opacity:.7; margin-top:3px; display:flex; gap:5px; align-items:center; }
    .editing { background:rgba(59,130,246,.2)!important; box-shadow:0 0 0 2px #3B82F6!important; }
    @keyframes bubbleIn { 0%{transform:scale(.9);opacity:0;}100%{transform:scale(1);opacity:1;} }
    .chat-input { display:flex; padding:10px; background:#fff; border-top:1px solid #ccc; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.1); margin-top:10px; }
    .chat-input input { flex:1; padding:12px 15px; font-size:16px; border:1px solid #ccc; border-radius:8px; margin-right:10px; transition:all .3s ease; }
    .chat-input input:focus { border-color:#3498db; outline:none; box-shadow:0 0 5px rgba(52,152,219,0.5); }
    .chat-input button { padding:12px 20px; font-size:16px; background:#3498db; color:#fff; border:none; border-radius:8px; cursor:pointer; transition:all .3s ease; }
    .chat-input button:hover { background:#2980b9; transform:translateY(-2px); }
    .chat-input button:active { transform:translateY(1px); }
  </style>
</head>
<body>
  <div class="top-nav">
    <div class="nav-picture"><img src="logo.png" alt="Logo"/></div>
    <div class="nav-links">
      <a href="#">Entrepreneur</a>
      <a href="#">Investisseur</a>
      <a href="#">Projets</a>
      <a href="#">Contact</a>
    </div>
  </div>
  <div class="container">
    <div class="sidebar">
      <h2>Contacts</h2>
      <?php foreach ($users as $user): ?>
        <?php if ($user['user_id'] != $currentUser['user_id']): ?>
          <div class="contact"
               onclick="selectContact(<?= $user['user_id'] ?>,'<?= htmlspecialchars($user['username']) ?>')">
            <?= htmlspecialchars($user['username']) ?>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
    <div class="chat-container">
      <div class="chat-header" id="chatHeader">
        <span id="chatTitle">Select a contact</span>
        <div class="agree-buttons">
          <button class="agree-btn" id="investorBtn" onclick="toggleAgree('investor')">👍 Investor</button>
          <button class="agree-btn" id="entrepreneurBtn" onclick="toggleAgree('entrepreneur')">👍 Entrepreneur</button>
        </div>
      </div>
      <div class="chat-box" id="chatBox"></div>
      <div class="chat-input">
        <input id="messageInput" type="text" placeholder="Type a message"/>
        <button onclick="sendMessage()">Send</button>
      </div>
    </div>
  </div>
  <script>
    let investorClicked = false;
    let entrepreneurClicked = false;

    function toggleAgree(role) {
      const investorBtn = document.getElementById('investorBtn');
      const entrepreneurBtn = document.getElementById('entrepreneurBtn');
      const container = document.querySelector('.agree-buttons');

      if (role === 'investor') {
        investorClicked = !investorClicked;
        investorBtn.classList.toggle('active');
      } else {
        entrepreneurClicked = !entrepreneurClicked;
        entrepreneurBtn.classList.toggle('active');
      }

      if (investorClicked && entrepreneurClicked) {
        container.innerHTML = '<button class="agree-btn merged-agree-btn">🤝 Agreed</button>';
      }
    }

    let currentContactId = null;
    const currentUserId = <?= $currentUser['user_id'] ?>;

    function selectContact(id, name) {
      currentContactId = id;
      document.getElementById('chatTitle').textContent = name;
      fetchMessages();
    }

    function fetchMessages() {
      if (!currentContactId) return;
      const form = new FormData();
      form.append('action','selectContact');
      form.append('contactId',currentContactId);
      fetch('../controller/controller.php',{method:'POST',body:form})
        .then(r=>r.json())
        .then(data=>{
          if (!data.success) return;
          const box = document.getElementById('chatBox');
          box.innerHTML='';
          data.messages.forEach(m=>box.appendChild(createMessageElement(m)));
          box.scrollTop = box.scrollHeight;
        });
    }

    function createMessageElement(msg) {
      const el = document.createElement('div');
      el.className = 'message '+(msg.sender_id==currentUserId?'sent':'received');
      el.dataset.id = msg.message_id;
      if (msg.is_deleted) el.dataset.deleted='true';
      el.innerHTML=`
        <div class="message-content">${msg.is_deleted?'Message deleted':msg.content}</div>
        <div class="message-time">${new Date(msg.timestamp).toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'})}</div>
        <div class="message-status">${msg.is_edited?'✏️ Edited':''}${msg.is_deleted?'🗑️ Deleted':''}</div>
      `;
      if (msg.sender_id==currentUserId && !msg.is_deleted) {
        const acts = document.createElement('div');
        acts.className='message-actions';
        acts.innerHTML=`
          <button class="message-action-btn edit" onclick="editMessage(${msg.message_id},'${msg.content.replace(/'/g,"\\'")}')">✏️</button>
          <button class="message-action-btn delete" onclick="deleteMessage(${msg.message_id})">🗑️</button>
        `;
        el.appendChild(acts);
      }
      return el;
    }

    function sendMessage() {
      const inp = document.getElementById('messageInput');
      const txt = inp.value.trim();
      if (!txt) { alert('Message cannot be empty!'); return; }
      if (txt.length>20) { alert('Message must be 20 characters or less!'); return; }
      if (!currentContactId) return;
      const form = new FormData();
      form.append('action','sendMessage');
      form.append('contactId',currentContactId);
      form.append('content',txt);
      fetch('../controller/controller.php',{method:'POST',body:form})
        .then(r=>r.json()).then(data=>{
          if (data.success) {
            document.getElementById('chatBox').appendChild(createMessageElement(data.message));
            document.getElementById('chatBox').scrollTop = document.getElementById('chatBox').scrollHeight;
            inp.value='';
          }
        });
    }

    function editMessage(messageId, currentContent) {
      const newText = prompt('Edit your message:', currentContent);
      if (!newText||newText===currentContent) return;
      const form=new FormData();
      form.append('action','editMessage');
      form.append('messageId',messageId);
      form.append('content',newText);
      fetch('../controller/controller.php',{method:'POST',body:form})
        .then(r=>r.json()).then(data=>{
          if (data.success) {
            const el=document.querySelector(`[data-id="${messageId}"]`);
            el.querySelector('.message-content').textContent=newText;
            el.querySelector('.message-status').innerHTML='✏️ Edited';
          } else alert('Edit failed');
        });
    }

    function deleteMessage(messageId) {
      if (!confirm('Are you sure you want to delete this message?')) return;
      const form=new FormData();
      form.append('action','deleteMessage');
      form.append('messageId',messageId);
      fetch('../controller/controller.php',{method:'POST',body:form})
        .then(r=>r.json()).then(data=>{
          if (data.success) {
            const el=document.querySelector(`[data-id="${messageId}"]`);
            el.querySelector('.message-content').textContent='Message deleted';
            el.querySelector('.message-status').innerHTML='🗑️ Deleted';
            el.dataset.deleted='true';
          } else alert('Delete failed');
        });
    }

    document.getElementById('messageInput').addEventListener('keydown', e=>{
      if (e.key==='Enter') { e.preventDefault(); sendMessage(); }
    });

    window.addEventListener('DOMContentLoaded', ()=>{
      const first=document.querySelector('.contact');
      if (first) first.click();
    });
  </script>
</body>
</html>
