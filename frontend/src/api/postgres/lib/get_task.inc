<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/dao/tasks.inc';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/firebase/lib/check_token.inc';
function getUserTask($uid)
{
    $taskDao = new TasksDao();

    // Получаем токен из cookies
    $token = isset($_COOKIE['idToken']) ? $_COOKIE['idToken'] : '';
    $response = checkToken($token);

    if ($response['success']) {
        $email = $response['userDetails']['email']; // Извлекаем email пользователя
        $task = $taskDao->getTask($email, $uid);
        return $task;
    }

    return [];
}