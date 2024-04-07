<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/credentials/redis_credentials.inc';



// Подключение к серверу Redis
$redis = new Redis();
$redis->connect(REDIS_HOST, REDIS_PORT);
$redis->auth(REDIS_PASSWORD);

// Обработка запроса
$response = array();
header('Access-Control-Allow-Origin: *');

if (isset($_GET['key']) && isset($_GET['value'])) {
    $key = $_GET['key'];
    $value = $_GET['value'];

    // Установка значения по ключу
    $redis->set($key, $value);
    $response['message'] = "Значение '$value' успешно установлено по ключу '$key'";
} elseif (isset($_GET['key'])) {
    $key = $_GET['key'];

    // Получение значения по ключу
    $value = $redis->get($key);
    if ($value !== false) {
        $response['value'] = $value;
    } else {
        $response['error'] = "Значение по ключу '$key' не найдено";
    }
} else {
    $response['error'] = "Не указан ключ";
}

// Закрываем соединение
$redis->close();

// Возвращаем ответ в формате JSON
header('Content-Type: application/json');
echo json_encode($response);

?>
