<?php
require_once("../config/database.php");
require_once("../model/Event.php");
session_start();

$event = new Event();
$event_id = $_GET['event_id'];
$event_data = $event->getById($event_id);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Modifier l'événement</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f8ff;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 400px;
            margin: 50px auto;
            background-color: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        h2 {
            text-align: center;
            color: #007BFF;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-sizing: border-box;
        }

        button {
            background-color: #007BFF;
            color: white;
            padding: 12px;
            width: 100%;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
            text-align: center;
        }

    </style>
</head>
<body>
<h2>Modifier l'Événement</h2>
<form method="post" action="../controller/updateEvent.php">
    <input type="hidden" name="event_id" value="<?= $event_data['event_id'] ?>">
    <input type="text" name="event_name" value="<?= $event_data['event_name'] ?>"><br>
    <input type="text" name="event_place" value="<?= $event_data['event_place'] ?>"><br>
    <input type="text" name="event_date" value="<?= $event_data['event_date'] ?>"><br>
    <button type="submit">Enregistrer</button>
</form>
<?php if (!empty($_SESSION['errors'])): foreach ($_SESSION['errors'] as $error): ?>
    <p style="color:red;"><?= $error ?></p>
<?php endforeach; unset($_SESSION['errors']); endif; ?>
</body>
</html>
