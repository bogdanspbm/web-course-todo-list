<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/dao/tasks.inc';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/firebase/lib/check_token.inc';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

parse_str(file_get_contents("php://input"), $_DELETE);

if (!isset($_DELETE["uid"])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'error' => 'UID is required']);
    exit;
}


$token = isset($_COOKIE['idToken']) ? $_COOKIE['idToken'] : '';
$response = checkToken($token);

if ($response['success']) {
    $email = $response['userDetails']['email']; // Извлекаем email пользователя
    $taskDao = new TasksDao();
    $taskDao->deleteTask($_DELETE["uid"], $email);

    echo json_encode(['success' => true, 'message' => 'Задача удаленна']);
} else {
    header('HTTP/1.0 401 Unauthorized');
    echo json_encode(['success' => false, 'message' => 'Не удалось авторизоваться']);
}
