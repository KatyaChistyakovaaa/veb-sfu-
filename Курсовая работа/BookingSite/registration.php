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
// Обработка формы регистрации
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Проверка пароля
    if ($password !== $confirm_password) {
        die("Пароли не совпадают");
    }

    // Хеширование пароля
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Проверка уникальности адреса электронной почты
    $stmtCheckEmail = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmtCheckEmail->execute([$email]);
    $emailCount = $stmtCheckEmail->fetchColumn();

    if ($emailCount > 0) {
        die("Адрес электронной почты уже занят");
    }

    // Получение идентификатора роли "user"
    $stmtRole = $pdo->prepare("SELECT role_id FROM roles WHERE role_name = 'user'");
    $stmtRole->execute();
    $roleId = $stmtRole->fetchColumn();

    // Вставка пользователя в базу данных с ролью "user"
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $email, $hashed_password, $roleId]);

    // Редирект после успешной регистрации
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Регистрация</title>
    <link rel="stylesheet" href="registration.css">

</head>
<body>
<div class="container">
    <h2>Регистрация</h2>
    <form action="registration.php" method="post" onsubmit="return validateForm()">
        <label for="username">Имя пользователя:</label>
        <input type="text" name="username" required><br>

        <label for="email">Электронная почта:</label>
        <input type="email" name="email" required><br>

        <label for="password">Пароль:</label>
        <input type="password" name="password" id="password" required>
        <span id="passwordError" class="error"></span><br>

        <label for="confirm_password">Подтвердите пароль:</label>
        <input type="password" name="confirm_password" id="confirm_password" required>
        <span id="confirmPasswordError" class="error"></span><br>

        <button type="submit">Зарегистрироваться</button>
        <p>Уже есть аккаунт? <a href="login.php">Авторизируйтесь</a></p>
    </form>
</div>

<script>
    function validateForm() {
        var username = document.getElementsByName("username")[0].value;
        var email = document.getElementsByName("email")[0].value;
        var password = document.getElementById("password").value;
        var confirmPassword = document.getElementById("confirm_password").value;

        // Регулярное выражение для проверки адреса электронной почты
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailRegex.test(email)) {
            alert("Пожалуйста, введите корректный адрес электронной почты.");
            return false;
        }

        // Регулярное выражение для проверки пароля
        var passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/;

        if (!passwordRegex.test(password)) {
            document.getElementById("passwordError").innerHTML = "Пароль должен содержать минимум 8 символов, включая буквы и цифры.";
            return false;
        } else {
            document.getElementById("passwordError").innerHTML = "";
        }

        if (password !== confirmPassword) {
            document.getElementById("confirmPasswordError").innerHTML = "Пароли не совпадают.";
            return false;
        } else {
            document.getElementById("confirmPasswordError").innerHTML = "";
        }
        checkEmailUnique(email);
        return true;
    }
    function checkEmailUnique(email) {
        $.ajax({
            type: 'POST',
            url: 'check_email.php',
            data: { email: email },
            success: function (response) {
                if (response === 'exists') {
                    document.getElementById("emailError").innerHTML = "Этот email уже зарегистрирован.";
                } else {
                    document.getElementById("emailError").innerHTML = "";
                }
            }
        });
    }
</script>

</body>
</html>
