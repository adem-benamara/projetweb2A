<?php
require_once(__DIR__ . "/../config/database.php");
require_once(__DIR__ . "/../model/Event.php");
$event = new Event();
$events = $event->getAll();
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Event List</title>
    <style>
        body {
            background-color: #f4faff;
            font-family: Arial, sans-serif;
            color: #333;
            padding: 30px;
        }

        .header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }

        .header img {
            width: 60px;
            height: 60px;
            margin-right: 15px;
        }

        h2 {
            color: #0056b3;
            margin: 0;
        }

        form {
            margin-bottom: 20px;
            padding: 15px;
            background: #ffffff;
            border-radius: 10px;
            width: 300px;
        }

        input, button {
            margin: 8px 0;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            background-color: #0056b3;
            color: white;
            border: none;
        }

        button:hover {
            background-color: #004099;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            color: white;
            text-decoration: none;
            margin: 2px;
            display: inline-block;
        }

        .btn-green { background-color: #28a745; }
        .btn-blue { background-color: #007bff; }
        .btn-red { background-color: #dc3545; }
    </style>
</head>
<body>
<!-- START: InnovConnect Banner -->
<header style="background: linear-gradient(to bottom, #0d1b4c, #2c66c6); color: white; padding: 20px 0;">
    <div style="display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: auto; padding: 0 20px;">
        <h1 style="font-size: 24px; font-weight: bold;">InterConnect</h1>
        <nav style="display: flex; gap: 20px;">
            <a href="#" style="color: white; text-decoration: none;">Entrepreneurs</a>
            <a href="#" style="color: white; text-decoration: none;">Investisseurs</a>
            <a href="#" style="color: white; text-decoration: none;">Projets</a>
            <a href="#" style="color: white; text-decoration: none;">Contact</a>
        </nav>
        <div>
            <img src="img.png" alt="Profil" style="width: 130px; height: 130px; border-radius: 0%; background-color: white; padding: 5px;">
        </div>
    </div>
    <div style="text-align: center; margin-top: 30px;">
        <h2 style="font-size: 32px; font-weight: bold;">Connecter les idées aux investisseurs</h2>
        <p style="font-size: 16px; margin-top: 10px;">Une plateforme simple pour découvrir des projets innovants et financer l'avenir.</p>
    </div>
</header>
<!-- END: InnovConnect Banner -->
<div class="header">
    <!--<img src="img.png" alt="logo">-->
    <!--<h2>Gestion des Événements</h2>-->
</div>
<div style="display: flex; justify-content: center; margin-bottom: 20px;">
<form method="post" action="../controller/EventController.php">
    <input type="text" name="events_name" placeholder="Event Name"><br>
    <input type="text" name="events_place" placeholder="Event Place"><br>
    <input type="text" name="events_date" placeholder="YYYY-MM-DD"><br>
    <button type="submit">Create Event</button>
</form>
    </div>
<?php if (!empty($_SESSION['errors'])): foreach ($_SESSION['errors'] as $error): ?>
    <p style="color:red;"><?= $error ?></p>
<?php endforeach; unset($_SESSION['errors']); endif; ?>

<h2>Liste des Événements</h2>
<table>
    <tr>
        <th>Nom</th>
        <th>Lieu</th>
        <th>Date</th>
        <th>event_id</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($events as $e): ?>
        <tr>
            <td><?= $e['event_name'] ?></td>
            <td><?= $e['event_place'] ?></td>
            <td><?= $e['event_date'] ?></td>
            <td><?= $e['event_id'] ?></td>
            <td>
                <a class="btn btn-green" href="participantForm.php?event_id=<?= $e['event_id'] ?>&event_date=<?= $e['event_date'] ?>">Participer</a>
                <a class="btn btn-blue" href="editEventForm.php?event_id=<?= $e['event_id'] ?>">Edit</a>
                <a class="btn btn-red" href="../controller/deleteEvent.php?event_id=<?= $e['event_id'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')">Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
