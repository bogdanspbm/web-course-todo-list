<?php
?>

<!DOCTYPE html>
<html lang="en">
<style>
    @import "./styles/styles.css";
    @import "./styles/navigation.css";
    @import "./styles/container.css";
    @import "./styles/tasks.css";
</style>
<head>
    <meta charset="UTF-8">
    <title>Todo</title>
</head>
<body>
<div class="navigation">
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
        <form style="display: none;" class="new-task">
            <div class="new-task-container">
                <input id="title" class="title-input invisible-input" type="text" placeholder="Название задачи">
                <textarea id="desc" maxLength="200" style="height: 14px"
                          oninput='this.style.height = (this.value.split("\n").length * 14) + "px"'
                          class="desc-input invisible-input" placeholder="Описание"></textarea>
            </div>
            <div class="new-task-footer">
                <input id="date" onchange="this.style.color = '#242424';" style="width: 128px;"
                       onclick="this.showPicker()"
                       type="date">
                <select id="priority" onchange="this.style.color = '#242424';" style="width: 128px;">
                    <option value="" disabled selected>Приоритет</option>
                    <option>Низкий</option>
                    <option>Средний</option>
                    <option>Высокий</option>
                </select>

                <input id="cancel" style="margin-left: auto;" class="button-secondary" type="reset" value="Отмена">
                <input id="submit" class="button-primary" type="submit" value="Добавить задачу">
            </div>
        </form>
    </div>
</div>
</body>
<script type="module" src="index.js"></script>
</html>