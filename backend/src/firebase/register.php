<?php

require_once '../credentials/firebase_credentials.inc';
require_once '../credentials/redis_credentials.inc';

$token = FIREBASE_TOKEN;
$url = "https://identitytoolkit.googleapis.com/v1/accounts:signUp?key={$token}";

header('Access-Control-Allow-Origin: *');


// Теперь данные формы доступны в $_POST массиве
if (!isset($_POST['email']) || !isset($_POST['password'])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Email and password are required']);
    exit;
}

// Проверка на совпадение паролей, если это требуется
if ($_POST['password'] !== $_POST['confirmPassword']) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Passwords do not match']);
    exit;
}

$firebaseRequestBody = json_encode([
    'email' => $_POST['email'],
    'password' => $_POST['password'],
    'returnSecureToken' => true
]);

$response = json_decode(makeFirebaseRequest($url, $firebaseRequestBody), true);

if (isset($response['email'])) {
    $redis = new Redis();
    $redis->connect(REDIS_HOST, REDIS_PORT);
    $redis->auth(REDIS_PASSWORD);

    // Добавляем email пользователя в набор зарегистрированных пользователей
    $redis->sAdd('registered_users', $response['email']);

    $redis->close();

    // Устанавливаем cookies. Здесь вы можете настроить время жизни cookies по своему усмотрению.
    setcookie('idToken', $response['idToken'], time() + (7 * 86400 * 30), "/"); // 86400 = 1 день
    header('Location: /home');
    exit;
} else {
    // Возвращаем ошибку Firebase, если регистрация не удалась
    echo json_encode($response);
}

function makeFirebaseRequest($url, $payload)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}
