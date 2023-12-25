<?php
session_start();

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

// Проверка метода запроса
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bookingId'])) {
    $bookingId = $_POST['bookingId'];

    // Ваш код для отмены бронирования по $bookingId
    cancelBooking($pdo, $bookingId);
}

// Функция для отмены бронирования
function cancelBooking($pdo, $bookingId) {
    $query = "DELETE FROM reservations WHERE reservation_id = :booking_id";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':booking_id', $bookingId, PDO::PARAM_INT);
    $statement->execute();
}
?>

