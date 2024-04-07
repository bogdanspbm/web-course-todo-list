<?php
require_once 'api/redis/get_tasks.inc';

if (!isset($_COOKIE['email'])) {
    if (isset($_COOKIE['idToken'])) {
        unset($_COOKIE['idToken']);
        setcookie('idToken', '', time() - 3600, '/'); // empty value and old timestamp
    }

    $path = "/login";
    header("Location: $path");
    exit;
}

$tasks = getUserTasks();
?>

<!DOCTYPE html>
<html lang="en">
<style>
    @import "../../styles/styles.css";
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
    <nav class="no-select"><img alt="Создать" class="nav-icon" src="../../resources/icons/ic_calendar_add_on_24x24.svg">
        Добавить задачу
    </nav>
    <nav class="no-select selected"><img alt="Домой" class="nav-icon" src="../../resources/icons/ic_home_24x24.svg">
        Домой
    </nav>
    <nav class="no-select"><img alt="Сегодня" class="nav-icon" src="../../resources/icons/ic_calendar_today_24x24.svg">
        Черновики
    </nav>
</div>
<div class="container-wrapper">
    <div class="container">
        <h2>Список задач</h2>
        <div class="add-task-button"><img alt="Создать" class="nav-icon" src="../../resources/icons/ic_add_24x24.svg">
            Добавить задачу
        </div>
        <form id="task-create-form" action="/api/redis/create_task.php" method="POST" style="display: none;"
              class="new-task">
            <div class="new-task-container">
                <input name="title" id="title" class="title-input invisible-input" type="text"
                       placeholder="Название задачи">
                <textarea name="desc" id="desc" maxLength="200" style="height: 14px"
                          oninput='this.style.height = (this.value.split("\n").length * 14) + "px"'
                          class="desc-input invisible-input" placeholder="Описание"></textarea>
                <input name="task-uid" type="hidden" id="task-uid" value="none">
            </div>
            <div class="new-task-footer">
                <input name="date" id="date" onchange="this.style.color = '#242424';" style="width: 128px;"
                       onclick="this.showPicker()"
                       type="date">
                <select name="priority" id="priority" onchange="this.style.color = '#242424';" style="width: 128px;">
                    <option value="" disabled selected>Приоритет</option>
                    <option>Низкий</option>
                    <option>Средний</option>
                    <option>Высокий</option>
                </select>

                <input id="cancel" style="margin-left: auto;" class="button-secondary" type="reset" value="Отмена">
                <input id="submit" class="button-primary" type="submit" value="Добавить задачу">
            </div>
        </form>
        <?php
        foreach ($tasks as $key => $value) {
            ?>
            <div class="new-task">
                <div class="new-task-container">
                    <input readonly name="title" class="title-input invisible-input" type="text"
                           value="<?php echo $value['title']; ?>"
                           placeholder="Название задачи">
                    <textarea readonly name="desc" maxLength="200" style="height: 14px"
                              value="<?php echo $value['description']; ?>"
                              oninput='this.style.height = (this.value.split("\n").length * 14) + "px"'
                              class="desc-input invisible-input" placeholder="Описание"></textarea>
                    <input readonly name="task-uid" type="hidden" value="none">
                </div>
                <div class="new-task-footer">
                    <input class="date-view" readonly name="date" onchange="this.style.color = '#242424';" style="width: 128px;"
                           value="<?php echo date("d-m-Y", $value['date']); ?>"
                           type="text">
                    <input class="select-view" readonly name="priority" onchange="this.style.color = '#242424';" style="width: 128px;"
                           value="<?php echo $value['priority']; ?>"
                           type="text">
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>
</body>
<script type="module" src="../../scripts/home.js"></script>
</html>