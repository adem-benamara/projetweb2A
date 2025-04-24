<?php
require_once __DIR__ . '/../view/database.php';
require_once __DIR__ . '/../controller/controller2.php';

$messages = getMessages($db); // Pass the $db object to the getMessages function
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Interaction & Matching - Back Office</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <header>
    <h1>Back Office - Interaction & Matching</h1>
  </header>

  <div class="main-layout">
    <div class="leftnav">
      <h2>Navigation</h2>
      <div class="nav-item">Admin</div>
      <div class="nav-item">Projects</div>
      <div class="nav-item">Events</div>
      <div class="nav-item">Messages</div>

      <!-- Dynamically Add Contacts -->
      <?php 
        // Assuming 'users' is a table in your database
        $sql = "SELECT user_id, username FROM users";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($users as $user): 
      ?>
        <div class="nav-item" data-contact-id="<?= $user['user_id'] ?>">
          <?= $user['username'] ?>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="container">
      <div class="messages-box">
        <h2>📨 Gestion des Messages</h2>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Expéditeur</th>
              <th>Destinataire</th>
              <th>Contenu</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($messages as $message): ?>
            <tr id="msg-row-<?= $message['message_id'] ?>">
              <td><?= $message['message_id'] ?></td>
              <td><?= $message['sender_username'] ?></td>
              <td><?= $message['receiver_username'] ?></td>
              <td id="msg-content-<?= $message['message_id'] ?>"><?= $message['content'] ?></td>
              <td><?= $message['timestamp'] ?></td>
              <td class="actions">
                <button onclick="editMessage(<?= $message['message_id'] ?>)">✏️</button>
                <button onclick="deleteMessage(<?= $message['message_id'] ?>)">🗑️</button>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script src="/web/view/script2.js?v=<?= time(); ?>"></script>

</body>
</html>
