<?php
// Include the database connection
require_once __DIR__ . '/../view/database.php';

// Function to fetch messages
function getMessages(PDO $db) {
    $sql = "
    SELECT 
        m.message_id, 
        m.sender_id, 
        m.receiver_id, 
        m.content, 
        m.timestamp,
        s.username AS sender_username, 
        r.username AS receiver_username
    FROM messages m
    LEFT JOIN users s ON m.sender_id = s.user_id
    LEFT JOIN users r ON m.receiver_id = r.user_id";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Return all fetched messages
}

// Function to fetch messages for a specific contact
function getMessagesForContact(PDO $db, $contact_id) {
    $sql = "
    SELECT 
        m.message_id, 
        m.sender_id, 
        m.receiver_id, 
        m.content, 
        m.timestamp,
        s.username AS sender_username, 
        r.username AS receiver_username
    FROM messages m
    LEFT JOIN users s ON m.sender_id = s.user_id
    LEFT JOIN users r ON m.receiver_id = r.user_id
    WHERE m.sender_id = :contact_id OR m.receiver_id = :contact_id";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':contact_id', $contact_id);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
