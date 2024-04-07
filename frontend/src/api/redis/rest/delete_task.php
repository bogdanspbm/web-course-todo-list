<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/credentials/redis_credentials.inc';

require_once '../firebase/check_token.inc';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$redis = new Redis();
$redis->connect(REDIS_HOST, REDIS_PORT);
$redis->auth(REDIS_PASSWORD);

// Получаем токен из cookies
$token = isset($_COOKIE['idToken']) ? $_COOKIE['idToken'] : '';
$response = checkToken($token);

if ($response['success'] && isset($_POST['taskIndex'])) {
    $email = $response['userDetails']['email']; // Извлекаем email пользователя
    $taskKey = "tasks:" . $email;
    $tasks = json_decode($redis->get($taskKey), true);

    if ($tasks && isset($tasks[$_POST['taskIndex']])) {
        array_splice($tasks, $_POST['taskIndex'], 1); // Удаляем задачу из списка
        $redis->set($taskKey, json_encode($tasks)); // Обновляем список задач в Redis

        echo json_encode(['success' => true, 'message' => 'Задача успешно удалена']);
    } else {
        echo json_encode(['error' => 'Задача не найдена']);
    }
} else {
    echo json_encode(['error' => 'Не удалось аутентифицировать пользователя или не указан индекс задачи']);
}

$redis->close();
?>
