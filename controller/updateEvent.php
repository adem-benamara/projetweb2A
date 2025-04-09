<?php
require_once("../config/database.php");
require_once("../model/Event.php");

$event = new Event();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['event_id'];
    $name = trim($_POST['event_name']);
    $place = trim($_POST['event_place']);
    $date = $_POST['event_date'];
    $errors = [];

    if (!$name) $errors[] = "Event name required.";
    if (!$place) $errors[] = "Event place required.";
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        $errors[] = "Invalid date format.";
    } else {
        $today = date('2025-04-09');
        if ($date < $today) {
            $errors[] = "La date doit être aujourd'hui ou dans le futur.";
        }
    }

    if (empty($errors)) {
        $event->update($id, $name, $place, $date);
        header("Location: ../view/eventList.php");
    } else {
        session_start();
        $_SESSION['errors'] = $errors;
        header("Location: ../view/editEventForm.php?event_id=$id");
    }
}
