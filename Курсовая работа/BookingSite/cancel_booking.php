<?php
session_start();

// Проверка существования сессии пользователя
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Проверка метода запроса
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

    // Получаем ID бронирования из POST-запроса
    $bookingId = isset($_POST['bookingId']) ? intval($_POST['bookingId']) : 0;

    // Ваш код для проверки, принадлежит ли бронирование текущему пользователю
    $userId = $_SESSION['user_id'];
    if (isBookingBelongsToUser($pdo, $bookingId, $userId)) {
        // Если бронирование принадлежит пользователю, удаляем его
        cancelBooking($pdo, $bookingId);
        echo "Бронирование успешно отменено.";
    } else {
        echo "Ошибка: Бронирование не принадлежит текущему пользователю.";
    }
} else {
    // Если запрос не является POST-запросом, перенаправляем пользователя
    header("Location: dashboard.php");
    exit();
}

// Функция для проверки, принадлежит ли бронирование пользователю
function isBookingBelongsToUser($pdo, $bookingId, $userId) {
    $query = "SELECT COUNT(*) FROM reservations WHERE reservation_id = :booking_id AND user_id = :user_id";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':booking_id', $bookingId, PDO::PARAM_INT);
    $statement->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $statement->execute();

    return $statement->fetchColumn() > 0;
}

// Функция для отмены бронирования
function cancelBooking($pdo, $bookingId) {
    $query = "DELETE FROM reservations WHERE reservation_id = :booking_id";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':booking_id', $bookingId, PDO::PARAM_INT);
    $statement->execute();
}

?>

