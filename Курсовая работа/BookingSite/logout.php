<?php
session_start();

// Удаление всех данных сессии
session_unset();
session_destroy();

// Перенаправление на страницу входа или другую страницу вашего выбора
header("Location: login.php");
exit();
?>
