<?php
// controller/ReclamationController.php
require_once __DIR__ . '/../model/ReclamationModel.php';
require_once __DIR__ . '/../model/ReplyModel.php';

class ReclamationController {
    private function ensureSession(): void {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = 2; // Simulate logged‐in user
        }
    }

    /** List user reclamations */
    public function index(): array {
        $this->ensureSession();
        $recs = ReclamationModel::getByUser((int)$_SESSION['user_id']);
        return ['recs' => $recs];
    }

    /** Show one thread */
    public function view(int $id): array {
        $this->ensureSession();
        $userId = (int)$_SESSION['user_id'];
        $recs   = ReclamationModel::getByUser($userId);
        $rec    = ReclamationModel::getById($id);
        if (!$rec || $rec['user_id'] !== $userId) {
            die('Unauthorized');
        }
        $replies = ReplyModel::getByReclamation($id);
        return [
            'recs'     => $recs,
            'selected' => $rec,
            'replies'  => $replies
        ];
    }

    /** Handle creation then redirect */
    public function create(): void {
        $this->ensureSession();
        if (
            $_SERVER['REQUEST_METHOD'] === 'POST' &&
            !empty($_POST['subject']) &&
            !empty($_POST['description'])
        ) {
            $filePath = null;
            if (!empty($_FILES['file']['name'])) {
                $dir = __DIR__ . '/../uploads/reclamations/';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $fn = time().'_'.basename($_FILES['file']['name']);
                move_uploaded_file($_FILES['file']['tmp_name'], $dir.$fn);
                $filePath = 'uploads/reclamations/'.$fn;
            }
            ReclamationModel::create([
                'user_id'     => $_SESSION['user_id'],
                'subject'     => $_POST['subject'],
                'description' => $_POST['description'],
                'file_path'   => $filePath
            ]);
        }
        header('Location: index.php?page=reclamations');
        exit;
    }
}
