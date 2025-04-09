<?php
require_once("../config/database.php");
require_once("../model/Participant.php");

$participant = new Participant();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = trim($_POST['participant_nom']);
    $prenom = trim($_POST['participant_prenom']);
    $age = $_POST['age'];
    $metier = $_POST['participant_metier'];
    $event_id = $_POST['event_id'];
    $event_date = $_POST['event_date'];

    $errors = [];
    if (!$nom || !$prenom) $errors[] = "Name required.";
    if (!is_numeric($age) || $age < 16) $errors[] = "Valid age required.";
    if (!in_array($metier, ['entrepreneur', 'investisseur'])) $errors[] = "Valid métier required.";

    if (empty($errors)) {
        $participant->create($nom, $prenom, $age, $metier, $event_id, $event_date);
        header("Location: ../view/eventList.php");
    } else {
        session_start();
        $_SESSION['errors'] = $errors;
        header("Location: ../view/participantForm.php?event_id=$event_id&event_date=$event_date");
    }
}
?>