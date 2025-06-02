<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'npc_livestock');

// Настройки сайта
define('SITE_NAME', 'Научно-Практический Центр НАН по животноводству');
define('SITE_URL', 'http://localhost:3000');

// Инициализация сессии
session_start();

// Подключение к БД
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES utf8");
} catch(PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Функция для защиты от XSS
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
?>