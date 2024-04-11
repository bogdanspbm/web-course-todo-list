<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/dao/tasks.inc';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/firebase/lib/check_token.inc';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Получаем токен из cookies
$token = isset($_COOKIE['idToken']) ? $_COOKIE['idToken'] : '';
$response = checkToken($token);

$requiredFields = ['title', 'desc', 'task-date', 'task-uid', 'priority'];
$missingFields = [];

foreach ($requiredFields as $field) {
    if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
        $missingFields[] = $field;
    }
}

if (!empty($missingFields)) {
    $missingFieldsList = implode(', ', $missingFields);
    $path = "/home?code=400&missing=" . urlencode($missingFieldsList);
    header("Location: $path");
    exit;
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

    // Обработка файла, если он был загружен
    $fileData = null;
    $fileName = null;
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $fileName = $_FILES['file']['name'];
        $fileData = file_get_contents($_FILES['file']['tmp_name']);
    }

    $task = [
        'uid' => $uid,
        'title' => $title,
        'description' => $description,
        'task_date' => strtotime($date),
        'priority' => $priority,
        'color' => $color,
        'user_mail' => $email,
        'status' => false,
        'file_name' => $fileName,
        'file_data' => $fileData
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
