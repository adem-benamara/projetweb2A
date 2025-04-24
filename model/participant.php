<?php
class Participant {
    public function create($nom, $prenom, $age, $metier, $event_id ) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO participant (participant_nom, participant_prenom, age, participant_metier,event_id) VALUES (?, ?, ?, ?,?)");
        $stmt->execute([$nom, $prenom, $age, $metier,$event_id]);
    }
}
?>