<?php
// view/index.php
require_once __DIR__ . '/../controller/ReclamationController.php';

// Remove session check completely
$controller = new ReclamationController();
$selectedId = $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'create') {
    $controller->create();
    header('Location: index.php');
    exit;
}

$data = $selectedId ? $controller->view($selectedId) : $controller->index();
extract($data);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reclamations</title>
    <style>
        /* User-provided frontend CSS */
        .modal-content {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .modal-content form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .modal-content input,
    .modal-content textarea {
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 16px;
    }

    .modal-content button[type="submit"] {
        background: #3B82F6;
        color: white;
        padding: 12px 20px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: bold;
    }

    .modal-content button[type="submit"]:hover {
        background: #2563eb;
    }
    .modal {
    display: none; /* Keep this as default */
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    /* Add these */
    justify-content: center;
    align-items: center;
}

/* Add this new class */
.modal.active {
    display: flex;
}
.modal-content {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    position: relative;
}

.modal-content form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.modal-content input,
.modal-content textarea {
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 16px;
}

.modal-content button[type="submit"] {
    background: #3B82F6;
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    transition: background 0.3s;
}

.modal-content button[type="submit"]:hover {
    background: #2563eb;
}
        body { margin:0; font-family:Arial,sans-serif; display:flex; height:100vh; flex-direction:column; }
        .top-nav { background:#3B82F6; display:flex; justify-content:space-between; align-items:center; padding:10px 20px; }
        .nav-links a { color:#fff; margin:0 15px; text-decoration:none; font-weight:bold; }
        .container { display:flex; height:100%; }
        .sidebar { width:250px; background:#2c3e50; color:#fff; padding:20px; box-sizing:border-box; overflow-y:auto; }
        .contact { padding:10px; cursor:pointer; border-bottom:1px solid #34495e; }
        .contact:hover { background:#34495e; }
        .chat-container { flex:1; display:flex; flex-direction:column; padding:20px; }
        .chat-header { background:#3498db; color:#fff; padding:10px; font-size:18px; border-radius:12px; margin-bottom:20px; }
        .chat-box { flex:1; padding:10px; overflow-y:auto; background:#ecf0f1; border-radius:8px; }
        .floating-btn { position:fixed; bottom:30px; right:30px; width:60px; height:60px; border-radius:50%; background:#3B82F6; color:#fff; font-size:36px; border:none; cursor:pointer; }
    </style>
</head>
<body>
  <div class="top-nav">
    <div class="nav-links">
      <a href="index.php?page=messages">Messages</a>
      <a href="index.php?page=reclamations">Reclamations</a>
    </div>
    <div class="nav-picture">
      <img src="path/to/profile.jpg" alt="Profile" height="40">
    </div>
  </div>

  <div class="container">
    <!-- Sidebar: list of user reclamations -->
    <div class="sidebar">
      <h2>Your Reclamations</h2>
      <?php foreach ($recs as $r): 
        $isActive = isset($selected) && $selected['id'] === $r['id'];
      ?>
        <div class="contact <?= $isActive ? 'active' : '' ?>"
             onclick="location.href='index.php?page=reclamations&id=<?= $r['id'] ?>'">
          <strong><?= htmlspecialchars($r['subject']) ?></strong><br>
          <small><?= date('d M Y, H:i', strtotime($r['created_at'])) ?></small>
          <span style="
            float:right;
             background:<?= $r['status'] === 'Pending' ? '#e74c3c' : '#2ecc71' ?>;
            color:#fff;
            padding:2px 6px;
            border-radius:4px;
            font-size:12px;
          "><?= $r['status'] ?></span>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Main thread area -->
    <div class="chat-container">
      <?php if (!empty($selected)): ?>
        <div class="chat-header"><?= htmlspecialchars($selected['subject']) ?></div>
        <div class="chat-box">
          <div class="message original">
            <?= nl2br(htmlspecialchars($selected['description'])) ?>
            <?php if (!empty($selected['file_path'])): ?>
  <div style="margin-top:8px;">
    📎 <a href="/web/<?= htmlspecialchars($selected['file_path']) ?>" target="_blank">Download attachment</a>
  </div>
<?php endif; ?>
            <div style="font-size:11px; opacity:.7; text-align:right;">
              <?= date('d M Y, H:i', strtotime($selected['created_at'])) ?>
            </div>
          </div>
          <?php foreach ($replies as $rep): ?>
            <div class="message reply">
              <?= nl2br(htmlspecialchars($rep['message'])) ?>
              <?php if (!empty($rep['file_path'])): ?>
  <div style="margin-top:8px;">
    📎 <a href="/web/<?= htmlspecialchars($rep['file_path']) ?>" target="_blank">Download reply file</a>
  </div>
<?php endif; ?>
              <div style="font-size:11px; opacity:.7; text-align:right;">
                <?= date('d M Y, H:i', strtotime($rep['created_at'])) ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="chat-box" style="display:flex;align-items:center;justify-content:center;color:#666;">
          Select a reclamation to view its details.
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Floating button + modal -->
  <button id="newReclamationBtn" class="floating-btn">+</button>
  <div id="reclamationModal" class="modal">
    <div class="modal-content">
      <span class="close" id="modalClose">&times;</span>
      <h2>New Reclamation</h2>
      <form action="index.php?page=reclamations&action=create" method="POST" enctype="multipart/form-data">
        <input type="text" name="subject" placeholder="Subject" required>
        <textarea name="description" rows="5" placeholder="Describe your complaint..." required></textarea>
        <input type="file" name="file">
        <button type="submit">Send</button>
      </form>
    </div>
  </div>

  <script>
     const modal = document.getElementById('reclamationModal');
    
    // Toggle modal visibility
    function toggleModal(show) {
        modal.classList.toggle('active', show);
    }

    document.getElementById('newReclamationBtn').onclick = () => toggleModal(true);
    document.getElementById('modalClose').onclick = () => toggleModal(false);
    window.onclick = e => { if (e.target === modal) toggleModal(false); };
  </script>
</body>
</html>
