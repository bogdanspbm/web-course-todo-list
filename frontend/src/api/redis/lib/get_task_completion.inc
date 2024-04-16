<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/credentials/redis_credentials.inc';

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/firebase/lib/check_token.inc';


function getTaskCompletion($uid)
{
    $redis = new Redis();
    $redis->connect(REDIS_HOST, REDIS_PORT);
    $redis->auth(REDIS_PASSWORD);

    $token = isset($_COOKIE['idToken']) ? $_COOKIE['idToken'] : '';

    $response = checkToken($token);

    if ($response['success']) {
        $email = $response['userDetails']['email']; // Извлекаем email пользователя
        $taskKey = "completion:" . $email . ":" . $uid;

        if(!$redis->exists($taskKey)){
            return ['status' => false, "message" => "Task doesn't exist"];
        }

        return ['status' => $redis->get($taskKey), "message" => 'Value successfully read'];
    }

    return ['status' => false, "message" => "Fail Auth"];
}


