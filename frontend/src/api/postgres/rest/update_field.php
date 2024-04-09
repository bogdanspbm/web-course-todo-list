<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/dao/tasks.inc';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/firebase/lib/check_token.inc';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

if (!isset($_POST["uid"])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'error' => 'UID is required']);
    exit;
}

if (!isset($_POST["key"])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'error' => 'Key is required']);
    exit;
}


$token = isset($_COOKIE['idToken']) ? $_COOKIE['idToken'] : '';
$response = checkToken($token);

if ($response['success']) {
    $email = $response['userDetails']['email']; // Извлекаем email пользователя
    $taskDao = new TasksDao();

    if (!$taskDao->hasTask($email, $_POST["uid"])) {
        $taskDao->createDraft($_POST["uid"], $email);
    }

    switch ($_POST["key"]) {
        case 'title':
        {
            $taskDao->updateTitle($_POST["value"], $_POST["uid"], $email);
            break;
        }
        case 'description':
        {
            $taskDao->updateDescription($_POST["value"], $_POST["uid"], $email);
            break;
        }
        case 'color':
        {
            $taskDao->updateColor($_POST["value"], $_POST["uid"], $email);
            break;
        }
        case 'task_date':
        {
            $taskDao->updateDate($_POST["value"], $_POST["uid"], $email);
            break;
        }
        case 'priority':
        {
            $taskDao->updatePriotiry($_POST["value"], $_POST["uid"], $email);
            break;
        }
    }

    echo json_encode(['success' => true, 'message' => 'Поле успешно обновлено', 'key' => $_POST["key"], 'value' => $_POST["value"]]);
} else {
    header('HTTP/1.0 401 Unauthorized');
    echo json_encode(['success' => false, 'message' => 'Не удалось авторизоваться']);
}
