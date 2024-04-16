<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/credentials/firebase_credentials.inc';

$tokenCache = [];

function checkToken($token)
{

    if (isset($tokenCache[$token])) {
        return array('success' => 1, 'message' => 'User is authenticated', 'userDetails' => $response['users'][0]);
    }

    $url = "https://identitytoolkit.googleapis.com/v1/accounts:lookup?key=" . FIREBASE_TOKEN;

    $firebaseRequestBody = json_encode(['idToken' => $token]);

    $response = json_decode(makeFirebaseRequest($url, $firebaseRequestBody), true);

    if (isset($response['users'])) {
        // Возвращаем положительный ответ
        $tokenCache[$token] = $response;
        return array('success' => 1, 'message' => 'User is authenticated', 'userDetails' => $response['users'][0]);
    } else {
        // Возвращаем ошибку авторизации, если токен недействителен
        return array('success' => 0, 'error' => 'Unauthorized');
    }
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
