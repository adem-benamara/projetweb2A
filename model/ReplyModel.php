<?php
// model/ReplyModel.php
require_once __DIR__ . '/Database.php';

class ReplyModel {
 

    public static function create(array $data): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare("
          INSERT INTO reclamation_reply
            (reclamation_id, admin_id, message, file_path)
          VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([
          $data['reclamation_id'],
          $data['admin_id'],
          $data['message'],
          $data['file_path'] ?? null
        ]);
    }
    
    public static function getByReclamation(int $recId): array {
      $db = Database::getInstance();
      $stmt = $db->prepare("
          SELECT *, 
          IFNULL(file_path, '') as file_path 
          FROM reclamation_reply
          WHERE reclamation_id = ?
          ORDER BY created_at ASC
      ");
      $stmt->execute([$recId]);
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
