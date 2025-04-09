<?php
require_once("../config/database.php");
require_once("../model/Event.php");

if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];
    $event = new Event();
    $event->delete($event_id);
}

header("Location: ../view/eventList.php");
exit;
