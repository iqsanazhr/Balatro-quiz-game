<?php
$host = 'localhost';
$dbname = 'balatro_web';
$username = 'root';
$password = ''; // Default XAMPP/WAMP password. Change if needed.

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $db_connected = true;
} catch (PDOException $e) {
    $db_connected = false;
    $db_error_msg = $e->getMessage();
}
?>