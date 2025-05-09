<?php
require_once("../config/database.php");
require_once("../model/Participant.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../libs/PHPMailer/src/PHPMailer.php';
require '../libs/PHPMailer/src/SMTP.php';
require '../libs/PHPMailer/src/Exception.php';

$participant = new Participant();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = trim($_POST['participant_nom']);
    $prenom = trim($_POST['participant_prenom']);
    $age = $_POST['age'];
    $metier = $_POST['participant_metier'];
    $event_id = $_POST['event_id'];
    $email = $_POST['participant_email'];

    $errors = [];
    if (!$nom || !$prenom) $errors[] = "Name required.";
    if (!is_numeric($age) || $age < 16) $errors[] = "Valid age required.";
    if (!in_array($metier, ['entrepreneur', 'investisseur'])) $errors[] = "Valid métier required.";

    // Get event name from event_id
    $stmt = $pdo->prepare("SELECT event_name FROM events WHERE event_id = ?");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        $errors[] = "Invalid event selected.";
    } else {
        $event_name = $event['event_name']; // Get the event name
    }

    if (empty($errors)) {
        // Save participant to database
        $participant->create($nom, $prenom, $age, $metier, $event_id, $email);
        // Path to PDF invitation
        $pdf_path = "../public/invitations/" . $event_name . ".pdf"; // Path to the PDF
        // Path to QR code
        $qr_path = "../public/qrcodes/" . $event_name . ".png";

        // Set up the email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'sandbox.smtp.mailtrap.io'; 
            $mail->SMTPAuth = true;
            $mail->Username = 'fbd819eacba989';
            $mail->Password = '7a4e7106768bc2'; 
            $mail->SMTPSecure = 'tls';
            $mail->Port = 2525;

            // Sender's email
            $mail->setFrom('noreply@example.com', 'Events Platform');
            // Recipient's email
            $mail->addAddress($email);  

            // Attach the QR code image (embed it)
            $mail->addEmbeddedImage($qr_path, 'qrcodeimg'); // 'qrcodeimg' is the reference ID
            // Attach the PDF file
            $mail->addAttachment($pdf_path, 'Invitation.pdf'); // Attach the PDF
            // Email content
            $mail->isHTML(true);
            $mail->Subject = 'Thank You for Your Participation!';
            $mail->Body = "
                <p>Thank you for registering for <strong>$event_name</strong>!</p>
                <p>Here is your QR code:</p>
                <img src='cid:qrcodeimg'>
                <p>Your PDF invitation is also attached.</p>
            ";

            // Send email
            $mail->send();
            echo 'Email sent successfully!';
        } catch (Exception $e) {
            echo "Error sending email: {$mail->ErrorInfo}";
        }

        // Redirect after email
        header("Location: ../view/eventList.php");
        exit;
    } else {
        session_start();
        $_SESSION['errors'] = $errors;
        header("Location: ../view/participantForm.php?event_id=$event_id");
        exit;
    }
}
?>
