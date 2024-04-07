<?php

require_once '../credentials/redis_credentials.inc';
require_once '../firebase/check_token.inc';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Подключение к серверу Redis
$redis = new Redis();
$redis->connect(REDIS_HOST, REDIS_PORT);
$redis->auth(REDIS_PASSWORD);


// Получаем токен из cookies
$token = isset($_COOKIE['idToken']) ? $_COOKIE['idToken'] : '';
$response = checkToken($token);

if (!isset($_POST['title']) || !isset($_POST['description']) || !isset($_POST['date']) || !isset($_POST['task-uid']) || !isset($_POST['priority'])) {
    $path = "/home?code=400";
    header("Location: $path");
}


if ($response['success']) {
    $email = $response['userDetails']['email']; // Извлекаем email пользователя
    $taskKey = "tasks:" . $email;

    // Получаем данные из POST-запроса
    $title = $_POST['title'];
    $description = $_POST['desc'];
    $date = $_POST['date'];
    $uid = $_POST['task-uid'];
    $priority = $_POST['priority'];
    $color = isset($_POST['color']) ? $_POST['color'] : "#F0F0F0";

    $task = [
        'uid' => $uid,
        'title' => $title,
        'description' => $description,
        'date' => strtotime($date),
        'priority' => $priority,
        'color' => $color,
        'status' => false
    ];

    // Добавляем задачу в список задач для пользователя
    $existingTasks = json_decode($redis->get($taskKey), true);
    if (!$existingTasks) {
        $existingTasks = [];
    }
    $existingTasks[] = $task;
    $redis->set($taskKey, json_encode($existingTasks));

    $path = "/home?code=200";
    header("Location: $path");
} else {
    $path = "/home?code=401";
    header("Location: $path");
}

$redis->close();
?>
