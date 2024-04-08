<?php
require_once 'api/postgres/lib/get_task.inc';

if (!isset($_COOKIE['email'])) {
    if (isset($_COOKIE['idToken'])) {
        unset($_COOKIE['idToken']);
        setcookie('idToken', '', time() - 3600, '/'); // empty value and old timestamp
    }

    $path = "/login";
    header("Location: $path");
    exit;
}

if (isset($_GET["uid"])) {
    $task = getUserTask($_GET["uid"]);
} else {
    $task = [];
}

$dates = [];
?>

<!DOCTYPE html>
<html lang="en">
<style>
    @import "../../styles/styles.css";
    @import "../../styles/checkbox.css";
    @import "../../styles/navigation.css";
    @import "../../styles/container.css";
    @import "../../styles/tasks.css";
</style>
<head>
    <meta charset="UTF-8">
    <title>Todo</title>
</head>
<body>
<div class="navigation">
    <div class="profile-header">
        <div class="profile-icon"></div>
        <div><?php echo $_COOKIE['email']; ?></div>
        <a class="logout-button" href="/api/firebase/logout.php"> <img alt="Выйти" class="nav-icon"
                                                                       src="../../resources/icons/ic_logout_24x24.svg"></a>
    </div>
    <nav class="no-select selected"><a class="nav-link" href="/edit">
            <img alt="Создать" class="nav-icon" src="../../resources/icons/ic_calendar_add_on_24x24.svg">
            Добавить задачу
        </a>
    </nav>
    <nav class="no-select"><a class="nav-link" href="/home">
            <img alt="Домой" class="nav-icon" src="../../resources/icons/ic_home_24x24.svg">
            Домой
        </a>
    </nav>
    <nav class="no-select">
        <a class="nav-link" href="/draft">
            <img alt="Сегодня" class="nav-icon" src="../../resources/icons/ic_calendar_today_24x24.svg">
            Черновики
        </a>
    </nav>
</div>
<div class="container-wrapper">
    <div class="container">
        <h2>Редактирование задачи</h2>
        <form id="task-create-form" action="/api/redis/create_task.php" method="POST" class="new-task">
            <div class="new-task-container">
                <input value="<?php echo $task['title']; ?>" name="title" id="title" class="title-input invisible-input"
                       type="text"
                       placeholder="Название задачи">
                <textarea name="desc" id="desc" maxLength="200" style="height: 14px"
                          oninput='this.style.height = (this.value.split("\n").length * 14) + "px"'
                          class="desc-input invisible-input" placeholder="Описание"><?php echo $task['description']; ?></textarea>
                <input name="task-uid" type="hidden" id="task-uid" value="">
            </div>
            <div class="new-task-footer">
                <input value="<?php echo date("Y-m-d", $task['task_date_long']); ?>" name="task-date" id="task-date" onchange="this.style.color = '#242424';" style="width: 128px;"
                       onclick="this.showPicker()"
                       type="date">
                <select name="priority" id="priority" onchange="this.style.color = '#242424';" style="width: 96px;">
                    <option value="" disabled <?php if(!isset($task['priority']) || $task['priority'] == "") echo "selected"; ?>>Приоритет</option>
                    <option <?php if(isset($task['priority']) || $task['priority'] == "Низкий") echo "selected"; ?>>Низкий</option>
                    <option <?php if(isset($task['priority']) || $task['priority'] == "Средний") echo "selected"; ?>>Средний</option>
                    <option <?php if(isset($task['priority']) || $task['priority'] == "Высокий") echo "selected"; ?>>Высокий</option>
                </select>
                <div class="custom-color-picker" style="width: 64px;">
                    <button type="button" name="color-display" id="color-display" style="background: <?php echo $task['color']; ?>">Цвет</button>
                    <input value="<?php echo $task['color']; ?>"
                           onchange="document.getElementById('color-display').style.background = this.value;
                                     document.getElementById('color-display').style.color = document.getTextColorFromBG(this.value);"
                           name="color" id="color" type="color">
                </div>
                <a class="cancel-button" href="/home">Отмена</a>
                <input id="submit" class="button-primary" type="submit" value="Добавить задачу">
            </div>
        </form>
    </div>
</div>
</body>
<script type="module" src="../../scripts/edit.js"></script>
</html>