<?php
// view/admin_index.php
require_once __DIR__ . '/../controller/AdminReclamationController.php';
require_once __DIR__ . '/../model/ReclamationModel.php';

$controller = new AdminReclamationController();
$status = $_GET['status'] ?? 'all';
$selectedId = isset($_GET['id']) ? (int)$_GET['id'] : null;
// Handle actions
if (isset($_GET['action'])) {
  switch ($_GET['action']) {
      case 'reply':
          $controller->reply();
          break;
      case 'update_priority': // Add this case
          $controller->updatePriority();
          break;
  }
}
if (isset($_GET['action'])) {
    if ($_GET['action'] === 'reply' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->reply();
    } elseif ($_GET['action'] === 'mark_unread') {
        $controller->markUnread((int)$_GET['id']);
    }
}

// Get data
$data = $controller->index(
  status: $status,
  selectedId: $selectedId // Now properly cast to int|null
);
extract($data);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Reclamations</title>
    <link rel="stylesheet" href="style.css?v=<?= time() ?>"> <!-- Force cache refresh -->
</head>
<body>
    <header>
        <h1>Reclamations Dashboard</h1>
    </header>
    
    <div class="main-layout">
        <nav class="leftnav">
            <h2>Navigation</h2>
            <div class="nav-item active">Reclamations</div>
        </nav>

        <div class="container">
            <div class="messages-box">
                <!-- Status Filter -->
                <div class="filter-buttons" style="margin-bottom: 20px;">
    <?php
    // Get counts for each status (you'll need to implement these methods)
    $allCount = count($recs);
    $pendingCount = array_reduce($recs, fn($carry, $item) => $item['status'] === 'Pending' ? $carry + 1 : $carry, 0);
    $answeredCount = $allCount - $pendingCount;
    ?>
    
    <a href="?status=all" class="agree-btn <?= $status === 'all' ? 'active' : '' ?>">
        All (<?= $allCount ?>)
    </a>
    <a href="?status=Pending" class="agree-btn <?= $status === 'Pending' ? 'active' : '' ?>">
        Pending (<?= $pendingCount ?>)
    </a>
    <a href="?status=Answered" class="agree-btn <?= $status === 'Answered' ? 'active' : '' ?>">
        Answered (<?= $answeredCount ?>)
    </a>
</div>

                <!-- Reclamations Table -->
                <table>
                <thead>
    <tr>
        <th>User</th>
        <th>Subject</th>
        <th>Priority</th> <!-- New column -->
        <th>Status</th>
        <th>Date</th>
        <th>Set Priority</th> <!-- Changed from Actions -->
    </tr>
</thead>
                    <tbody>
                        <?php foreach ($recs as $rec): ?>
                        <tr>
                            <td><?= htmlspecialchars($rec['username']) ?></td>
                            <td>
                                <a href="admin_index.php?id=<?= $rec['id'] ?>&status=<?= $status ?>">
                                    <?= htmlspecialchars($rec['subject']) ?>
                                </a>
                            </td>
                            <td>
    <span class="priority-indicator priority-<?= strtolower($rec['priority'] ?? 'low') ?>">
        <?= ucfirst($rec['priority'] ?? 'Low') ?>
    </span>
</td>
                            <td>
                                <span class="status-badge <?= strtolower($rec['status']) ?>">
                                    <?= $rec['status'] ?>
                                </span>
                            </td>
                            <td><?= date('d M Y, H:i', strtotime($rec['created_at'])) ?></td>
                            <td class="actions">
    <form method="post" action="admin_index.php?action=update_priority&status=<?= $status ?>">
        <input type="hidden" name="reclamation_id" value="<?= $rec['id'] ?>">
        <select name="priority" class="priority-select" onchange="this.form.submit()">
            <option value="high" <?= $rec['priority'] === 'high' ? 'selected' : '' ?>>🔥 High</option>
            <option value="medium" <?= $rec['priority'] === 'medium' ? 'selected' : '' ?>>⚠️ Medium</option>
            <option value="low" <?= $rec['priority'] === 'low' ? 'selected' : '' ?>>⬇️ Low</option>
        </select>
    </form>
</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Selected Reclamation Thread -->
                <?php if (!empty($selected)): ?>
                <div class="chat-container" style="margin-top: 30px;">
                    <div class="chat-header">
                        <span><?= htmlspecialchars($selected['subject']) ?></span>
                        <span>Status: <?= $selected['status'] ?></span>
                    </div>
                    
                    <div class="chat-box">
                        <div class="message original">
                            <p><?= nl2br(htmlspecialchars($selected['description'])) ?></p>
                            <?php if (!empty($selected['file_path'])): ?>
                            <div class="attachment">
                                📎 <a href="<?= $selected['file_path'] ?>" download>Attachment</a>
                            </div>
                            <?php endif; ?>
                        </div>

                        <?php foreach ($replies as $reply): ?>
                        <div class="message reply">
                            <p><?= nl2br(htmlspecialchars($reply['message'])) ?></p>
                            <div class="meta">
                                <?= date('d M Y H:i', strtotime($reply['created_at'])) ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Reply Form -->
                    <form method="post" action="admin_index.php?action=reply" enctype="multipart/form-data" class="chat-input">
    <input type="hidden" name="reclamation_id" value="<?= $selected['id'] ?>">
    
    <div class="form-group">
        <textarea 
            name="message" 
            placeholder="✏️ Type your reply here..." 
            required
        ></textarea>
    </div>
    
    <div class="form-group">
        <label class="file-upload">
            📎 Attach File:
            <input type="file" name="file">
        </label>
    </div>
    
    <div class="form-group">
        <button type="submit" class="submit-btn">
            📨 Send Reply
        </button>
    </div>
</form>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>