<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/dao/tasks.inc';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/firebase/lib/check_token.inc';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');


// Получаем токен из cookies
$token = isset($_COOKIE['idToken']) ? $_COOKIE['idToken'] : '';
$response = checkToken($token);

if (!isset($_POST['title']) || !isset($_POST['desc']) || !isset($_POST['task-date']) || !isset($_POST['task-uid']) || !isset($_POST['priority'])) {
    $path = "/home?code=400";
    header("Location: $path");
}

$tasksDao = new TasksDao();


if ($response['success']) {
    $email = $response['userDetails']['email']; // Извлекаем email пользователя
    $taskKey = "tasks:" . $email;

    // Получаем данные из POST-запроса
    $title = $_POST['title'];
    $description = $_POST['desc'];
    $date = $_POST['task-date'];
    $uid = $_POST['task-uid'];
    $priority = $_POST['priority'];
    $color = isset($_POST['color']) ? $_POST['color'] : "#F0F0F0";

    $task = [
        'uid' => $uid,
        'title' => $title,
        'description' => $description,
        'task_date' => strtotime($date),
        'priority' => $priority,
        'color' => $color,
        'user_mail' => $email,
        'status' => false
    ];

    // Добавляем задачу в список задач для пользователя
    $tasksDao->deleteTask($uid, $email);
    $tasksDao->createTask($task);

    $path = "/home?code=200";
    header("Location: $path");
} else {
    $path = "/home?code=401";
    header("Location: $path");
}

?>
