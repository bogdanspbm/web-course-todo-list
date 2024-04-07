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
                <select name="priority" id="priority" onchange="this.style.color = '#242424';" style="width: 96px;">
                    <option value="" disabled selected>Приоритет</option>
                    <option>Низкий</option>
                    <option>Средний</option>
                    <option>Высокий</option>
                </select>
                <div class="custom-color-picker" style="width: 64px;">
                    <button type="button" name="color-display" id="color-display">Цвет</button>
                    <input onchange="document.getElementById('color-display').style.background = this.value;" name="color" id="color" type="color">
                </div>
                <input id="cancel" style="margin-left: auto;" class="button-secondary" type="reset" value="Отмена">
                <input id="submit" class="button-primary" type="submit" value="Добавить задачу">
            </div>
        </form>
        <?php
        foreach ($tasks as $date => $value) {

            if (!isset($dates[$date])) {
                $dates[$date] = true;
                echo "<h2>" . date("d-m-Y", $date) . "</h2>";
            }
            for ($i = 0; $i < count($value); $i++) {

                $task = $value[$i];
                ?>
                <div class="horizontal-container" style="align-items: center; gap: 8px">
                    <div class="checkbox-wrapper">
                        <div class="round">
                            <input autocomplete="off" type="checkbox" id="<?php echo "checkbox" . $task['uid'] ?>"/>
                            <label for="<?php echo "checkbox" . $task['uid'] ?>"></label>
                        </div>
                    </div>
                    <div class="new-task-horizontal">
                        <div class="new-task-container">
                            <input readonly name="title" class="title-input invisible-input" type="text"
                                   value="<?php echo $task['title']; ?>"
                                   placeholder="Название задачи">
                            <textarea readonly name="desc" maxLength="200" style="height: 14px"
                                      oninput='this.style.height = (this.value.split("\n").length * 14) + "px"'
                                      class="desc-input invisible-input"
                                      placeholder="Описание"><?php echo $task['description']; ?></textarea>
                            <input readonly name="task-uid" type="hidden" value="none">
                        </div>
                        <div class="color-flag" style="background: <?php echo $task['color']; ?>"></div>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>
</div>
</body>
<script type="module" src="../../scripts/home.js"></script>
</html>