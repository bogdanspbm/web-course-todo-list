<?php
require_once '../credentials/firebase_credentials.inc';

$token = FIREBASE_TOKEN;
$url = "https://identitytoolkit.googleapis.com/v1/accounts:signUp?key={$token}";

$requestBody = file_get_contents('php://input');
$requestData = json_decode($requestBody, true);

if (!isset($requestData['email']) || !isset($requestData['password'])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Email and password are required']);
    exit;
}

$firebaseRequestBody = json_encode([
    'email' => $requestData['email'],
    'password' => $requestData['password'],
    'returnSecureToken' => true
]);

$response = makeFirebaseRequest($url, $firebaseRequestBody);
echo $response;

function makeFirebaseRequest($url, $payload) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}
