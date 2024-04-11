<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/postgres/lib/get_tasks.inc';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/redis/lib/get_task_completion.inc';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/libs/date_lib.inc';

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
        <a class="logout-button" href="/api/firebase/redirect/logout.php"> <img alt="Выйти" class="nav-icon"
                                                                                src="../../resources/icons/ic_logout_24x24.svg"></a>
    </div>
    <nav class="no-select"><a class="nav-link" href="/edit">
            <img alt="Создать" class="nav-icon" src="../../resources/icons/ic_calendar_add_on_24x24.svg">
            Добавить задачу
        </a>
    </nav>
    <nav class="no-select selected"><a class="nav-link" href="/home">
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
        <h2>Список задач</h2>
        <div class="add-task-button"><img alt="Создать" class="nav-icon" src="../../resources/icons/ic_add_24x24.svg">
            Добавить задачу
        </div>
        <form enctype="multipart/form-data" id="task-create-form" action="/api/postgres/redirect/create_task.php"
              method="POST" style="display: none;"
              class="new-task">
            <div class="new-task-container">
                <div class="horizontal-container">
                    <input onfocusout="document.setInputValue(this, 'title');" value="<?php echo $task['title']; ?>"
                           name="title" id="title" class="title-input invisible-input"
                           type="text"
                           placeholder="Название задачи">
                    <div style="display: none" class="loader" id="progress-loader"></div>
                    <img style="display: none" alt="Успешное сохранение"
                         src="../../resources/icons/ic_cloud_done_24x24.svg"
                         class="loader-success" id="progress-loader-success">
                    <img style="display: none" alt="Ошибка сохранения" src="../../resources/icons/ic_failed_24x24.svg"
                         class="loader-failed"
                         id="progress-loader-failed">
                </div>
                <div class="horizontal-container" style="height: fit-content">
                <textarea style="width: 100%; height: 14px;" name="desc" id="desc" maxLength="200" style="height: 14px"
                          oninput='this.style.height = (this.value.split("\n").length * 14) + "px"'
                          class="desc-input invisible-input" placeholder="Описание"></textarea>
                    <div class="file-container">
                        <div id="upload-file-button" class="upload-file-button"
                             onclick="document.getElementById('file-input').click()">+ Добавить файл 
                        </div>
                        <input id="file-input" hidden="hidden" type="file" name="file"
                               onchange="document.uploadFile(this)">
                    </div>
                </div>
                <input name="task-uid" type="hidden" id="task-uid" value="none">
            </div>
            <div class="new-task-footer">
                <input name="task-date" id="task-date"
                       onchange="document.setInputValue(this, 'task-date'); this.style.color = '#242424';"
                       style="width: 128px;"
                       onclick="this.showPicker()"
                       type="date">
                <select name="priority" id="priority"
                        onchange="document.setInputValue(this, 'priority'); this.style.color = '#242424';"
                        style="width: 96px;">
                    <option value="" disabled selected>Приоритет</option>
                    <option>Низкий</option>
                    <option>Средний</option>
                    <option>Высокий</option>
                </select>
                <div class="custom-color-picker" style="width: 64px;">
                    <button type="button" name="color-display" id="color-display">Цвет</button>
                    <input value="#F0F0F0"
                           onchange="document.setInputValue(this, 'color');
                                     document.getElementById('color-display').style.background = this.value;
                                     document.getElementById('color-display').style.color = document.getTextColorFromBG(this.value);"
                           name="color" id="color" type="color">
                </div>
                <input id="cancel" style="margin-left: auto;" class="button-secondary" type="reset" value="Отмена">
                <input id="submit" class="button-primary" type="submit" value="Добавить задачу">
            </div>
        </form>
        <?php
        foreach ($tasks as $date => $value) {

            if (!isset($dates[$date])) {
                $dates[$date] = true;
                echo "<h2 id='date-$date'>" . format_date_lite($date) . "</h2>";
            }
            for ($i = 0; $i < count($value); $i++) {
                $task = $value[$i];
                $statusResult = getTaskCompletion($task['uid']);
                ?>
                <div id="container-<?php echo $task['uid']; ?>" class="date-<?php echo $date; ?> horizontal-container"
                     style="height: fit-content; max-height: 500px; overflow-y: hidden; transition: max-height 0.5s; align-items: center; gap: 8px">
                    <div class="checkbox-wrapper">
                        <div class="round">
                            <input <?php echo ($statusResult['status'] == 1 || $statusResult['status'] == '1') ? 'checked="true"' : "" ?>
                                    onclick="document.setTaskStatus(this, '<?php echo $task['uid']; ?>').then();"
                                    autocomplete="off" type="checkbox" id="<?php echo "checkbox" . $task['uid'] ?>"/>
                            <label for="<?php echo "checkbox" . $task['uid'] ?>"></label>
                        </div>
                    </div>
                    <div class="new-task-horizontal">
                        <div class="color-flag"
                             style="border: 1px solid <?php echo $task['color'] != "" && isset($task['color']) ? $task['color'] : "#FFFFFF"; ?>; background: <?php echo $task['color'] != "" && isset($task['color']) ? $task['color'] : "#FFFFFF"; ?> ;border-right-color: #F0F0F0;"></div>
                        <div class="new-task-container">
                            <input readonly name="title" class="title-input invisible-input" type="text"
                                   value="<?php echo $task['title']; ?>"
                                   placeholder="Название задачи">
                            <textarea readonly name="desc" maxLength="200" style="height: 14px"
                                      oninput='this.style.height = (this.value.split("\n").length * 14) + "px"'
                                      class="desc-input invisible-input"
                                      placeholder="Описание"><?php echo $task['description']; ?></textarea>
                            <input readonly type="hidden" value="none">
                        </div>
                        <div class="task-control" id="task-control-<?php echo $task['uid']; ?>">
                            <div class="task-control-button"
                                 onclick="document.deleteTask('<?php echo $task['uid']; ?>')"><img alt="Удалить"
                                                                                                   class="nav-icon"
                                                                                                   src="resources/icons/ic_delete_24x24.svg">
                            </div>
                        </div>
                        <div style="display: none" class="loader-gray"
                             id="progress-loader-<?php echo $task['uid']; ?>"></div>
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