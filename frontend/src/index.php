<?php

// Предполагаем, что функция isTokenValid($token) уже определена

// Получаем токен из cookies
$token = isset($_COOKIE['idToken']) ? $_COOKIE['idToken'] : '';

// Определяем текущий запрошенный путь
$requestPath = trim($_SERVER['REQUEST_URI'], '/');

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
    header("Location: $redirectPathWhenTokenInvalid");
    exit;
} elseif ($isTokenValid && in_array($requestPath, $publicPaths)) {
    // Если токен валиден, но пытаемся получить доступ к регистрации или входу, редиректим на home
    header("Location: $redirectPathWhenTokenValid");
    exit;
}

// Обработка запросов к защищенным и публичным страницам
$basePath = __DIR__ . '/pages';
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

function isTokenValid($token) {
    $url = "http://185.47.54.162/firebase/check_token.php?token=" . urlencode($token);
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    return isset($data['success']) && $data['success'];
}
