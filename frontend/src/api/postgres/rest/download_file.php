<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/dao/tasks.inc';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/firebase/lib/check_token.inc';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$token = isset($_COOKIE['idToken']) ? $_COOKIE['idToken'] : '';
$response = checkToken($token);

if (!$response['success']) {
    header('HTTP/1.0 401 Unauthorized');
    echo json_encode(['success' => false, 'message' => 'Authentication failed']);
    exit;
}

if (!isset($_GET['uid'])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'error' => 'UID is required']);
    exit;
}

$uid = $_GET['uid'];
$email = $response['userDetails']['email'];
$tasksDao = new TasksDao();

$fileData = $tasksDao->getFile($uid, $email);

if (!$fileData) {
    header('HTTP/1.1 404 Not Found');
    echo json_encode(['success' => false, 'message' => 'File not found']);
    exit;
}

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $fileData['file_name'] . '"');
echo $fileData['file_data'];
