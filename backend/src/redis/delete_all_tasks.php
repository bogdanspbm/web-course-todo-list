<?php

require_once '../credentials/redis_credentials.inc';
require_once '../firebase/check_token.inc'; // Предполагается, что здесь функция checkToken возвращает массив с информацией о валидности токена и деталями пользователя

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$redis = new Redis();
try {
    $redis->connect(REDIS_HOST, REDIS_PORT);
    $redis->auth(REDIS_PASSWORD);

    // Получаем токен из cookies
    $token = isset($_COOKIE['idToken']) ? $_COOKIE['idToken'] : '';
    $response = checkToken($token);

    if ($response['success']) {
        $email = $response['userDetails']['email']; // Извлекаем email пользователя
        $taskKey = "tasks:" . $email;

        // Проверяем, существуют ли задачи для данного пользователя
        if ($redis->exists($taskKey)) {
            $redis->del($taskKey); // Удаляем все задачи пользователя, удаляя его ключ из Redis
            $path = "/home?code=200";
            header("Location: $path");
        } else {
            $path = "/home?code=401";
            header("Location: $path");
        }
    } else {
        $path = "/home?code=401";
        header("Location: $path");
    }
} catch (Exception $e) {

} finally {
    if ($redis->isConnected()) {
        $redis->close();
    }
}

$path = "/home?code=500";
header("Location: $path");
?>
