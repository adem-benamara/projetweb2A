<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once(__DIR__ . '/../view/database.php'); 

if (isset($_POST['message_id'])) {
    $message_id = intval($_POST['message_id']); 

    
    $sql = "DELETE FROM messages WHERE message_id = :message_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':message_id', $message_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Message deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error deleting message']);
    }
}

?>
