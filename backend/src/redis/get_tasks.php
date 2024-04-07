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
    $tasksJson = $redis->get($taskKey);
    $tasks = json_decode($tasksJson, true);

    if ($tasks) {
        // Группируем задачи по датам
        $tasksByDate = [];
        foreach ($tasks as $task) {
            $date = $task['date'];
            if (!isset($tasksByDate[$date])) {
                $tasksByDate[$date] = [];
            }
            $tasksByDate[$date][] = $task;
        }

        // Сортируем задачи внутри каждой даты (если нужно)
        foreach ($tasksByDate as $date => &$tasksArray) {
            usort($tasksArray, function ($task1, $task2) {
                return $task1['date'] - $task2['date'];
            });
        }

        // Сортируем даты задач
        ksort($tasksByDate);

        echo json_encode($tasksByDate);
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode([]);
}

$redis->close();
?>
