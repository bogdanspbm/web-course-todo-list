<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/credentials/redis_credentials.inc';

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/firebase/lib/check_token.inc';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

if (!isset($_POST["uid"])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'error' => 'UID is required']);
    exit;
}

if (!isset($_POST["status"])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'error' => 'Status is required']);
    exit;
}

$redis = new Redis();
$redis->connect(REDIS_HOST, REDIS_PORT);
$redis->auth(REDIS_PASSWORD);

// Получаем токен из cookies
$token = isset($_COOKIE['idToken']) ? $_COOKIE['idToken'] : '';
$response = checkToken($token);

if ($response['success']) {
    $email = $response['userDetails']['email']; // Извлекаем email пользователя
    $taskKey = "completion:" . $email . ":" . $_POST["uid"];
    $redis->set($taskKey, $_POST["status"] != "false");
    echo json_encode(['success' => true, 'message' => 'Задача успешно удалена', 'key' => $taskKey, 'value' => $_POST["status"] != "false"]);
} else {
    header('HTTP/1.0 401 Unauthorized');
    echo json_encode(['success' => false, 'message' => 'Не удалось авторизоваться']);
}

