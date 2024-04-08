<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/dao/tasks.inc';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/firebase/lib/check_token.inc';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');


// Получаем токен из cookies
$token = isset($_COOKIE['idToken']) ? $_COOKIE['idToken'] : '';
$response = checkToken($token);

if (!isset($_GET['uid'])) {
    $path = "/home?code=400";
    header("Location: $path");
}

$tasksDao = new TasksDao();
if ($response['success']) {
    $email = $response['userDetails']['email']; // Извлекаем email пользователя
    $tasksDao->deleteTask($_GET['uid'], $email);
    $path = "/home?code=200";
    header("Location: $path");
} else {
    $path = "/home?code=401";
    header("Location: $path");
}

?>
