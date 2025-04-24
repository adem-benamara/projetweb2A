<?php

$host = 'localhost';
$db = 'project';
$username = 'root';
$password = '';


try {
    $db = new PDO("mysql:host=$host;dbname=$db", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>
