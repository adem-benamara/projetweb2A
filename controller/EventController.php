<?php
require_once("../config/database.php");
require_once("../model/Event.php");
require_once('C:/xampp/htdocs/events_project/libs/qrcodes/qrlib.php');
require_once('C:/xampp/htdocs/events_project/libs/fpdf/fpdf.php');

$event = new Event();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['events_name']);
    $place = trim($_POST['events_place']);
    $date = $_POST['events_date'];
    $errors = [];

    // Create and save the PDF invitation
    function createEventInvitationPDF($event_name, $event_date, $event_place) {
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFillColor(70,130,180); // Steel blue
        $pdf->SetTextColor(255);
        $pdf->SetFont('Arial', 'B', 24);
        $pdf->Cell(0, 20, 'Invitation', 0, 1, 'C', true);
        $pdf->Ln(10);
        $pdf->SetTextColor(0);
        $pdf->SetFont('Arial', '', 14);
        $pdf->MultiCell(0, 10, "You are cordially invited to:\n\nEvent: $event_name\nDate: $event_date\nLocation: $event_place\n\nPlease present this invitation at the entrance.", 0, 'C');

        // Sanitize filename
        $sanitized_event_name = preg_replace('/[^A-Za-z0-9\-]/', '_', $event_name);
        $filename = "../public/invitations/" . $sanitized_event_name . ".pdf";
        $pdf->Output('F', $filename);
        return $sanitized_event_name;
    }

    if (empty($errors)) {
        // Save to DB
        $event->create($name, $place, $date);

        // Generate PDF and get sanitized filename
        $sanitized_event_name = createEventInvitationPDF($name, $date, $place);

        // Build URL to the PDF
        $pdf_url = "http://localhost/events_project/public/invitations/" . $sanitized_event_name . ".pdf";
       // $qr_url = "http://http://192.168.0.1//events_project/public/invitations/" . $sanitized_event_name . ".pdf";

        // Generate QR code that links to the PDF
        $qr_path = '../public/qrcodes/' . $sanitized_event_name . '.png';
        QRcode::png($pdf_url, $qr_path);

        //echo "QR Code and Invitation PDF created successfully:<br>";
        //echo "<img src='$qr_path' alt='QR Code'>";
       
        header("Location: ../view/eventList.php");
        exit;
}
}
?>
