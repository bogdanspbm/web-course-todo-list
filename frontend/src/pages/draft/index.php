<?php
require_once 'api/postgres/lib/get_drafts.inc';
require_once 'api/libs/date_lib.inc';

if (!isset($_COOKIE['email'])) {
    if (isset($_COOKIE['idToken'])) {
        unset($_COOKIE['idToken']);
        setcookie('idToken', '', time() - 3600, '/'); // empty value and old timestamp
    }

    $path = "/login";
    header("Location: $path");
    exit;
}

$tasks = getUserDrafts();
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
    <nav class="no-select"><a class="nav-link" href="/home">
            <img alt="Домой" class="nav-icon" src="../../resources/icons/ic_home_24x24.svg">
            Домой
        </a>
    </nav>
    <nav class="no-select selected">
        <a class="nav-link" href="/draft">
            <img alt="Сегодня" class="nav-icon" src="../../resources/icons/ic_calendar_today_24x24.svg">
            Черновики
        </a>
    </nav>
</div>
<div class="container-wrapper">
    <div class="container">
        <h2>Список черновиков</h2>
        <?php
        foreach ($tasks as $date => $value) {
            for ($i = 0; $i < count($value); $i++) {

                $task = $value[$i];
                ?>
                <div class="horizontal-container" style="align-items: center; gap: 8px">
                    <div class="new-task-horizontal">
                        <div class="color-flag" style="background: <?php echo $task['color']; ?>"></div>
                        <div class="new-task-container">
                            <input readonly name="title" class="title-input invisible-input" type="text"
                                   value="<?php echo $task['title']; ?>"
                                   placeholder="Название задачи">
                            <textarea readonly name="desc" maxLength="200" style="height: 14px"
                                      oninput='this.style.height = (this.value.split("\n").length * 14) + "px"'
                                      class="desc-input invisible-input"
                                      placeholder="Описание"><?php echo $task['description']; ?></textarea>
                            <input readonly name="task-uid" type="hidden" value="<?php echo $task['uid']; ?>">
                        </div>
                        <div class="task-control">
                            <a href="/edit?uid=<?php echo $task['uid']; ?>" class="task-control-button"><img alt="Редактировать" class="nav-icon"
                                                                src="resources/icons/ic_edit_24x24.svg"></a>
                            <a href="/api/postgres/redirect/delete_task.php?uid=<?php echo $task['uid']; ?>"
                               class="task-control-button"><img alt="Удалить" class="nav-icon"
                                                                src="resources/icons/ic_delete_24x24.svg"></a>
                            <div class="task-control-button"><img alt="Опубликовать" class="nav-icon"
                                                                src="resources/icons/ic_bookmark_add_24x24.svg"></div>
                        </div>
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