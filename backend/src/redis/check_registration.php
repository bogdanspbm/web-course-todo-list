// check_registration.php
<?php
require_once '../credentials/redis_credentials.inc';

// Получаем email пользователя из запроса
if (!isset($_GET['email'])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Email is required']);
    exit;
}
$email = $_GET['email'];

$redis = new Redis();
$redis->connect(REDIS_HOST, REDIS_PORT);
$redis->auth(REDIS_PASSWORD);

// Проверяем, есть ли email в наборе зарегистрированных пользователей
$isRegistered = $redis->sIsMember('registered_users', $email);

$redis->close();

if ($isRegistered) {
    echo json_encode(['registered' => true, 'message' => 'User is registered']);
} else {
    echo json_encode(['registered' => false, 'message' => 'User is not registered']);
}
