-- Создание базы данных
CREATE DATABASE IF NOT EXISTS restaurant_booking CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Использование базы данных
USE restaurant_booking;

-- Таблица ролей
CREATE TABLE IF NOT EXISTS roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name ENUM('user', 'admin') NOT NULL
) CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Таблица пользователей
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    role_id INT,
    unique (email),
    FOREIGN KEY (role_id) REFERENCES roles(role_id)
) CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Таблица столиков
CREATE TABLE IF NOT EXISTS tables (
    table_id INT AUTO_INCREMENT PRIMARY KEY,
    table_number INT NOT NULL,
    capacity INT NOT NULL,
    UNIQUE (table_number)
) CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Таблица бронирований
CREATE TABLE IF NOT EXISTS reservations (
    reservation_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    table_id INT,
    guests INT,
    reservation_date DATE NOT NULL,
    reservation_time TIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (table_id) REFERENCES tables(table_id)
) CHARACTER SET utf8 COLLATE utf8_general_ci;
