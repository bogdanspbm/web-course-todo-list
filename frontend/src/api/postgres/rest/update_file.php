<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/dao/tasks.inc';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/firebase/lib/check_token.inc';


header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: PUT');


$token = isset($_COOKIE['idToken']) ? $_COOKIE['idToken'] : '';
$response = checkToken($token);

if (!$response['success']) {
    header('HTTP/1.0 401 Unauthorized');
    echo json_encode(['success' => false, 'message' => 'Authentication failed']);
    exit;
}

$errorDetails = [];

if (!isset($_POST['uid'])) {
    $errorDetails['uid'] = $_POST['uid'] . 'UID is required';
}

if (!isset($_FILES['file'])) {
    $errorDetails['file'] = 'File is required';
} elseif ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    $errorDetails['file'] = 'Error uploading file: ' . $_FILES['file']['error'];
}

if (!empty($errorDetails)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'error' => 'Missing or invalid parameters', 'details' => $errorDetails]);
    exit;
}

$email = $response['userDetails']['email'];
$tasksDao = new TasksDao();
$uid = $_POST['uid'];
$file = $_FILES['file'];
$fileName = $file['name'];
$fileData = file_get_contents($file['tmp_name']);


if (!$tasksDao->hasTask($email, $uid)) {
    $tasksDao->createDraft($uid, $email);
}

$tasksDao->updateFile($uid, $email, $fileName, $fileData);

echo json_encode(['success' => true, 'message' => 'File successfully updated']);
?>
