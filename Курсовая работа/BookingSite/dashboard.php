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

// Проверка существования сессии пользователя
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Определение роли пользователя


// Получение информации о пользователе
$userId = $_SESSION['user_id'];
$userInfo = getUserInfo($pdo, $userId);
$roleName = getRoleName($pdo, $userInfo['role_id']);  // Замените 'role_id' на имя столбца с ролями в вашей таблице пользователей
function getAllUsers($pdo) {
    $query = "SELECT user_id, username FROM users";
    $statement = $pdo->prepare($query);
    $statement->execute();

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}
// Функция для получения подробной информации о пользователе и его бронях

// Функция для удаления пользователя
function deleteUser($pdo, $userId) {
    $query = "DELETE FROM users WHERE user_id = :user_id";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':user_id', $userId, PDO::PARAM_INT);

    if ($statement->execute()) {
        echo "Пользователь успешно удален";
    } else {
        echo "Ошибка при удалении пользователя";
    }
}
function cancelBooking($pdo, $bookingId) {
    $query = "DELETE FROM reservations WHERE reservation_id = :reservation_id";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':reservation_id', $bookingId, PDO::PARAM_INT);
    $statement->execute();
}
function changeUsername($pdo, $userId, $newUsername) {
    $query = "UPDATE users SET username = :username WHERE user_id = :user_id";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':username', $newUsername, PDO::PARAM_STR);
    $statement->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $statement->execute();
}
function getUserInfoAndBookings($pdo, $userId) {
    $userInfo = getUserInfo($pdo, $userId);
    $userBookings = getUserBookings($pdo, $userId);

    return [
        'user_info' => $userInfo,
        'bookings' => $userBookings,
    ];
}


function getRoleName($pdo, $roleId) {
    $query = "SELECT role_name FROM Roles WHERE role_id = :role_id";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':role_id', $roleId, PDO::PARAM_INT);
    $statement->execute();

    $result = $statement->fetch(PDO::FETCH_ASSOC);

    return $result ? $result['role_name'] : 'user'; // Возвращаем 'user', если роль не найдена
}
function getUserBookings($pdo, $userId) {
    $query = "SELECT * FROM reservations WHERE user_id = :user_id";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $statement->execute();

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}
function getUserInfo($pdo, $userId) {
    $query = "SELECT * FROM users WHERE user_id = :user_id";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $statement->execute();

    return $statement->fetch(PDO::FETCH_ASSOC);
}

// Проверка метода запроса
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Ваш код для обработки действия
    if ($_POST['action'] === 'updateUsername') {
        $newUsername = isset($_POST['newUsername']) ? $_POST['newUsername'] : '';
        updateUsername($pdo, $userId, $newUsername);
    } elseif ($_POST['action'] === 'getUserInfoAndBookings') {
        $userId = isset($_POST['userId']) ? $_POST['userId'] : 0;
        $userInfo = getUserInfoAndBookings($pdo, $userId);
        echo json_encode($userInfo);
        exit();
    } elseif ($_POST['action'] === 'updateUserInfo') {
        $userId = isset($_POST['userId']) ? $_POST['userId'] : 0;
        $newEmail = isset($_POST['newEmail']) ? $_POST['newEmail'] : '';
        $newRole = isset($_POST['newRole']) ? $_POST['newRole'] : '';
        updateUserInfo($pdo, $userId, $newEmail, $newRole);
    }elseif ($_POST['action'] === 'deleteUser') {
        $deleteUserId = isset($_POST['deleteUserId']) ? $_POST['deleteUserId'] : 0;
        deleteUser($pdo, $deleteUserId);
    }elseif ($_POST['action'] === 'changeUserRole') {
        $userId = isset($_POST['userId']) ? $_POST['userId'] : 0;
        $newRole = isset($_POST['newRole']) ? $_POST['newRole'] : '';
        changeUserRole($pdo, $userId, $newRole);
    }elseif ($_POST['action'] === 'changeUsername') {
        $userId = isset($_POST['userId']) ? $_POST['userId'] : 0;
        $newUsername = isset($_POST['newUsername']) ? $_POST['newUsername'] : '';
        changeUsername($pdo, $userId, $newUsername);
    }elseif ($_POST['action'] === 'cancelBooking') {
        $bookingId = isset($_POST['bookingId']) ? $_POST['bookingId'] : 0;
        cancelBooking($pdo, $bookingId);
    }elseif ($_POST['action'] === 'updateBooking') {
        $reservationId = isset($_POST['reservationId']) ? $_POST['reservationId'] : 0;
        $newDate = isset($_POST['newDate']) ? $_POST['newDate'] : '';
        $newTime = isset($_POST['newTime']) ? $_POST['newTime'] : '';
        $newGuests = isset($_POST['newGuests']) ? $_POST['newGuests'] : '';
        updateBooking($pdo, $reservationId, $newDate, $newTime, $newGuests);
    }
}
function updateBooking($pdo, $reservationId, $newDate, $newTime, $newGuests) {
    $query = "UPDATE reservations SET reservation_date = :newDate, reservation_time = :newTime, guests = :newGuests WHERE reservation_id = :reservationId";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':newDate', $newDate, PDO::PARAM_STR);
    $statement->bindParam(':newTime', $newTime, PDO::PARAM_STR);
    $statement->bindParam(':newGuests', $newGuests, PDO::PARAM_INT);
    $statement->bindParam(':reservationId', $reservationId, PDO::PARAM_INT);
    $statement->execute();
}
function changeUserRole($pdo, $userId, $newRole) {
    $query = "UPDATE users SET role_id = :role_id WHERE user_id = :user_id";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':role_id', $newRole, PDO::PARAM_STR);
    $statement->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $statement->execute();
}

// Функция для обновления информации о пользователе
function updateUserInfo($pdo, $userId, $newEmail, $newRole) {
    $query = "UPDATE users SET email = :email, role_id = :role_id WHERE user_id = :user_id";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':email', $newEmail, PDO::PARAM_STR);
    $statement->bindParam(':role_id', $newRole, PDO::PARAM_STR);
    $statement->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $statement->execute();
}



function updateUsername($pdo, $userId, $newUsername) {
    $query = "UPDATE users SET username = :username WHERE user_id = :user_id";
    $statement = $pdo->prepare($query);
    $statement->bindParam(':username', $newUsername, PDO::PARAM_STR);
    $statement->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $statement->execute();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="dashboard.css">
    <title>Панель пользователя</title>
</head>
<body>
<style>
    label{
        color: white;
    }
    #userInfoDetails{
        color: #333333;
    }
</style>
<h1>Панель пользователя</h1>

<h2>Привет, <?php echo $userInfo['username']; ?>!</h2>

<!-- Информация о пользователе -->
<h2>Ваш профиль:</h2>
<p>ID: <?php echo $userInfo['user_id']; ?></p>
<p>Email: <?php echo $userInfo['email']; ?></p>
<p>Имя: <?php echo $userInfo['username']; ?>
    <!-- Добавляем кнопку "Изменить" и форму для изменения имени -->
    <button onclick="showUpdateUsernameForm()">Изменить</button>
<form id="updateUsernameForm" style="display: none;">
    <label for="newUsername">Новое имя пользователя:</label>
    <input type="text" name="newUsername" required>
    <button type="button" onclick="updateUsername()">Сохранить</button>
</form>
</p>
<p>Роль: <?php echo ucfirst($roleName); ?></p>



<!-- Ваш код для отображения бронирований пользователя -->

<!-- Форма для отмены бронирования -->
<!-- Список бронирований пользователя -->
<h2>Ваши бронирования:</h2>

<?php
// Получаем бронирования пользователя из базы данных
$userBookings = getUserBookings($pdo, $_SESSION['user_id']);

if ($userBookings) {
    foreach ($userBookings as $booking) {
        ?>
        <div>
            <p>Бронь ID: <?php echo $booking['reservation_id']; ?></p>
            <p>Кол-во гостей: <?php echo $booking['guests']?></p>
            <p>Дата и время: <?php echo $booking['reservation_date'] . ' ' . $booking['reservation_time']; ?></p>
            <!-- Добавляем кнопку для отмены бронирования -->
            <form action="cancel_booking.php" method="post">
                <input type="hidden" name="bookingId" value="<?php echo $booking['reservation_id']; ?>">
                <button type="submit">Отменить бронь</button>
            </form>
        </div>
        <?php
    }
} else {
    echo "<p>У вас нет активных бронирований.</p>";
}
?>
<?php if ($roleName === 'admin'): ?>
    <h2>Список всех пользователей:</h2>
    <input type="text" id="userSearch" placeholder="Поиск по именам" oninput="searchUsers()">
    <ul id="userList">
        <?php
        // Получаем список всех пользователей из базы данных
        $allUsers = getAllUsers($pdo);

        foreach ($allUsers as $user) {
            echo '<li class="userListItem" onclick="showUserInfo(' . $user['user_id'] . ')">' . $user['username'] . '</li>';
        }
        ?>
    </ul>
    <h2>Удаление пользователя:</h2>

        <div id="userInfoModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeUserInfoModal()">&times;</span>
            <h2 id="userInfoTitle"></h2>
            <div id="userInfoDetails"></div>
            <form id="updateBookingForm" style="display: none;">
                <label for="newDate">Новая дата:</label>
                <input type="date" name="newDate" id="newDate" required>
                <label for="newTime">Новое время:</label>
                <input type="time" name="newTime" id="newTime" required>
                <label for="newGuests">Новое количество гостей:</label>
                <input type="number" name="newGuests" id="newGuests" required>
                <button type="button" id="updateBookingButton" onclick="updateBooking()">Сохранить изменения</button>
            </form>
            <form id="changeRoleForm">
                <label for="newRole">Новая роль:</label>
                <input type="text" name="newRole" id="newRole" required>
                <button type="button" onclick="changeUserRole(<?php echo $userId; ?>)">Изменить роль</button>
            </form>

            <h3>Изменение имени пользователя:</h3>
            <form id="changeUsernameForm">
                <label for="newUsername">Новое имя пользователя:</label>
                <input type="text" name="newUsername" id="newUsername" required>
                <button type="button" onclick="changeUsername()">Изменить имя</button>
            </form>

        </div>
    </div>
<?php endif; ?>
<a href="logout.php" class="logout-button">Выход</a>
<a href="booking.php" class="booking-link">Забронировать столик</a>
<script>
    var currentUserId;
    // Функция для отображения формы для изменения имени
    function showUpdateUsernameForm() {
        var updateUsernameForm = document.getElementById('updateUsernameForm');
        updateUsernameForm.style.display = 'block';
    }

    // Функция для отправки AJAX-запроса на обновление имени
    function updateUsername() {
        var newUsername = document.getElementById('updateUsernameForm').elements.newUsername.value;

        // Отправка AJAX-запроса
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'dashboard.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Обновляем страницу после успешного обновления имени
                location.reload();
            }
        };
        xhr.send('action=updateUsername&newUsername=' + newUsername);
    }


    function changeUsername() {
        var newUsername = document.getElementById('newUsername').value;

        // Отправка AJAX-запроса
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'dashboard.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Обновляем информацию в модальном окне после успешного изменения имени
                showUserInfo(currentUserId);
            }
        };
        xhr.send('action=changeUsername&userId=' + currentUserId + '&newUsername=' + newUsername);
    }
    // Функция для удаления пользователя
    function deleteUser() {
        // Подтверждение удаления с помощью встроенной функции confirm
        if (confirm("Вы уверены, что хотите удалить этого пользователя?")) {
            // Отправка AJAX-запроса на удаление пользователя
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'dashboard.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Обновляем страницу после успешного удаления пользователя
                    location.reload();
                }
            };
            xhr.send('action=deleteUser&deleteUserId=' + currentUserId);
        }
    }
    // Функция для фильтрации списка пользователей при вводе в поисковую строку
    function searchUsers() {
        var input, filter, ul, li, a, i, txtValue;
        input = document.getElementById('userSearch');
        filter = input.value.toUpperCase();
        ul = document.getElementById('userList');
        li = ul.getElementsByTagName('li');

        // Проходим по каждому элементу списка и скрываем тех, кто не соответствует поисковому запросу
        for (i = 0; i < li.length; i++) {
            a = li[i];
            txtValue = a.textContent || a.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                li[i].style.display = '';
            } else {
                li[i].style.display = 'none';
            }
        }
    }
    // Функция для отображения подробной информации о пользователе
    // Функция для отображения подробной информации о пользователе

    function updateUserInfo(userId) {
        var newEmail = document.getElementById('newEmail').value;
        var newRole = document.getElementById('newRole').value;

        // Отправка AJAX-запроса на обновление информации о пользователе
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'dashboard.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Обновляем страницу после успешного обновления информации
                location.reload();
            }
        };
        xhr.send('action=updateUserInfo&userId=' + userId + '&newEmail=' + newEmail + '&newRole=' + newRole);
    }
    // Функция для отмены брони
    function cancelBooking(reservationId) {
        // Отправка AJAX-запроса
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'cancel_booking_for_user.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Обновляем информацию в модальном окне после успешной отмены брони
                showUserInfo(currentUserId);
            }
        };
        xhr.send('bookingId=' + reservationId);
    }

    function changeUserRole() {
        var newRole = document.getElementById('newRole').value;

        // Отправка AJAX-запроса
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'dashboard.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Обновляем информацию в модальном окне после успешного изменения роли
                showUserInfo(currentUserId);
            }
        };
        xhr.send('action=changeUserRole&userId=' + currentUserId + '&newRole=' + newRole);
    }
    function showUserInfo(userId) {
        currentUserId = userId;
        // Отправка AJAX-запроса для получения информации о пользователе
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'dashboard.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Парсинг JSON-ответа и отображение информации в модальном окне
                var userInfo = JSON.parse(xhr.responseText);
                displayUserInfoModal(userInfo);
            }
        };
        xhr.send('action=getUserInfoAndBookings&userId=' + userId);
    }
    // Функция для отображения информации о пользователе
    function displayUserInfoModal(userInfo) {
        var modal = document.getElementById('userInfoModal');
        var title = document.getElementById('userInfoTitle');
        var details = document.getElementById('userInfoDetails');

        // Отобразить информацию в модальном окне
        title.innerHTML = 'Бронирования пользователя: ' + userInfo.user_info.username;
        details.innerHTML = '';
        // Отобразить основную информацию о пользователе
        details.innerHTML += '<h3>Основная информация:</h3>';
        details.innerHTML += '<p>ID: ' + userInfo.user_info.user_id + '</p>';
        details.innerHTML += '<p>Email: ' + userInfo.user_info.email + '</p>';
        details.innerHTML += '<p>Имя: ' + userInfo.user_info.username + '</p>';
        details.innerHTML += '<p>Роль: ' + userInfo.user_info.role_id + '</p>';
        details.innerHTML += '<button onclick="deleteUser()">Удалить пользователя</button>';
        if (userInfo.bookings.length > 0) {
            details.innerHTML += '<h3>Информация о бронированиях:</h3>';

            userInfo.bookings.forEach(function (booking) {
                details.innerHTML += '<p>Бронь ID: ' + booking.reservation_id + '</p>';
                details.innerHTML += '<p>Количество гостей: ' + booking.guests + '</p>';
                details.innerHTML += '<p>Дата и время: ' + booking.reservation_date + ' ' + booking.reservation_time + '</p>';
                details.innerHTML += '<button onclick="cancelBooking(' + booking.reservation_id + ')">Отменить бронь</button>';

                // Добавляем кнопки для изменения даты и времени брони, а также количества гостей
                details.innerHTML += '<button onclick="showUpdateBookingForm(' + booking.reservation_id + ')">Изменить бронь</button>';

                details.innerHTML += '<hr>';
            });
        } else {
            details.innerHTML += '<p>Пользователь не сделал бронирований.</p>';
        }

        // Отобразить модальное окно
        modal.style.display = 'block';
    }
    // Функция для отображения формы изменения брони
    function showUpdateBookingForm(reservationId) {
        var updateBookingForm = document.getElementById('updateBookingForm');
        var updateBookingButton = document.getElementById('updateBookingButton');
        updateBookingButton.setAttribute('data-reservation-id', reservationId);  // Устанавливаем атрибут с ID бронирования
        updateBookingForm.style.display = 'block';
    }

    // Функция для отправки AJAX-запроса на изменение брони
    function updateBooking() {
        var reservationId = document.getElementById('updateBookingButton').getAttribute('data-reservation-id');
        var newDate = document.getElementById('newDate').value;
        var newTime = document.getElementById('newTime').value;
        var newGuests = document.getElementById('newGuests').value;

        // Отправка AJAX-запроса
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'dashboard.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Обновляем информацию в модальном окне после успешного изменения брони
                showUserInfo(currentUserId);
            }
        };
        xhr.send('action=updateBooking&reservationId=' + reservationId + '&newDate=' + newDate + '&newTime=' + newTime + '&newGuests=' + newGuests);
    }



    // Функция для закрытия модального окна с информацией о пользователе
    function closeUserInfoModal() {
        var modal = document.getElementById('userInfoModal');
        modal.style.display = 'none';
    }
</script>
</body>
</html>

