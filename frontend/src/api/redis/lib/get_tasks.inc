<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/credentials/redis_credentials.inc';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/firebase/lib/check_token.inc';

function getUserTasks()
{
    $redis = new Redis();
    $redis->connect(REDIS_HOST, REDIS_PORT);
    $redis->auth(REDIS_PASSWORD);

    // Получаем токен из cookies
    $token = isset($_COOKIE['idToken']) ? $_COOKIE['idToken'] : '';
    $response = checkToken($token);
    $tasksByDate = []; // Массив для группировки задач по датам

    if ($response['success']) {
        $email = $response['userDetails']['email']; // Извлекаем email пользователя
        $taskKey = "tasks:" . $email;
        $tasks = json_decode($redis->get($taskKey), true);
        if ($tasks) {
            // Группируем задачи по датам
            foreach ($tasks as $task) {
                $date = $task['date'];
                if (!isset($tasksByDate[$date])) {
                    $tasksByDate[$date] = [];
                }
                $tasksByDate[$date][] = $task;
            }

            // Отсортировать задачи внутри каждой даты по возрастанию (если требуется)
            foreach ($tasksByDate as &$tasks) {
                usort($tasks, function ($a, $b) {
                    return $a['date'] <=> $b['date'];
                });
            }

            // Отсортировать группы задач по датам
            ksort($tasksByDate);
        }
    }

    $redis->close();

    // Возвращаем задачи, сгруппированные по датам и отсортированные
    return $tasksByDate;
}
?>
