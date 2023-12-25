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
    header("Location: login.php");
    exit();
}

// Получение списка столов для выбора
$stmtTables = $pdo->prepare("SELECT table_id, capacity FROM tables");
$stmtTables->execute();
$tables = $stmtTables->fetchAll(PDO::FETCH_ASSOC);

// Обработка формы бронирования
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $guests = $_POST['guests'];

    // Поиск подходящего стола в зависимости от количества гостей
    $stmtFindTable = $pdo->prepare("SELECT table_id FROM tables WHERE capacity >= ? ORDER BY capacity ASC LIMIT 1");
    $stmtFindTable->execute([$guests]);
    $table = $stmtFindTable->fetch(PDO::FETCH_ASSOC);

    if (!$table) {
        die("Извините, нет подходящего стола для указанного количества гостей.");
    }

    $table_id = $table['table_id'];

    // Проверка доступности выбранного времени и стола (здесь можно добавить дополнительные проверки)
    $stmtCheckAvailability = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE reservation_date = ? AND reservation_time = ? AND table_id = ?");
    $stmtCheckAvailability->execute([$date, $time, $table_id]);
    $availabilityCount = $stmtCheckAvailability->fetchColumn();

    if ($availabilityCount > 0) {
        die("Выбранное время или стол уже заняты. Пожалуйста, выберите другое время или стол.");
    }

    // Вставка записи о бронировании в базу данных
    $stmtInsertReservation = $pdo->prepare("INSERT INTO reservations (user_id, table_id,guests, reservation_date, reservation_time) VALUES (?, ?, ?, ?, ?)");
    $stmtInsertReservation->execute([$user_id, $table_id,$guests, $date, $time ]);

    // Редирект после успешного бронирования
    header("Location: dashboard.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="booking.css">
    <title>Бронирование столика</title>
</head>
<body>
<div class="user-info">
    <?php
    // Проверка существования сессии пользователя
    if (isset($_SESSION['username'])) {
        echo 'Привет, ' .'<a href="dashboard.php">'.htmlspecialchars($_SESSION['username']).'</a>'. '!';
        echo '<br><a href="logout.php" class="logout-button">Выход</a>';
    }
    ?>
</div>
<div class="container">
    <h2>Бронирование столика</h2>
    <form action="booking.php" method="post">
        <label for="date">Дата:</label>
        <input type="date" name="date" required><br>

        <label for="time">Время:</label>
        <input type="time" name="time" required><br>

        <label for="guests">Количество гостей:</label>
        <input type="number" name="guests" min="1" required><br>

        <button type="submit">Забронировать</button>
    </form>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var bookingForm = document.getElementById("bookingForm");
        var bookingButton = document.getElementById("bookingButton");

        // Проверка наличия авторизации
        var isUserAuthenticated = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;

        // Установка состояния кнопки в зависимости от авторизации
        bookingButton.disabled = !isUserAuthenticated;
    });
</script>
</body>
</html>
