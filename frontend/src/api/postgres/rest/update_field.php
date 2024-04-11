<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/dao/tasks.inc';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/firebase/lib/check_token.inc';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: PUT');

// Read data from PUT request
parse_str(file_get_contents("php://input"), $_PUT);

if (!isset($_PUT["uid"])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'error' => 'UID is required']);
    exit;
}

if (!isset($_PUT["key"])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'error' => 'Key is required']);
    exit;
}

$token = isset($_COOKIE['idToken']) ? $_COOKIE['idToken'] : '';
$response = checkToken($token);

if ($response['success']) {
    $email = $response['userDetails']['email']; // Extract user email
    $taskDao = new TasksDao();

    if (!$taskDao->hasTask($email, $_PUT["uid"])) {
        $taskDao->createDraft($_PUT["uid"], $email);
    }

    switch ($_PUT["key"]) {
        case 'title':
            $taskDao->updateTitle($_PUT["value"], $_PUT["uid"], $email);
            break;
        case 'description':
            $taskDao->updateDescription($_PUT["value"], $_PUT["uid"], $email);
            break;
        case 'color':
            $taskDao->updateColor($_PUT["value"], $_PUT["uid"], $email);
            break;
        case 'task_date':
            $taskDao->updateDate($_PUT["value"], $_PUT["uid"], $email);
            break;
        case 'priority':
            $taskDao->updatePriority($_PUT["value"], $_PUT["uid"], $email);
            break;
    }

    echo json_encode(['success' => true, 'message' => 'Field successfully updated', 'key' => $_PUT["key"], 'value' => $_PUT["value"]]);
} else {
    header('HTTP/1.0 401 Unauthorized');
    echo json_encode(['success' => false, 'message' => 'Authentication failed']);
}

?>
