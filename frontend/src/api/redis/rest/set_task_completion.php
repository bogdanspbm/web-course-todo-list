<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/credentials/redis_credentials.inc';

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/firebase/lib/check_token.inc';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: PUT');



$method = $_SERVER['REQUEST_METHOD'];
if ('PUT' === $method) {
    parse_str(file_get_contents('php://input'), $_PUT);
    var_dump($_PUT); //$_PUT contains put fields
}

if (!isset($_PUT["uid"])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'error' => 'UID is required']);
    exit;
}

if (!isset($_PUT["status"])) {
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
    $taskKey = "completion:" . $email . ":" . $_PUT["uid"];
    $redis->set($taskKey, $_PUT["status"] != "false");
    echo json_encode(['success' => true, 'message' => 'Задача успешно удалена', 'key' => $taskKey, 'value' => $_PUT["status"] != "false"]);
} else {
    header('HTTP/1.0 401 Unauthorized');
    echo json_encode(['success' => false, 'message' => 'Не удалось авторизоваться']);
}

