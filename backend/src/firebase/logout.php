<?php
if (isset($_COOKIE['idToken'])) {
    unset($_COOKIE['idToken']);
    setcookie('idToken', '', time() - 3600, '/'); // empty value and old timestamp
}

if (isset($_COOKIE['email'])) {
    unset($_COOKIE['email']);
    setcookie('email', '', time() - 3600, '/'); // empty value and old timestamp
}

$path = "/login";
header("Location: $path");