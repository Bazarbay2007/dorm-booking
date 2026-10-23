<?php
// Хостингке жүктегенде мәндерді өзгерт (InfinityFree панеліндегі деректер)
define('BASE', '');  // XAMPP-та қалтада тұрса: '/dorm-booking'
$DB_HOST = 'localhost';
$DB_NAME = 'dorm_booking';
$DB_USER = 'root';
$DB_PASS = '';

session_start();
require_once __DIR__ . '/includes/functions.php';
try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $ex) {
    die('Дерекқорға қосылу қатесі.');
}
