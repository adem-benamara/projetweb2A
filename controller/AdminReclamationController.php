<?php
// controller/AdminReclamationController.php
require_once __DIR__ . '/../model/ReclamationModel.php';
require_once __DIR__ . '/../model/ReplyModel.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class AdminReclamationController {
    private function ensureSession(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

    /** List & optionally select a thread */
    public function index(string $status = 'all', ?int $selectedId = null): array {
        $this->ensureSession();
        $status = in_array($status, ['all', 'Pending', 'Answered']) ? $status : 'all';
        $selectedId = $selectedId ? (int)$selectedId : null;
        $recs = ReclamationModel::getAllWithStats($status);
        $selected = null;
        $replies  = [];
        if ($selectedId) {
            $selected = ReclamationModel::getByIdWithUser($selectedId);
            ReclamationModel::markRead($selectedId);
            $replies = ReplyModel::getByReclamation($selectedId);
        }
        return [
            'recs'       => $recs,
            'selected'   => $selected,
            'replies'    => $replies,
            'status'     => $status,
            'selectedId' => $selectedId
        ];
    }

    /** Handle admin reply then redirect */
    public function reply(): void {
        $this->ensureSession();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['reclamation_id'])) {
            $recId   = (int)$_POST['reclamation_id'];
            $message = trim($_POST['message']);
    
            $filePath = null;
            if (!empty($_FILES['file']['name'])) {
                $dir = __DIR__ . '/../uploads/reclamation_replies/';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $fn = time().'_'.basename($_FILES['file']['name']);
                move_uploaded_file($_FILES['file']['tmp_name'], $dir.$fn);
                $filePath = 'uploads/reclamation_replies/'.$fn;
            }
    
            ReplyModel::create([
                'reclamation_id' => $recId,
                'admin_id'       => $_SESSION['user_id'],
                'message'        => $message,
                'file_path'      => $filePath
            ]);
    
            ReclamationModel::markAnswered($recId);
            ReclamationModel::markRead($recId);
    
            // PHPMailer implementation
            require_once __DIR__ . '/../phpmailer/src/Exception.php';
        require_once __DIR__ . '/../phpmailer/src/PHPMailer.php';
        require_once __DIR__ . '/../phpmailer/src/SMTP.php';

        $mail = new PHPMailer(true);
        try {
            // SMTP Settings for Gmail
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'werfellimehdi00@gmail.com'; // Your Gmail
            $mail->Password   = 'ehrczobjgkcqscba';          // App Password (NOT your regular password)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use TLS
            $mail->Port       = 587; // Gmail SMTP port for TLS

            // Email Details
            $mail->setFrom('werfellimehdi00@gmail.com', 'University Project');
            $mail->addAddress('werfellimehdi00@gmail.com'); // Send to yourself
            $mail->Subject = 'New Reply to Reclamation #' . $_POST['reclamation_id'];
            $mail->Body    = "your Reclamation have been replied to\n\n" .
                            "Reply Content: " . $_POST['message'];

            // Send email
            $mail->send();
        } catch (Exception $e) {
            // Log errors to XAMPP's error log
            error_log('PHPMailer Error: ' . $e->getMessage());
        }

        // Redirect back
        header("Location: admin_index.php?id=" . $_POST['reclamation_id']);
        exit;
    }
}
    public function updatePriority(): void {
        $this->ensureSession();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)$_POST['reclamation_id'];
            $priority = $_POST['priority'];
            $status = $_GET['status'] ?? 'all';
    
            ReclamationModel::updatePriority($id, $priority);
        }
        
        header("Location: admin_index.php?status=$status&id=$id");
        exit;
    }
}
