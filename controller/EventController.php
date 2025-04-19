<?php
require_once("../config/database.php");
require_once("../model/Event.php");

$event = new Event();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['events_name']);
    $place = trim($_POST['events_place']);
    $date = $_POST['events_date'];
    $errors = [];

    // Contrôles de saisie
    //if (!$name) $errors[] = "Event name required.";
    //if (!$place) $errors[] = "Event place required.";

    // Format YYYY-MM-DD
    //if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
       // $errors[] = "Invalid date format.";
    //} else {
        ////$today = date('2025-04-09'); // Date actuelle
        //if ($date < $today) {
            //$errors[] = "La date doit être aujourd'hui ou dans le futur.";
        //}
    //}

    if (empty($errors)) {
        $event->create($name, $place, $date);
        header("Location: ../view/eventList.php");
    } else {
        session_start();
        $_SESSION['errors'] = $errors;
        header("Location: ../view/eventList.php");
    }
}

?>