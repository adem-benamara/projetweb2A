<?php
// model/ReclamationModel.php
require_once __DIR__ . '/Database.php';

class ReclamationModel {
    /**
     * Fetch all reclamations for a given user.
     *
     * @param int $userId
     * @return array
     */
    public static function getByUser(int $userId): array {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT *
             FROM reclamation
             WHERE user_id = ?
             ORDER BY created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch a single reclamation by its ID.
     *
     * @param int $id
     * @return array|null
     */
    public static function getById(int $id): ?array {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM reclamation WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Create a new reclamation.
     *
     * @param array $data
     * @return bool
     */
    public static function create(array $data): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            "INSERT INTO reclamation
             (user_id, subject, description, file_path)
             VALUES (?, ?, ?, ?)"
        );
        return $stmt->execute([
            $data['user_id'],
            $data['subject'],
            $data['description'],
            $data['file_path'] ?? null
        ]);
    }

    /**
     * Mark a reclamation as answered.
     *
     * @param int $id
     * @return bool
     */
    public static function markAnswered(int $id): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            UPDATE reclamation 
            SET status = 'Answered', updated_at = NOW() 
            WHERE id = ?
        ");
        return $stmt->execute([$id]);
    }

    // ─── Admin‐side methods ───────────────────────────────────────

    /**
     * Fetch all reclamations (optionally filtered by status),
     * with reply counts & last-reply timestamp.
     *
     * @param string $status 'all', 'Pending', or 'Answered'
     * @return array
     */
    public static function getAllWithStats(string $status = 'all'): array {
        $db = Database::getInstance();
        $sql = "
            SELECT
                r.id,
                r.user_id,
                u.username,
                r.subject,
                r.status,
                r.priority,
                r.created_at,
                r.is_read,
                COUNT(rr.id) AS reply_count,
                MAX(rr.created_at) AS last_reply_at
            FROM reclamation r
            JOIN users u ON u.user_id = r.user_id
            LEFT JOIN reclamation_reply rr ON rr.reclamation_id = r.id
        ";
        $params = [];
        if (in_array($status, ['Pending','Answered'], true)) {
            $sql .= " WHERE r.status = ?";
            $params[] = $status;
        }
        $sql .= "
        GROUP BY r.id
        ORDER BY 
            CASE r.priority
                WHEN 'high' THEN 1
                WHEN 'medium' THEN 2
                WHEN 'low' THEN 3
            END,
            r.created_at DESC
    ";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch one reclamation plus its user info.
     *
     * @param int $id
     * @return array|null
     */
    public static function getByIdWithUser(int $id): ?array {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT
              r.*,
              u.username,
              u.email AS user_email
            FROM reclamation r
            JOIN users u ON u.user_id = r.user_id
            WHERE r.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Mark a reclamation as read.
     *
     * @param int $id
     * @return bool
     */
    public static function markRead(int $id): bool {
        $db = Database::getInstance();
        return $db
            ->prepare("UPDATE reclamation SET is_read = 1 WHERE id = ?")
            ->execute([$id]);
    }

    /**
     * Mark a reclamation as unread.
     *
     * @param int $id
     * @return bool
     */
    public static function markUnread(int $id): bool {
        $db = Database::getInstance();
        return $db
            ->prepare("UPDATE reclamation SET is_read = 0 WHERE id = ?")
            ->execute([$id]);
    }
    // Add this new method
public static function updatePriority(int $id, string $priority): bool {
    $db = Database::getInstance();
    $stmt = $db->prepare("UPDATE reclamation SET priority = ? WHERE id = ?");
    return $stmt->execute([$priority, $id]);
}
}
