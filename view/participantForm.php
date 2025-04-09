<?php
$event_id = $_GET['event_id'];
$event_date = $_GET['event_date'];
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Participant Form</title>
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

        input[type="text"], select {
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
<h2>Participate in Event</h2>
<form method="post" action="../controller/ParticipantController.php">
    <input type="hidden" name="event_id" value="<?= $event_id ?>">
    <input type="hidden" name="event_date" value="<?= $event_date ?>">
    <input type="text" name="participant_nom" placeholder="Nom"><br>
    <input type="text" name="participant_prenom" placeholder="Prénom"><br>
    <input type="text" name="age" placeholder="Age"><br>
    <select name="participant_metier">
        <option value="entrepreneur">Entrepreneur</option>
        <option value="investisseur">Investisseur</option>
    </select><br>
    <button type="submit">Submit</button>
</form>
<?php if (!empty($_SESSION['errors'])): foreach ($_SESSION['errors'] as $error): ?>
    <p style="color:red;"><?= $error ?></p>
<?php endforeach; unset($_SESSION['errors']); endif; ?>
</body>
</html>

