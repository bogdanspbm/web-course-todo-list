<!DOCTYPE html>
<html lang="en">
<style>
    @import "../../styles/styles.css";
    @import "../../styles/navigation.css";
    @import "../../styles/container.css";
    @import "../../styles/auth.css";
</style>
<head>
    <meta charset="UTF-8">
    <title>Todo: Авторизация</title>
</head>
<body>
<div class="navigation">
</div>
<div class="container-wrapper">
    <div class="container" style="max-width: 480px;">
        <h2>Вход в аккаунт</h2>
        <a class="change-page-button" href="/register"><img alt="Войти" class="nav-icon" src="../../resources/icons/ic_arrow_left_alt_24x24.svg">
            Регистрация
        </a>
        <form action="/api/firebase/rest/auth.php" method="POST" id="registrationForm">
            <div class="vertical-container" style="gap: 4px">
                <label for="email">Почта:</label>
                <input placeholder="Введите почту" type="email" id="email" name="email" required>
                <sup id="email-sup">Почта уже занята</sup>
            </div>
            <div class="vertical-container" style="gap: 4px">
                <label for="password">Пароль:</label>
                <input placeholder="Введите пароль" type="password" id="password" name="password" required>
                <sup id="pass-sup">Пароль должен состоять минимум из 8 символов</sup>
            </div>
            <div></div>
            <button id="submit" type="submit">Войти</button>
        </form>
    </div>
</div>
</body>
<script type="module" src="../../scripts/login.js"></script>
</html>