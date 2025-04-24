<?php
// models/model.php
require_once('/../controller/controller.php');
class MessageModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getMessages() {
        $query = "SELECT * FROM messages";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateMessage($id, $content) {
        $query = "UPDATE messages SET content = :content, isEdited = 1 WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function deleteMessage($id) {
        $query = "DELETE FROM messages WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>
