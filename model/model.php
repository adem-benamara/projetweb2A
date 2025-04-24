<?php
class Model {
    private $db;
    public function __construct() {
        try {
            $this->db = new PDO(
                'mysql:host=localhost;dbname=project;charset=utf8mb4',
                'root',
                ''
            );
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('DB Connection failed: '.$e->getMessage());
        }
    }

    public function getUsers() {
        $stmt = $this->db->prepare("
            SELECT user_id, username
            FROM users
            WHERE user_type IN ('entrepreneur','investor')
        ");
        $stmt->execute();
        $r = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$r) {
            // fallback defaults
            $r = [
                ['user_id'=>1,'username'=>'Alice'],
                ['user_id'=>2,'username'=>'Bob'],
                ['user_id'=>3,'username'=>'Charlie'],
                ['user_id'=>4,'username'=>'David'],
                ['user_id'=>5,'username'=>'Eve'],
            ];
        }
        // also include our extra contact here if you like—but we already appended in controller
        return $r;
    }

    public function getUserById($id) {
        // special‐case our extra contact
        if ($id === 6) {
            return ['user_id'=>6,'username'=>'Investor 2'];
        }

        $stmt = $this->db->prepare("
            SELECT user_id, username
            FROM users
            WHERE user_id = ?
        ");
        $stmt->execute([$id]);
        $u = $stmt->fetch(PDO::FETCH_ASSOC);
        return $u ?: ['user_id'=>$id,'username'=>'You'];
    }

    public function getMessages($me, $them) {
        $stmt = $this->db->prepare("
            SELECT m.*
            FROM messages m
            WHERE (m.sender_id = :me AND m.receiver_id = :them)
               OR (m.sender_id = :them AND m.receiver_id = :me)
            ORDER BY m.timestamp
        ");
        $stmt->execute([':me'=>$me,':them'=>$them]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function sendMessage($me, $them, $content) {
        $stmt = $this->db->prepare("
            INSERT INTO messages (sender_id, receiver_id, content, timestamp)
            VALUES (:s,:r,:c,NOW())
        ");
        $stmt->execute([':s'=>$me,':r'=>$them,':c'=>$content]);
        $id = $this->db->lastInsertId();
        return [
            'message_id'  => (int)$id,
            'sender_id'   => $me,
            'receiver_id' => $them,
            'content'     => $content,
            'timestamp'   => date('Y-m-d H:i:s'),
            'is_edited'   => false,
            'is_deleted'  => false
        ];
    }

    public function editMessage($mid, $content) {
        $stmt = $this->db->prepare("
            UPDATE messages
            SET content = :c, is_edited = 1
            WHERE message_id = :id
        ");
        return $stmt->execute([':c'=>$content,':id'=>$mid]);
    }

    public function deleteMessage($mid) {
        $stmt = $this->db->prepare("
            UPDATE messages
            SET is_deleted = 1
            WHERE message_id = :id
        ");
        return $stmt->execute([':id'=>$mid]);
    }
}
