<?php
require_once '../credentials/firebase_credentials.inc';
require_once '../credentials/redis_credentials.inc';

$apiKey = FIREBASE_TOKEN;
$url = "https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key={$apiKey}";

$requestBody = file_get_contents('php://input');
$requestData = json_decode($requestBody, true);

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json'); // Указываем формат ответа

// Теперь данные формы доступны в $_POST массиве
if (!isset($_POST['email']) || !isset($_POST['password'])) {
    header('HTTP/1.1 400 Bad Request');
    $path = "/login";
    header("Location: $path");
}

$firebaseRequestBody = json_encode([
    'email' => $_POST['email'],
    'password' => $_POST['password'],
    'returnSecureToken' => true
]);

$response = json_decode(makeFirebaseRequest($url, $firebaseRequestBody), true);

if (isset($response['idToken'])) { // Проверяем наличие idToken в ответе
    $redis = new Redis();
    $redis->connect(REDIS_HOST, REDIS_PORT);
    $redis->auth(REDIS_PASSWORD);

    // Записываем время последнего входа пользователя
    $redis->set('user_last_login:'.$response['email'], time());

    $redis->close();

    // Установка cookies с токеном. Обратите внимание, что время жизни cookie нужно настроить в соответствии с вашими требованиями
    setcookie('idToken', $response['idToken'], time() + (7 * 86400 * 30), "/"); // 86400 = 1 день
    setcookie('email', $response['email'], time() + (7 * 86400 * 30), "/"); // 86400 = 1 день

    $path = "/home";
    header("Location: $path");
} else {
    http_response_code(401); // Настройка кода ответа в случае ошибки авторизации
    $path = "/login";
    header("Location: $path");
}

function makeFirebaseRequest($url, $payload) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}
