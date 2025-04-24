<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once(__DIR__ . '/../view/database.php');


if (isset($_POST['message_id']) && isset($_POST['content'])) {
    $message_id = intval($_POST['message_id']);  // Ensure the ID is an integer
    $content = htmlspecialchars($_POST['content']);  // Sanitize the input content

    
    $sql = "UPDATE messages SET content = :content, is_edited = 1 WHERE message_id = :message_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':content', $content);
    $stmt->bindParam(':message_id', $message_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Message updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error updating message']);
    }
}


?>
