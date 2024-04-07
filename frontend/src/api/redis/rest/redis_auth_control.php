<?php

// Подключаем файл с настройками Redis
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/credentials/redis_credentials.inc';


// Подключение к серверу Redis
$redis = new Redis();
$redis->connect(REDIS_HOST, REDIS_PORT);

// Аутентификация
$redis->auth(REDIS_PASSWORD);

// Функция для добавления зарегистрированных пользователей
function addUser($email, $token) {
    global $redis;
    // Сохраняем токен пользователя в Redis под ключом, равным email
    $redis->set($email, $token);
}

// Функция для проверки, занят ли email
function isEmailTaken($email) {
    global $redis;
    // Проверяем, существует ли ключ (email) в Redis
    return $redis->exists($email);
}

// Обработка запроса
$response = array();
header('Access-Control-Allow-Origin: *');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получаем данные из POST запроса
    $email = $_POST['email'];
    $token = $_POST['token'];

    // Добавляем пользователя
    addUser($email, $token);
    $response['message'] = "Пользователь успешно добавлен.";
} elseif ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['email'])) {
    // Получаем email из GET запроса
    $email = $_GET['email'];

    // Проверяем, занят ли email
    if (isEmailTaken($email)) {
        $response['message'] = "Email уже занят.";
    } else {
        $response['message'] = "Email свободен.";
    }
} else {
    $response['error'] = "Не указан email";
}

// Возвращаем ответ в формате JSON
header('Content-Type: application/json');
echo json_encode($response);

// Закрываем соединение с Redis
$redis->close();

?>
