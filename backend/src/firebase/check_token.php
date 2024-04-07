<?php

require_once '../credentials/firebase_credentials.inc';


// Проверяем, передан ли токен как параметр GET-запроса
if (!isset($_GET['token'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Token is required']);
}

$token = $_GET['token'];
$url = "https://identitytoolkit.googleapis.com/v1/accounts:lookup?key=" . FIREBASE_TOKEN;

$firebaseRequestBody = json_encode(['idToken' => $token]);

$response = json_decode(makeFirebaseRequest($url, $firebaseRequestBody), true);

header('Content-Type: application/json');
if (isset($response['users'])) {
    // Возвращаем положительный ответ
    echo json_encode(['success' => true, 'message' => 'User is authenticated', 'userDetails' => $response['users'][0]]);
} else {
    // Возвращаем ошибку авторизации, если токен недействителен
    echo json_encode(['error' => 'Unauthorized']);
}


function makeFirebaseRequest($url, $payload)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}
