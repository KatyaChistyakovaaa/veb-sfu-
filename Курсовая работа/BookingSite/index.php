<?php
session_start();

// Подключение к базе данных (замените значения на свои)
$host = 'localhost';
$db_name = 'restaurant_booking';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}
// Проверка существования сессии пользователя
if (!isset($_SESSION['user_id'])) {
    header("Location: registration.php");
    exit();
}
else{
    header("Location: dashboard.php");
    exit();
}