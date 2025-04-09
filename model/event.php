<?php
class Event {
    public function getAll() {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM events");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    private function generateUniqueId() {
      global $pdo;
      do {
          $id = rand(1, 999999); // ID aléatoire, non nul
          $stmt = $pdo->prepare("SELECT COUNT(*) FROM events WHERE event_id = ?");
          $stmt->execute([$id]);
          $count = $stmt->fetchColumn();
      } while ($count > 0);
      return $id;
  }
  public function getById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM events WHERE event_id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function update($id, $name, $place, $date) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE events SET event_name = ?, event_place = ?, event_date = ? WHERE event_id = ?");
    $stmt->execute([$name, $place, $date, $id]);
}

    public function create($name, $place, $date) {
        global $pdo;
        $id = $this->generateUniqueId(); // generates random ID
        $stmt = $pdo->prepare("INSERT INTO events (event_id, event_name, event_place, event_date) VALUES (?, ?, ?,?)");
        $stmt->execute([$id, $name, $place, $date]);
    }

    public function delete($id) {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM events WHERE event_id = ?");
        $stmt->execute([$id]);
    }
}
?>