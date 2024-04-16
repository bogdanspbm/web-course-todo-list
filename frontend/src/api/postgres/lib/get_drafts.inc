<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/dao/tasks.inc';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/firebase/lib/check_token.inc';
function getUserDrafts()
{
    $taskDao = new TasksDao();

    // Получаем токен из cookies
    $token = isset($_COOKIE['idToken']) ? $_COOKIE['idToken'] : '';
    $response = checkToken($token);
    $tasksByDate = []; // Массив для группировки задач по датам

    if ($response['success']) {
        $email = $response['userDetails']['email']; // Извлекаем email пользователя
        $tasks = $taskDao->getDraft($email);

        for ($i = 0; $i < count($tasks); $i++) {
            $task = $tasks[$i];

            if (!isset($tasksByDate[(int)$task['task_date_long']])) {
                $tasksByDate[(int)$task['task_date_long']] = [];
            }

            $tasksByDate[(int)$task['task_date_long']][] = $task;
        }
    } else {
        header('HTTP/1.0 401 Unauthorized');
    }

    return $tasksByDate;
}