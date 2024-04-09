<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/credentials/redis_credentials.inc';

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/firebase/check_token.inc';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

if (!isset($_GET["uid"])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'UID is required']);
    exit;
}

$redis = new Redis();
$redis->connect(REDIS_HOST, REDIS_PORT);
$redis->auth(REDIS_PASSWORD);

// Получаем токен из cookies
$token = isset($_COOKIE['idToken']) ? $_COOKIE['idToken'] : '';
$response = checkToken($token);

if ($response['success']) {
    
}

