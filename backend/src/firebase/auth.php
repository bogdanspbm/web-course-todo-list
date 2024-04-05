// login.php
<?php
require_once '../credentials/firebase_credentials.inc';
require_once '../credentials/redis_credentials.inc';

$apiKey = FIREBASE_TOKEN;
$url = "https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key={$apiKey}";

$requestBody = file_get_contents('php://input');
$requestData = json_decode($requestBody, true);

header('Access-Control-Allow-Origin: *');


if (!isset($requestData['email']) || !isset($requestData['password'])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Email and password are required']);
    exit;
}

$firebaseRequestBody = json_encode([
    'email' => $requestData['email'],
    'password' => $requestData['password'],
    'returnSecureToken' => true
]);

$response = json_decode(makeFirebaseRequest($url, $firebaseRequestBody), true);

if (isset($response['email'])) {
    $redis = new Redis();
    $redis->connect(REDIS_HOST, REDIS_PORT);
    $redis->auth(REDIS_PASSWORD);

    // Записываем время последнего входа пользователя
    $redis->set('user_last_login:'.$response['email'], time());

    $redis->close();
    echo json_encode(['success' => true, 'message' => 'Login successful']);
} else {
    // Возвращаем ошибку Firebase, если вход не удался
    echo json_encode($response);
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
