<?php


header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json'); // Указываем формат ответа

if(!isset($_GET["uid"])){
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'UID is required']);
    exit;
}