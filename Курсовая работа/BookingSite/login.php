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
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
// Обработка формы авторизации
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Получение пользователя по электронной почте
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Проверка пароля и роли
    if ($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Редирект после успешной авторизации
        header("Location: booking.php");
        exit();
    } else {

    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Авторизация</title>
</head>
<body>
<div class="container">
    <h2>Авторизация</h2>
    <form action="login.php" method="post">
        <label for="email">Электронная почта:</label>
        <input type="email" name="email" required><br>

        <label for="password">Пароль:</label>
        <input type="password" name="password" required><br>

        <button type="submit">Войти</button>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($user)) {
            echo '<div class="error">Неверные электронная почта или пароль.</div>';
        }
        ?>
        <p>Нет аккаунта? <a href="registration.php">Зарегистрируйтесь</a></p>
    </form>
</div>
</body>
</html>
