<?php
require_once 'api/firebase/lib/check_token.inc';

// Обработка запросов к защищенным и публичным страницам
$basePath = __DIR__ . '/pages';

// Получаем токен из cookies
$token = isset($_COOKIE['idToken']) ? $_COOKIE['idToken'] : '';

// Определяем текущий запрошенный путь и query строку
$requestUri = $_SERVER['REQUEST_URI'];
$queryString = $_SERVER['QUERY_STRING'];

// Извлекаем путь без query параметров
$requestPath = explode('?', $requestUri, 2)[0];
$requestPath = trim($requestPath, '/');

// Путь для перенаправления, если токен не валиден
$redirectPathWhenTokenInvalid = '/login'; // или '/login.php', если нужно

// Путь для перенаправления, если токен валиден, но пытаются доступиться к регистрации/входу
$redirectPathWhenTokenValid = '/home';

// Список путей, доступ к которым разрешен только при наличии валидного токена
$protectedPaths = ['home']; // Можно добавить другие защищенные пути

// Список путей, доступных только без валидного токена
$publicPaths = ['register', 'login'];

// Проверяем токен
$isTokenValid = isTokenValid($token);

if (!$isTokenValid && !in_array($requestPath, $publicPaths)) {
    // Если токен недействителен и путь не один из публичных, редиректим на страницу входа/регистрации
    header("Location: $redirectPathWhenTokenInvalid" . ($queryString ? "?$queryString" : ""));
    exit;
} elseif ($isTokenValid && in_array($requestPath, $publicPaths)) {
    // Если токен валиден, но пытаемся получить доступ к регистрации или входу, редиректим на home
    header("Location: $redirectPathWhenTokenValid" . ($queryString ? "?$queryString" : ""));
    exit;
}

$routes = [
    '' => '/home/index.php', // Корень сайта
    'home' => '/home/index.php',
    'register' => '/register/index.php',
    'login' => '/login/index.php',
    // Добавьте другие маршруты здесь
];

if (array_key_exists($requestPath, $routes)) {
    $includePath = $basePath . $routes[$requestPath];
    if (file_exists($includePath)) {
        include($includePath);
    } else {
        header("HTTP/1.1 404 Not Found");
        echo "<h1>404 Page Not Found</h1>";
    }
} else {
    header("HTTP/1.1 404 Not Found");
    echo "<h1>404 Page Not Found</h1>";
}

function isTokenValid($token)
{
    $data = checkToken($token);
    return isset($data['success']) && $data['success'];
}
