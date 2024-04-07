<?php

require_once '../credentials/redis_credentials.inc';
require_once '../firebase/check_token.inc';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$redis = new Redis();
$redis->connect(REDIS_HOST, REDIS_PORT);
$redis->auth(REDIS_PASSWORD);

// Получаем токен из cookies
$token = isset($_COOKIE['idToken']) ? $_COOKIE['idToken'] : '';
$response = checkToken($token);

if ($response['success']) {
    $email = $response['userDetails']['email']; // Извлекаем email пользователя
    $taskKey = "tasks:" . $email;
    $tasks = $redis->get($taskKey);

    if ($tasks) {
        echo $tasks; // Возвращаем список задач в формате JSON
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode([]);
}

$redis->close();
?>
